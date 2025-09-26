<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpVerificationMail;
use App\Models\OtpVerification;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class OtpController extends Controller
{
    public function show(Request $request): View
    {
        $email = $request->session()->get('otp_email');
        $type = $request->session()->get('otp_type', 'login');

        if (!$email) {
            return redirect()->route('login')->withErrors(['email' => 'Session expired. Please try again.']);
        }

        return view('auth.otp-verification', [
            'email' => $email,
            'type' => $type,
            'maskedEmail' => $this->maskEmail($email)
        ]);
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'otp_code' => 'required|string|size:6'
        ]);

        $email = $request->session()->get('otp_email');
        $type = $request->session()->get('otp_type', 'login');
        $otpCode = $request->input('otp_code');

        if (!$email) {
            return redirect()->route('login')->withErrors(['otp_code' => 'Session expired. Please try again.']);
        }

        // Verify OTP
        $otp = OtpVerification::where('email', $email)
            ->where('otp_code', $otpCode)
            ->where('type', $type)
            ->where('expires_at', '>', now())
            ->where('used_at', null)
            ->first();

        if (!$otp) {
            return back()->withErrors(['otp_code' => 'Invalid or expired OTP code.']);
        }

        // Mark OTP as used
        $otp->update(['used_at' => now()]);

        // Set verification in session
        if ($type === 'registration') {
            $request->session()->put('otp_verified_registration', $email);
        } else {
            $request->session()->put('otp_verified_login', $email);
        }
        
        $request->session()->put('otp_verified_at', now());

        // Clear OTP session data
        $request->session()->forget(['otp_email', 'otp_type']);

        // Redirect based on type
        if ($type === 'registration') {
            return redirect()->route('register.complete');
        } else {
            return redirect()->route('login.complete');
        }
    }

    public function resend(Request $request): RedirectResponse
    {
        $email = $request->session()->get('otp_email');
        $type = $request->session()->get('otp_type', 'login');

        if (!$email) {
            return redirect()->route('login')->withErrors(['email' => 'Session expired. Please try again.']);
        }

        // Check rate limiting
        $recentOtp = OtpVerification::where('email', $email)
            ->where('created_at', '>', now()->subMinutes(1))
            ->first();

        if ($recentOtp) {
            return back()->withErrors(['otp_code' => 'Please wait before requesting a new code.']);
        }

        try {
            // Generate new OTP
            $otp = OtpVerification::generateOtp($email, $type, $request->ip());
            
            // Get student info
            $student = Student::where('email', $email)->first();
            $name = $student ? ($student->full_name ?? $student->first_name . ' ' . $student->last_name) : 'Student';

            // Send OTP
            Mail::to($email)->send(new OtpVerificationMail($otp->otp_code, $name, $type));

            return back()->with('message', 'A new verification code has been sent to your email.');

        } catch (\Exception $e) {
            \Log::error('Failed to resend OTP', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);

            return back()->withErrors(['otp_code' => 'Failed to send verification code. Please try again.']);
        }
    }

    private function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return $email;
        }

        $localPart = $parts[0];
        $domain = $parts[1];

        if (strlen($localPart) <= 3) {
            return str_repeat('*', strlen($localPart)) . '@' . $domain;
        }

        $visibleStart = substr($localPart, 0, 2);
        $visibleEnd = substr($localPart, -1);
        $maskedMiddle = str_repeat('*', strlen($localPart) - 3);

        return $visibleStart . $maskedMiddle . $visibleEnd . '@' . $domain;
    }
}