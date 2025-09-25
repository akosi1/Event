<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpVerificationMail;
use App\Models\OtpVerification;
use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration form.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle initial registration request (MS365 email verification)
     */
    public function store(Request $request): RedirectResponse
    {
        // Check if reCAPTCHA is configured
        $recaptchaSecret = config('services.recaptcha.secret_key');
        if (!$recaptchaSecret) {
            \Log::error('reCAPTCHA secret key is missing in config/services.php or .env');
            throw ValidationException::withMessages([
                'recaptcha' => 'Server configuration error. Please try again later.',
            ]);
        }

        // reCAPTCHA verification
        $recaptchaToken = $request->input('g-recaptcha-response');
        if (!$recaptchaToken) {
            throw ValidationException::withMessages([
                'recaptcha' => 'Please verify that you are not a robot.',
            ]);
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => $recaptchaSecret,
            'response' => $recaptchaToken,
            'remoteip' => $request->ip(),
        ]);

        $recaptcha = $response->json();

        if (!($recaptcha['success'] ?? false) || ($recaptcha['score'] ?? 0) < 0.5) {
            throw ValidationException::withMessages([
                'recaptcha' => 'Suspicious activity detected. Please try again.',
            ]);
        }

        // Validate input
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'ends_with:@mcclawis.edu.ph'],
            'department' => ['required', 'string', 'in:BSIT,BSBA,BSED,BEED,BSHM'],
            'year_level' => ['required', 'integer', 'min:1', 'max:4'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $email = strtolower(trim($request->input('email')));

        // Check if user already exists
        if (User::where('email', $email)->exists()) {
            return back()->withErrors(['email' => 'An account with this email already exists.'])->withInput();
        }

        // Check if student exists in database
        $student = Student::where('email', $email)->first();
        if (!$student) {
            return back()->withErrors([
                'email' => 'Email not found in student database. Please contact administration.'
            ])->withInput();
        }

        // Verify name & department
        if (
            strtolower($student->first_name) !== strtolower(trim($request->input('first_name'))) ||
            strtolower($student->last_name) !== strtolower(trim($request->input('last_name'))) ||
            $student->department !== $request->input('department')
        ) {
            return back()->withErrors([
                'email' => 'The provided information does not match our student records.'
            ])->withInput();
        }

        // Store registration data in session
        $request->session()->put('registration_data', [
            'first_name' => trim($request->input('first_name')),
            'middle_name' => trim($request->input('middle_name')),
            'last_name' => trim($request->input('last_name')),
            'email' => $email,
            'department' => $request->input('department'),
            'year_level' => $request->input('year_level'),
            'password' => Hash::make($request->input('password'))
        ]);

        // Send OTP
        try {
            $otp = OtpVerification::generateOtp($email, 'registration', $request->ip());

            Mail::to($email)->send(new OtpVerificationMail(
                $otp->otp_code,
                $student->full_name ?? $student->first_name . ' ' . $student->last_name,
                'registration'
            ));

            $request->session()->put('otp_email', $email);
            $request->session()->put('otp_type', 'registration');

            return redirect()->route('otp.show')->with('message', 'Check your MS365 email for the verification code.');

        } catch (\Exception $e) {
            \Log::error('Failed to send registration OTP', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);

            return back()->withErrors([
                'email' => 'Failed to send verification email. Please try again.'
            ])->withInput();
        }
    }

    /**
     * Complete registration after OTP verification
     */
    public function complete(Request $request): RedirectResponse
    {
        $registrationData = $request->session()->get('registration_data');
        $otpVerified = $request->session()->get('otp_verified_registration');

        if (!$registrationData || !$otpVerified) {
            return redirect()->route('register')->withErrors([
                'email' => 'Registration session expired. Please try again.'
            ]);
        }

        if ($otpVerified !== $registrationData['email']) {
            return redirect()->route('register')->withErrors([
                'email' => 'Email verification mismatch. Please try again.'
            ]);
        }

        try {
            // Create user
            $fullName = trim(
                $registrationData['first_name'] . ' ' .
                ($registrationData['middle_name'] ?? '') . ' ' .
                $registrationData['last_name']
            );

            $user = User::create([
                'name' => $fullName,
                'email' => $registrationData['email'],
                'password' => $registrationData['password'],
                'department' => $registrationData['department'],
                'student_id' => null,
                'role' => 'student',
                'status' => 'active',
                'email_verified_at' => now()
            ]);

            // Link with student
            $student = Student::where('email', $registrationData['email'])->first();
            if ($student) {
                $student->update([
                    'first_name' => $registrationData['first_name'],
                    'middle_name' => $registrationData['middle_name'],
                    'last_name' => $registrationData['last_name'],
                    'year_level' => $registrationData['year_level'],
                    'email_verified_at' => now()
                ]);

                $user->update(['student_id' => $student->student_id]);
            }

            // Clean up session
            $request->session()->forget([
                'registration_data',
                'otp_verified_registration',
                'otp_verified_at'
            ]);

            // Fire event
            event(new Registered($user));
            Auth::login($user);

            return redirect()->route('dashboard')
                ->with('success', 'Account created successfully! Welcome to McClawis portal.');

        } catch (\Exception $e) {
            \Log::error('Registration completion failed', [
                'email' => $registrationData['email'],
                'error' => $e->getMessage()
            ]);

            return redirect()->route('register')->withErrors([
                'email' => 'Failed to complete registration. Please try again.'
            ]);
        }
    }
}
