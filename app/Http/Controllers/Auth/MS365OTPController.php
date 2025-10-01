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
use Carbon\Carbon;

class MS365OTPController extends Controller
{
    public function showMS365Form(): View
    {
        return view('auth.ms365-verify');
    }

    public function verifyMS365Account(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => [
                'required',
                'email',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (!str_ends_with($value, '@mcclawis.edu.ph')) {
                        $fail('The ' . $attribute . ' must be a mcclawis.edu.ph email address.');
                    }
                },
            ],
        ]);

        if (User::where('email', $request->email)->exists()) {
            return back()->withErrors(['email' => 'This email is already registered.']);
        }

        session(['email' => $request->email]);

        return $this->sendOtp($request->email);
    }

    private function sendOtp(string $email): RedirectResponse
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpVerification::updateOrCreate(
            ['email' => $email],
            [
                'otp' => Hash::make($otp),
                'expires_at' => Carbon::now()->addMinutes(10),
                'attempts' => 0,
            ]
        );

        try {
            Mail::send('emails.otp-verification', ['otp' => $otp, 'email' => $email], function ($message) use ($email) {
                $message->to($email)
                        ->subject('EventAps - Email Verification Code');
            });

            return redirect()->route('otp.verify.form')
                             ->with('status', 'Verification code sent to your McLawis email address.');
        } catch (\Exception $e) {
            if (app()->environment('local')) {
                return back()->withErrors(['email' => 'Mailer error: ' . $e->getMessage()]);
            }
            return back()->withErrors(['email' => 'Failed to send verification code. Please try again later.']);
        }
    }

    public function showOTPForm(): View
    {
        if (!session('email')) {
            return redirect()->route('ms365.verify');
        }

        return view('auth.otp-verify');
    }

    public function verifyOTP(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
            'email' => ['required', 'email'],
        ]);

        $otpRecord = OtpVerification::where('email', $request->email)->first();

        if (!$otpRecord) {
            return back()->withErrors(['otp' => 'Invalid verification request.']);
        }

        if (Carbon::now()->gt($otpRecord->expires_at)) {
            return back()->withErrors(['otp' => 'Verification code has expired.']);
        }

        if ($otpRecord->attempts >= 3) {
            return back()->withErrors(['otp' => 'Too many failed attempts. Please request a new code.']);
        }

        if (!Hash::check($request->otp, $otpRecord->otp)) {
            $otpRecord->increment('attempts');
            return back()->withErrors(['otp' => 'Invalid verification code.']);
        }

        $otpRecord->update(['verified_at' => Carbon::now()]);

        return redirect()->route('register')
                         ->with('verified_email', $request->email)
                         ->with('status', 'Email verified successfully! Please complete your registration.');
    }

    public function resendOTP(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $otpRecord = OtpVerification::where('email', $request->email)->first();

        if ($otpRecord && Carbon::now()->lt($otpRecord->created_at->addMinutes(2))) {
            return back()->withErrors(['otp' => 'Please wait before requesting another code.']);
        }

        return $this->sendOtp($request->email);
    }
}
