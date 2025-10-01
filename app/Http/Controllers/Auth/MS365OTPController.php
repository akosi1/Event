<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OtpVerification;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
     * Helper function to generate and send OTP via PHPMailer
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
            $mail = new PHPMailer(true);

            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.office365.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = env('MAIL_USERNAME');
            $mail->Password   = env('MAIL_PASSWORD');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Sender & recipient
            $mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME', 'EventAps'));
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'EventAps - Email Verification Code';

            // Render Blade template to string
            $body = view('emails.otp-verification', [
                'otp' => $otp,
                'email' => $email
            ])->render();

            $mail->Body = $body;

            $mail->send();

            Log::info("✅ PHPMailer sent OTP to: {$email}");

            return redirect()->route('otp.verify.form')
                ->with('status', 'Verification code sent to your McLawis email address.');
        } catch (Exception $e) {
            Log::error("❌ PHPMailer failed for {$email}: {$e->getMessage()}");

            if (app()->environment('local')) {
                return back()->withErrors(['email' => 'Mailer error: ' . $e->getMessage()]);
            }

            return back()->withErrors(['email' => 'Failed to send verification code. Please try again later.']);
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

        return $this->sendOtp($request->email);
    }
}
