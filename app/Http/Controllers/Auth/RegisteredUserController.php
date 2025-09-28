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

        if (!$verifiedEmail) {
            return redirect()->route('ms365.verify')
                             ->with('error', 'Please verify your McLawis College email first.');
        }

        // Verify OTP record is valid (not expired, and verified within 1 hour)
        $otpRecord = OtpVerification::where('email', $verifiedEmail)
                                    ->whereNotNull('verified_at')
                                    ->where('created_at', '>=', Carbon::now()->subHours(1))
                                    ->first();

        if (!$otpRecord) {
            return redirect()->route('ms365.verify')
                             ->with('error', 'Email verification has expired. Please verify again.');
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

        if (!$verifiedEmail) {
            return redirect()->route('ms365.verify')
                             ->with('error', 'Please verify your McLawis College email first.');
        }

        // Verify OTP record is still valid
        $otpRecord = OtpVerification::where('email', $verifiedEmail)
                                    ->whereNotNull('verified_at')
                                    ->where('created_at', '>=', Carbon::now()->subHours(1))
                                    ->first();

        if (!$otpRecord) {
            return redirect()->route('ms365.verify')
                             ->with('error', 'Email verification has expired. Please verify again.');
        }

        $request->validate([
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
        ]);

        $user = User::create([
            'first_name'        => $request->first_name,
            'middle_name'       => $request->middle_name,
            'last_name'         => $request->last_name,
            'email'             => $verifiedEmail, // force verified email
            'department'        => $request->department,
            'password'          => Hash::make($request->password),
            'role'              => 'user',
            'status'            => 'active',
            'email_verified_at' => now(),
        ]);

        // Remove OTP record after successful registration
        $otpRecord->delete();

        event(new Registered($user));

        Auth::login($user);

        // Clear session data
        session()->forget(['verified_email', 'email']);

        return redirect(route('dashboard', absolute: false));
    }
}
