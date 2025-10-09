<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Rules\Recaptcha;
use App\Services\LoginAttemptService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    protected LoginAttemptService $loginAttemptService;

    public function __construct(LoginAttemptService $loginAttemptService)
    {
        $this->loginAttemptService = $loginAttemptService;
    }

    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Create unique identifier (email + IP)
        $identifier = $request->input('email') . '|' . $request->ip();

        // Check if user is locked out
        if ($this->loginAttemptService->isLockedOut($identifier)) {
            $remainingTime = $this->loginAttemptService->getRemainingLockoutTime($identifier);
            $lockoutEnd = $this->loginAttemptService->getLockoutEndTimestamp($identifier);
            
            return back()->withErrors([
                'locked_out' => true,
                'seconds' => $remainingTime,
                'lockout_end' => $lockoutEnd,
                'message' => "Too many login attempts. Please try again in {$remainingTime} seconds."
            ])->withInput($request->only('email'));
        }

        // Validate reCAPTCHA
        $request->validate([
            'g-recaptcha-response' => ['required', new Recaptcha()],
        ], [
            'g-recaptcha-response.required' => 'Please complete the reCAPTCHA verification.',
        ]);

        try {
            // Attempt to authenticate the user
            $request->authenticate();

            // Clear login attempts on successful login
            $this->loginAttemptService->clear($identifier);

            // Regenerate session to prevent fixation attacks
            $request->session()->regenerate();

            // Redirect to intended page or dashboard
            return redirect()->intended('/dashboard');

        } catch (ValidationException $e) {
            // Increment failed login attempts
            $this->loginAttemptService->increment($identifier);
            $remaining = $this->loginAttemptService->getRemainingAttempts($identifier);

            // Check if just got locked out
            if ($remaining === 0) {
                $lockoutTime = $this->loginAttemptService->getRemainingLockoutTime($identifier);
                $lockoutEnd = $this->loginAttemptService->getLockoutEndTimestamp($identifier);
                
                return back()->withErrors([
                    'locked_out' => true,
                    'seconds' => $lockoutTime,
                    'lockout_end' => $lockoutEnd,
                    'message' => "Too many login attempts. Your account has been locked for {$lockoutTime} seconds."
                ])->withInput($request->only('email'));
            }

            // Return with remaining attempts
            return back()->withErrors([
                'failed_attempt' => true,
                'remaining' => $remaining,
                'message' => $remaining === 1 
                    ? "Invalid credentials. You have 1 attempt remaining before your account is locked."
                    : "Invalid credentials. You have {$remaining} attempts remaining."
            ])->withInput($request->only('email'));
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Check login attempt status (AJAX endpoint)
     */
    public function checkAttempts(Request $request)
    {
        $identifier = $request->input('email') . '|' . $request->ip();
        
        return response()->json([
            'is_locked' => $this->loginAttemptService->isLockedOut($identifier),
            'remaining_attempts' => $this->loginAttemptService->getRemainingAttempts($identifier),
            'lockout_time' => $this->loginAttemptService->getRemainingLockoutTime($identifier)
        ]);
    }
}