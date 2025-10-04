<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OtpVerification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Carbon\Carbon;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View|RedirectResponse
    {
        // Check if email is verified in session
        $verifiedEmail = session('verified_email');
        $otpVerified = session('otp_verified');

        // Log for debugging
        Log::info('Registration page accessed', [
            'verified_email' => $verifiedEmail,
            'otp_verified' => $otpVerified,
            'all_session' => session()->all()
        ]);

        if (!$verifiedEmail || !$otpVerified) {
            Log::warning('Registration access denied - email not verified');
            return redirect()->route('ms365.verify')
                             ->withErrors(['email' => 'Please verify your McLawis College email first.']);
        }

        // Verify OTP record is valid (not expired, and verified within 1 hour)
        $otpRecord = OtpVerification::where('email', $verifiedEmail)
                                    ->whereNotNull('verified_at')
                                    ->where('verified_at', '>=', Carbon::now()->subHour())
                                    ->first();

        if (!$otpRecord) {
            Log::warning('OTP verification expired or not found', ['email' => $verifiedEmail]);
            
            // Clear invalid session data
            session()->forget(['verified_email', 'email', 'otp_verified', 'email_verified']);
            
            return redirect()->route('ms365.verify')
                             ->withErrors(['email' => 'Email verification has expired. Please verify again.']);
        }

        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $verifiedEmail = session('verified_email');
        $otpVerified = session('otp_verified');

        // Log registration attempt
        Log::info('Registration attempt', [
            'verified_email' => $verifiedEmail,
            'otp_verified' => $otpVerified,
            'request_email' => $request->email
        ]);

        if (!$verifiedEmail || !$otpVerified) {
            Log::warning('Registration denied - email not verified in session');
            return redirect()->route('ms365.verify')
                             ->withErrors(['email' => 'Please verify your McLawis College email first.']);
        }

        // Verify OTP record is still valid
        $otpRecord = OtpVerification::where('email', $verifiedEmail)
                                    ->whereNotNull('verified_at')
                                    ->where('verified_at', '>=', Carbon::now()->subHour())
                                    ->first();

        if (!$otpRecord) {
            Log::warning('OTP record expired during registration', ['email' => $verifiedEmail]);
            
            // Clear session data
            session()->forget(['verified_email', 'email', 'otp_verified', 'email_verified']);
            
            return redirect()->route('ms365.verify')
                             ->withErrors(['email' => 'Email verification has expired. Please verify again.']);
        }

        // Validate registration data
        $validated = $request->validate([
            'id_number'    => ['required', 'string', 'max:255', 'unique:' . User::class],
            'first_name'   => ['required', 'string', 'max:255'],
            'middle_name'  => ['nullable', 'string', 'max:255'],
            'last_name'    => ['required', 'string', 'max:255'],
            'email'        => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:' . User::class,
                function ($attribute, $value, $fail) use ($verifiedEmail) {
                    if ($value !== $verifiedEmail) {
                        $fail('The email must match your verified McLawis College email.');
                    }
                },
            ],
            'department'   => ['required', 'string', 'in:BSIT,BSBA,BSED,BEED,BSHM'],
            'password'     => ['required', 'confirmed', Rules\Password::defaults()],
            'role'         => ['required', 'in:student'],
            'status'       => ['required', 'in:active'],
        ]);

        try {
            // Create the user
            $user = User::create([
                'id_number'         => $validated['id_number'],
                'first_name'        => $validated['first_name'],
                'middle_name'       => $validated['middle_name'],
                'last_name'         => $validated['last_name'],
                'email'             => $verifiedEmail, // Force verified email
                'department'        => $validated['department'],
                'password'          => Hash::make($validated['password']),
                'role'              => $validated['role'],
                'status'            => $validated['status'],
                'email_verified_at' => now(),
            ]);

            Log::info('User registered successfully', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            // Remove OTP record after successful registration
            $otpRecord->delete();

            // Fire registered event
            event(new Registered($user));

            // Log the user in
            Auth::login($user);

            // Clear all verification session data
            session()->forget([
                'verified_email',
                'email',
                'otp_verified',
                'email_verified',
                'ms365_verification_started'
            ]);

            return redirect()->route('dashboard')
                             ->with('success', 'Registration completed successfully! Welcome to EventAps.');

        } catch (\Exception $e) {
            Log::error('Registration failed', [
                'error' => $e->getMessage(),
                'email' => $verifiedEmail
            ]);

            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['error' => 'Registration failed. Please try again.']);
        }
    }
}