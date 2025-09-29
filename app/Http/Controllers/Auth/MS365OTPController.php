<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OtpVerification;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class MS365OTPController extends Controller
{
    public function showMS365Form(): View
    {
        Log::debug('Showing MS365 email verification form');
        return view('auth.ms365-verify');
    }

    public function verifyMS365Account(Request $request): RedirectResponse
    {
        Log::debug('Attempting to verify MS365 email: ' . $request->email);

        $request->validate([
            'email' => [
                'required',
                'email',
                'regex:/^[a-zA-Z0-9._%+-]+@mcclawis\.edu\.ph$/',
                'max:255'
            ],
        ], [
            'email.regex' => 'Please use your official McLawis College email address (@mcclawis.edu.ph)',
        ]);

        // Check if user already exists
        if (User::where('email', $request->email)->exists()) {
            Log::info("Email already registered: " . $request->email);
            return back()->withErrors(['email' => 'This email is already registered.']);
        }

        // Generate OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        Log::debug("Generated OTP for {$request->email}: {$otp}");

        try {
            OtpVerification::updateOrCreate(
                ['email' => $request->email],
                [
                    'otp' => Hash::make($otp),
                    'expires_at' => Carbon::now()->addMinutes(10),
                    'attempts' => 0,
                ]
            );
            Log::info("OTP stored in database for: " . $request->email);
        } catch (\Exception $e) {
            Log::error("Failed to save OTP to database for {$request->email}: " . $e->getMessage());
            return back()->withErrors(['email' => 'Something went wrong while saving OTP.']);
        }

        try {
            Mail::send('emails.otp-verification', ['otp' => $otp, 'email' => $request->email], function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('EventAps - Email Verification Code');
            });
            Log::info("OTP email sent to: " . $request->email);

            return redirect()->route('otp.verify.form')
                            ->with('email', $request->email)
                            ->with('status', 'Verification code sent to your McLawis email address.');
        } catch (\Exception $e) {
            Log::error("Failed to send OTP email to {$request->email}: " . $e->getMessage());
            return back()->withErrors(['email' => 'Failed to send verification code. Please try again.']);
        }
    }

    public function showOTPForm(): View
    {
        if (!session('email')) {
            Log::info("No session email found, redirecting to MS365 verify form.");
            return redirect()->route('ms365.verify');
        }

        Log::debug("Showing OTP verification form for: " . session('email'));
        return view('auth.otp-verify');
    }

    public function verifyOTP(Request $request): RedirectResponse
    {
        Log::debug("Verifying OTP for: " . $request->email);

        $request->validate([
            'otp' => ['required', 'digits:6'],
            'email' => ['required', 'email'],
        ]);

        $otpRecord = OtpVerification::where('email', $request->email)->first();

        if (!$otpRecord) {
            Log::warning("OTP record not found for: " . $request->email);
            return back()->withErrors(['otp' => 'Invalid verification request.']);
        }

        if (Carbon::now()->gt($otpRecord->expires_at)) {
            Log::info("OTP expired for: " . $request->email);
            return back()->withErrors(['otp' => 'Verification code has expired.']);
        }

        if ($otpRecord->attempts >= 3) {
            Log::info("Too many failed OTP attempts for: " . $request->email);
            return back()->withErrors(['otp' => 'Too many failed attempts. Please request a new code.']);
        }

        if (!Hash::check($request->otp, $otpRecord->otp)) {
            $otpRecord->increment('attempts');
            Log::warning("Invalid OTP entered for: " . $request->email . " | Attempt #" . ($otpRecord->attempts + 1));
            return back()->withErrors(['otp' => 'Invalid verification code.']);
        }

        $otpRecord->update(['verified_at' => Carbon::now()]);
        Log::info("OTP successfully verified for: " . $request->email);

        return redirect()->route('register')
                       ->with('verified_email', $request->email)
                       ->with('status', 'Email verified successfully! Please complete your registration.');
    }

    public function resendOTP(Request $request): RedirectResponse
    {
        Log::debug("Resending OTP for: " . $request->email);

        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $otpRecord = OtpVerification::where('email', $request->email)->first();

        if ($otpRecord && Carbon::now()->lt($otpRecord->created_at->addMinutes(2))) {
            Log::info("OTP resend request too soon for: " . $request->email);
            return back()->withErrors(['otp' => 'Please wait before requesting another code.']);
        }

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        Log::debug("Generated new OTP for resend: {$otp}");

        try {
            OtpVerification::updateOrCreate(
                ['email' => $request->email],
                [
                    'otp' => Hash::make($otp),
                    'expires_at' => Carbon::now()->addMinutes(10),
                    'attempts' => 0,
                ]
            );
            Log::info("OTP updated in DB for: " . $request->email);
        } catch (\Exception $e) {
            Log::error("Failed to save new OTP for {$request->email}: " . $e->getMessage());
            return back()->withErrors(['email' => 'Something went wrong while saving OTP.']);
        }

        try {
            Mail::send('emails.otp-verification', ['otp' => $otp, 'email' => $request->email], function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('EventAps - Email Verification Code');
            });

            Log::info("Resent OTP email to: " . $request->email);

            return back()->with('status', 'New verification code sent to your email.');
        } catch (\Exception $e) {
            Log::error("Failed to resend OTP email to {$request->email}: " . $e->getMessage());
            return back()->withErrors(['email' => 'Failed to send verification code.']);
        }
    }
}
