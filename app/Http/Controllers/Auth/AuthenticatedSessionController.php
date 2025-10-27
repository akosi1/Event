<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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

    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $identifier = $request->input('email') . '|' . $request->ip();

        // Check if user is locked out
        if ($this->loginAttemptService->isLockedOut($identifier)) {
            $remainingTime = $this->loginAttemptService->getRemainingLockoutTime($identifier);
            $lockoutEnd = $this->loginAttemptService->getLockoutEndTimestamp($identifier);
            $minutes = floor($remainingTime / 60);
            $seconds = $remainingTime % 60;

            $timeMessage = $minutes > 0
                ? "{$minutes} minute" . ($minutes > 1 ? 's' : '') . " and {$seconds} second" . ($seconds != 1 ? 's' : '')
                : "{$seconds} second" . ($seconds != 1 ? 's' : '');

            return back()->withErrors([
                'locked_out' => true,
                'seconds' => $remainingTime,
                'lockout_end' => $lockoutEnd,
                'message' => "Too many login attempts. Please try again in {$timeMessage}."
            ])->withInput($request->only('email'));
        }

        // Validate input + reCAPTCHA
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'g-recaptcha-response' => ['required', new Recaptcha()],
        ], [
            'g-recaptcha-response.required' => 'Please complete the reCAPTCHA verification.',
        ]);

        try {
            // Attempt login
            if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials do not match our records.'],
                ]);
            }

            $user = Auth::user();

            // Clear login attempts
            $this->loginAttemptService->clear($identifier);

            // Regenerate session
            $request->session()->regenerate();

            // Redirect based on role
            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended('/dashboard');

        } catch (ValidationException $e) {
            // Increment failed attempts
            $this->loginAttemptService->increment($identifier);
            $remaining = $this->loginAttemptService->getRemainingAttempts($identifier);

            // Check lockout after failed attempt
            if ($remaining === 0 || $this->loginAttemptService->isLockedOut($identifier)) {
                $lockoutTime = $this->loginAttemptService->getRemainingLockoutTime($identifier);
                $lockoutEnd = $this->loginAttemptService->getLockoutEndTimestamp($identifier);
                $minutes = floor($lockoutTime / 60);
                $seconds = $lockoutTime % 60;

                $timeMessage = $minutes > 0
                    ? "{$minutes} minute" . ($minutes > 1 ? 's' : '') . " and {$seconds} second" . ($seconds != 1 ? 's' : '')
                    : "{$seconds} second" . ($seconds != 1 ? 's' : '');

                return back()->withErrors([
                    'locked_out' => true,
                    'seconds' => $lockoutTime,
                    'lockout_end' => $lockoutEnd,
                    'message' => "Too many login attempts. Your account has been locked for {$timeMessage}."
                ])->withInput($request->only('email'));
            }

            // Remaining attempts warning
            return back()->withErrors([
                'failed_attempt' => true,
                'remaining' => $remaining,
                'message' => $remaining === 1
                    ? "Invalid credentials. You have 1 attempt remaining."
                    : "Invalid credentials. You have {$remaining} attempts remaining."
            ])->withInput($request->only('email'));
        }
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('login');
    }

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
