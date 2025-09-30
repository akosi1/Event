<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OtpVerification;
use Illuminate\Http\Request;ka lag
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;   // <--- Added this
use Carbon\Carbon;

class MS365OTPController extends Controller
{
    /**
     * Show MS365 account verification form
     */
    public function showMS365Form(): View
    {
        return view('auth.ms365-verify');
    }

    /**
     * Verify MS365 email and send OTP
     */
    public function verifyMS365Account(Request $request): RedirectResponse
    {
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

        if (User::where('email', $request->email)->exists()) {
            return back()->withErrors(['email' => 'This email is already registered.']);
        }

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        OtpVerification::updateOrCreate(
            ['email' => $request->email],
            [
                'otp' => Hash::make($otp),
                'expires_at' => Carbon::now()->addMinutes(10),
                'attempts' => 0,
            ]
        );

        try {
            Mail::send('emails.otp-verification', ['otp' => $otp, 'email' => $request->email], function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('EventAps - Email Verification Code');
            });

            return redirect()->route('otp.verify.form')
                           ->with('email', $request->email)
                           ->with('status', 'Verification code sent to your McLawis email address.');
        } catch (\Exception $e) {
            Log::error('OTP email sending failed (verifyMS365Account): ' . $e->getMessage());
            return back()->withErrors(['email' => 'Failed to send verification code. Please try again.']);
        }
    }

    /**
     * Show OTP verification form
     */
    public function showOTPForm(): View
    {
        if (!session('email')) {
            return redirect()->route('ms365.verify');
        }
        
        return view('auth.otp-verify');
    }

    /**
     * Verify OTP and proceed to registration
     */
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

    /**
     * Resend OTP
     */
    public function resendOTP(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $otpRecord = OtpVerification::where('email', $request->email)->first();
        
        if ($otpRecord && Carbon::now()->lt($otpRecord->created_at->addMinutes(2))) {
            return back()->withErrors(['otp' => 'Please wait before requesting another code.']);
        }

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        OtpVerification::updateOrCreate(
            ['email' => $request->email],
            [
                'otp' => Hash::make($otp),
                'expires_at' => Carbon::now()->addMinutes(10),
                'attempts' => 0,
            ]
        );

        try {
            Mail::send('emails.otp-verification', ['otp' => $otp, 'email' => $request->email], function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('EventAps - Email Verification Code');
            });

            return back()->with('status', 'New verification code sent to your email.');
        } catch (\Exception $e) {
            Log::error('OTP email sending failed (resendOTP): ' . $e->getMessage());
            return back()->withErrors(['email' => 'Failed to send verification code.']);
        }
    }
}
