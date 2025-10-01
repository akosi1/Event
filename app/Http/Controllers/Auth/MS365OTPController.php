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
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Mail\PHPMailerService;

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
        'max:255'
            ],
        ], [
            'email.regex' => 'Please use your official McLawis College email address (@mcclawis.edu.ph)',
        ]);

        // Check if email is already registered
        if (User::where('email', $request->email)->exists()) {
            return back()->withErrors(['email' => 'This email is already registered.']);
        }

        // Store the email in the session for OTP verification
        session(['email' => $request->email]);

        // Generate OTP and send it
        return $this->sendOtp($request->email);
    }

    /**
     * Helper function to generate and send OTP
     */
    private function sendOtp(string $email): RedirectResponse
    {
        // Generate OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP in the database
        OtpVerification::updateOrCreate(
            ['email' => $email],
            [
                'otp' => Hash::make($otp),
                'expires_at' => Carbon::now()->addMinutes(10),
                'attempts' => 0,
            ]
        );

        try {
            // Send OTP email
            Mail::send('emails.otp-verification', ['otp' => $otp, 'email' => $email], function ($message) use ($email) {
                $message->to($email)
                        ->subject('EventAps - Email Verification Code');
            });

            return redirect()->route('otp.verify.form')
                             ->with('status', 'Verification code sent to your McLawis email address.');
        } catch (\Exception $e) {
            Log::error('OTP email sending failed for email: ' . $email . ' (verifyMS365Account): ' . $e->getMessage());
            return back()->withErrors(['email' => 'Failed to send verification code. Please try again.']);
        }
    }

    /**
     * Show OTP verification form
     */
    public function showOTPForm(): View
    {
        // Redirect to MS365 verification form if email is not in session
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

        // Check if OTP has expired
        if (Carbon::now()->gt($otpRecord->expires_at)) {
            return back()->withErrors(['otp' => 'Verification code has expired.']);
        }

        // Check if too many attempts were made
        if ($otpRecord->attempts >= 3) {
            return back()->withErrors(['otp' => 'Too many failed attempts. Please request a new code.']);
        }

        // Check if OTP is correct
        if (!Hash::check($request->otp, $otpRecord->otp)) {
            $otpRecord->increment('attempts');
            return back()->withErrors(['otp' => 'Invalid verification code.']);
        }

        // Mark as verified
        $otpRecord->update(['verified_at' => Carbon::now()]);

        // Redirect to registration page
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

        // Prevent sending OTP too soon
        if ($otpRecord && Carbon::now()->lt($otpRecord->created_at->addMinutes(2))) {
            return back()->withErrors(['otp' => 'Please wait before requesting another code.']);
        }

        // Send a new OTP
        return $this->sendOtp($request->email);
    }
}
