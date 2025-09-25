<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        // reCAPTCHA verification
        $recaptchaToken = $request->input('g-recaptcha-response');
        if (!$recaptchaToken) {
            throw ValidationException::withMessages([
                'recaptcha' => 'Please verify that you are not a robot.',
            ]);
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => config('services.recaptcha.secret_key'),
            'response' => $recaptchaToken,
            'remoteip' => $request->ip(),
        ]);

        $recaptcha = $response->json();
        if (!($recaptcha['success'] ?? false) || ($recaptcha['score'] ?? 0) < 0.5) {
            \Log::warning('reCAPTCHA login blocked', [
                'ip'     => $request->ip(),
                'score'  => $recaptcha['score'] ?? 'N/A',
                'errors' => $recaptcha['error-codes'] ?? [],
            ]);

            throw ValidationException::withMessages([
                'recaptcha' => 'Suspicious activity detected. Please try again.',
            ]);
        }

        // Get the login identifier (email or student ID)
        $identifier = $request->input('email');
        $department = $request->input('department');
        
        // Check if it's a student ID or MS365 email
        $user = null;
        $student = null;
        
        if (Student::isMs365Email($identifier)) {
            // It's an MS365 email
            $student = Student::where('email', $identifier)
                ->where('department', $department)
                ->first();
                
            if ($student) {
                $user = User::where('email', $identifier)->first();
            }
        } else {
            // It's a student ID
            $student = Student::where('student_id', $identifier)
                ->where('department', $department)  
                ->first();
                
            if ($student) {
                $user = User::where('email', $student->email)->first();
                
                // For MS365 authentication, redirect to OTP verification
                if ($user && Student::isMs365Email($student->email)) {
                    $request->session()->put('pending_login_user_id', $user->id);
                    $request->session()->put('otp_email', $student->email);
                    $request->session()->put('otp_type', 'login');
                    
                    return redirect()->route('otp.show')
                        ->with('message', 'Please verify your identity with the OTP sent to your MS365 email.');
                }
            }
        }

        if (!$student || !$user) {
            throw ValidationException::withMessages([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }

        // For MS365 emails, require OTP verification before login
        if (Student::isMs365Email($student->email)) {
            // Check if OTP was already verified in this session
            $otpVerified = $request->session()->get('otp_verified_login');
            $otpVerifiedAt = $request->session()->get('otp_verified_at');
            
            if ($otpVerified !== $student->email || 
                !$otpVerifiedAt || 
                now()->diffInMinutes($otpVerifiedAt) > 10) {
                
                $request->session()->put('pending_login_user_id', $user->id);
                $request->session()->put('otp_email', $student->email);
                $request->session()->put('otp_type', 'login');
                
                return redirect()->route('otp.show')
                    ->with('message', 'Please verify your identity with the OTP sent to your MS365 email.');
            }
            
            // Clear OTP verification session data
            $request->session()->forget(['otp_verified_login', 'otp_verified_at']);
        }

        // Regular authentication for non-MS365 or already OTP-verified users
        $request->authenticate();
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Complete login after OTP verification
     */
    public function completeLogin(Request $request): RedirectResponse
    {
        $userId = $request->session()->get('pending_login_user_id');
        $otpVerified = $request->session()->get('otp_verified_login');
        
        if (!$userId || !$otpVerified) {
            return redirect()->route('login')->withErrors(['email' => 'Session expired. Please try again.']);
        }

        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('login')->withErrors(['email' => 'User not found.']);
        }

        // Log in the user
        Auth::login($user);
        $request->session()->regenerate();
        
        // Clear session data
        $request->session()->forget(['pending_login_user_id', 'otp_verified_login', 'otp_verified_at']);

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}