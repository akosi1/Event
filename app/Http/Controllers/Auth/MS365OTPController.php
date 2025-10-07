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
use Illuminate\Support\Facades\Validator;
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
        // ✅ Sanitize: Remove extra spaces and control chars
        $rawEmail = $request->input('email', '');
        $email = trim($rawEmail);

        // ✅ Enforce "NO SPACES" policy strictly (including non-breaking spaces)
        if ($rawEmail !== $email || preg_match('/\s/u', $rawEmail)) {
            return back()
                ->withInput(['email' => $email])
                ->withErrors(['email' => 'Email must not contain any spaces or special whitespace characters.']);
        }

        // ✅ Additional sanitization: allow only safe email characters
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return back()
                ->withInput(['email' => $email])
                ->withErrors(['email' => 'Invalid email format.']);
        }

        // ✅ Validate domain and format
        $validator = Validator::make(['email' => $email], [
            'email' => [
                'required',
                'email',
                'max:254',
                'regex:/^[a-zA-Z0-9._%+-]+@mcclawis\.edu\.ph$/i',
            ],
        ], [
            'email.regex' => 'Please use your official McLawis College email address (@mcclawis.edu.ph).',
        ]);

        if ($validator->fails()) {
            return back()
                ->withInput(['email' => $email])
                ->withErrors($validator);
        }

        // ✅ Prevent registration if already exists
        if (User::where('email', $email)->exists()) {
            return back()->withErrors(['email' => 'This email is already registered.']);
        }

        // Store sanitized email in session
        $request->session()->put('email', $email);
        $request->session()->put('ms365_verification_started', true);

        return $this->sendOtp($email);
    }

    /**
     * Helper: Generate and send OTP securely
     */
    private function sendOtp(string $email): RedirectResponse
    {
        // ✅ Final safety check: no spaces, valid format
        if (preg_match('/\s/u', $email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Log::warning('Blocked OTP send for invalid email: ' . $email);
            return redirect()->route('ms365.verify')
                ->withErrors(['email' => 'Invalid email address.']);
        }

        // ✅ Ensure domain is correct (defense in depth)
        if (!str_ends_with(strtolower($email), '@mcclawis.edu.ph')) {
            Log::warning('OTP requested for non-McLawis email: ' . $email);
            return redirect()->route('ms365.verify')
                ->withErrors(['email' => 'Only @mcclawis.edu.ph emails are allowed.']);
        }

        // Generate 6-digit numeric OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store hashed OTP with expiration
        OtpVerification::updateOrCreate(
            ['email' => $email],
            [
                'otp' => Hash::make($otp),
                'expires_at' => Carbon::now()->addMinutes(10),
                'attempts' => 0,
                'verified_at' => null,
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
            Log::error('OTP email failed for: ' . $email . ' - ' . $e->getMessage());
            return back()->withErrors(['email' => 'Failed to send verification code. Please try again later.']);
        }
    }

    /**
     * Show OTP verification form
     */
    public function showOTPForm(): View
    {
        if (!session('email')) {
            return redirect()->route('ms365.verify')
                ->withErrors(['email' => 'Session expired. Please start verification again.']);
        }

        return view('auth.otp-verify');
    }

    /**
     * Verify OTP and proceed to registration
     */
    public function verifyOTP(Request $request): RedirectResponse
    {
        // ✅ Sanitize inputs
        $rawOtp = $request->input('otp', '');
        $rawEmail = $request->input('email', '');

        $otp = trim($rawOtp);
        $email = trim($rawEmail);

        // ✅ Enforce "NO SPACES" in OTP or email
        if ($rawOtp !== $otp || $rawEmail !== $email || preg_match('/\s/u', $rawOtp . $rawEmail)) {
            Log::warning('OTP verification blocked due to whitespace in input.');
            return back()->withErrors(['otp' => 'Invalid input: spaces or special characters are not allowed.']);
        }

        // ✅ Validate OTP format: must be exactly 6 digits
        if (!preg_match('/^[0-9]{6}$/', $otp)) {
            return back()->withErrors(['otp' => 'OTP must be a 6-digit number with no spaces or symbols.']);
        }

        // ✅ Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return back()->withErrors(['email' => 'Invalid email format.']);
        }

        // ✅ Cross-check email with session (prevent tampering)
        if ($email !== session('email')) {
            Log::alert('Email mismatch in OTP verification: session=' . session('email') . ', input=' . $email);
            return back()->withErrors(['otp' => 'Verification request mismatch. Please restart the process.']);
        }

        $otpRecord = OtpVerification::where('email', $email)->first();

        if (!$otpRecord) {
            Log::warning('OTP verification attempted for unregistered email: ' . $email);
            return back()->withErrors(['otp' => 'Invalid verification request.']);
        }

        if ($otpRecord->isExpired()) {
            return back()->withErrors(['otp' => 'Verification code has expired. Please request a new code.']);
        }

        if ($otpRecord->maxAttemptsReached()) {
            return back()->withErrors(['otp' => 'Too many failed attempts. Please request a new code.']);
        }

        if (!Hash::check($otp, $otpRecord->otp)) {
            $otpRecord->incrementAttempts();
            Log::info('Failed OTP attempt for: ' . $email . ' (attempts: ' . ($otpRecord->attempts + 1) . ')');
            return back()->withErrors(['otp' => 'Invalid verification code.']);
        }

        // ✅ Mark as verified
        $otpRecord->update(['verified_at' => Carbon::now()]);

        // ✅ Store verified email securely
        $request->session()->put('verified_email', $email);
        $request->session()->put('email_verified', true);
        $request->session()->put('otp_verified', true);
        $request->session()->save();

        Log::info('OTP verified successfully for: ' . $email);

        return redirect()->route('register')
            ->with('success', 'Email verified successfully! Please complete your registration.');
    }

    /**
     * Resend OTP with rate limiting
     */
    public function resendOTP(Request $request): RedirectResponse
    {
        $rawEmail = $request->input('email', '');
        $email = trim($rawEmail);

        // ✅ Block any whitespace
        if ($rawEmail !== $email || preg_match('/\s/u', $rawEmail)) {
            return back()->withErrors(['email' => 'Email must not contain spaces.']);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return back()->withErrors(['email' => 'Invalid email format.']);
        }

        // ✅ Verify email matches session (prevent abuse)
        if ($email !== session('email')) {
            Log::warning('Resend OTP: email mismatch - session=' . session('email') . ', input=' . $email);
            return back()->withErrors(['email' => 'Invalid request.']);
        }

        $otpRecord = OtpVerification::where('email', $email)->first();

        // ✅ Rate limit: 1 resend per minute
        if ($otpRecord && Carbon::now()->lt($otpRecord->updated_at->addMinutes(1))) {
            return back()->withErrors(['otp' => 'Please wait 1 minute before requesting another code.']);
        }

        return $this->sendOtp($email);
    }
}