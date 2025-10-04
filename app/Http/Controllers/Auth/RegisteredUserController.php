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
        // Retrieve verified email and OTP status from session
        $verifiedEmail = session('verified_email');
        $otpVerified = session('otp_verified');

        Log::info('Registration page accessed', [
            'verified_email' => $verifiedEmail,
            'otp_verified' => $otpVerified,
            'session_data' => session()->all(),
        ]);

        // Redirect if email or OTP not verified
        if (!$verifiedEmail || !$otpVerified) {
            Log::warning('Registration access denied - email not verified');
            return redirect()->route('ms365.verify')
                             ->withErrors(['email' => 'Please verify your McLawis College email first.']);
        }

        // Check OTP verification record (verified within last 60 minutes)
        $otpRecord = OtpVerification::where('email', $verifiedEmail)
            ->whereNotNull('verified_at')
            ->where('verified_at', '>=', Carbon::now()->subHour())
            ->first();

        if (!$otpRecord) {
            Log::warning('OTP verification expired or not found', ['email' => $verifiedEmail]);

            // Clear verification-related session keys
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

        Log::info('Registration attempt', [
            'verified_email' => $verifiedEmail,
            'otp_verified' => $otpVerified,
            'request_email' => $request->email,
            'request_data' => $request->except('password', 'password_confirmation'),
        ]);

        if (!$verifiedEmail || !$otpVerified) {
            Log::warning('Registration denied - email not verified in session');
            return redirect()->route('ms365.verify')
                             ->withErrors(['email' => 'Please verify your McLawis College email first.']);
        }

        // Check if OTP verification is still valid
        $otpRecord = OtpVerification::where('email', $verifiedEmail)
            ->whereNotNull('verified_at')
            ->where('verified_at', '>=', Carbon::now()->subHour())
            ->first();

        if (!$otpRecord) {
            Log::warning('OTP record expired during registration', ['email' => $verifiedEmail]);

            session()->forget(['verified_email', 'email', 'otp_verified', 'email_verified']);

            return redirect()->route('ms365.verify')
                             ->withErrors(['email' => 'Email verification has expired. Please verify again.']);
        }

        // Validate registration input
        $validated = $request->validate([
            'id_number'    => ['required', 'string', 'max:255', 'unique:' . User::class],
            'first_name'   => ['required', 'string', 'max:255'],
            'middle_name'  => ['nullable', 'string', 'max:255'],
            'last_name'    => ['required', 'string', 'max:255'],
            'email'        => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:' . User::class,
                function ($attribute, $value, $fail) use ($verifiedEmail) {
                    if (strtolower($value) !== strtolower($verifiedEmail)) {
                        $fail('The email must match your verified McLawis College email.');
                    }
                },
            ],
            'department'   => ['required', 'string', 'in:BSIT,BSBA,BSED,BEED,BSHM'],
            'password'     => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        try {
            // Create the user with the verified email (forced)
            $user = User::create([
                'id_number'         => $validated['id_number'],
                'first_name'        => $validated['first_name'],
                'middle_name'       => $validated['middle_name'] ?? null,
                'last_name'         => $validated['last_name'],
                'email'             => $verifiedEmail,
                'department'        => $validated['department'],
                'password'          => Hash::make($validated['password']),
                'role'              => 'user',
                'status'            => 'active',
                'email_verified_at' => now(),
            ]);

            Log::info('User registered successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            // Delete OTP record after successful registration
            $otpRecord->delete();

            // Fire the Registered event
            event(new Registered($user));

            // Log the user in
            Auth::login($user);

            // Clear verification-related session data
            session()->forget([
                'verified_email',
                'email',
                'otp_verified',
                'email_verified',
                'ms365_verification_started',
            ]);

            return redirect()->route('dashboard')
                             ->with('success', 'Registration completed successfully! Welcome to EventAps.');

        } catch (\Exception $e) {
            Log::error('Registration failed', [
                'error' => $e->getMessage(),
                'email' => $verifiedEmail,
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['error' => 'Registration failed. Please try again.']);
        }
    }
}
