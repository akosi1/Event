<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
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
        // ✅ Step 1: Validate that the token exists
        $recaptchaToken = $request->input('g-recaptcha-response');

        if (empty($recaptchaToken)) {
            throw ValidationException::withMessages([
                'recaptcha' => 'reCAPTCHA verification failed. Please try again.',
            ]);
        }

        // ✅ Step 2: Send token to Google for verification
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret_key'),
            'response' => $recaptchaToken,
            'remoteip' => $request->ip(),
        ]);

        $recaptchaResult = $response->json();

        // ✅ Step 3: Check response
        if (
            !($recaptchaResult['success'] ?? false) ||
            ($recaptchaResult['score'] ?? 0) < 0.5
        ) {
            \Log::warning('reCAPTCHA login blocked', [
                'ip' => $request->ip(),
                'score' => $recaptchaResult['score'] ?? 'N/A',
                'errors' => $recaptchaResult['error-codes'] ?? [],
            ]);

            throw ValidationException::withMessages([
                'recaptcha' => 'Suspicious activity detected. Try again.',
            ]);
        }

        // ✅ Step 4: Continue login
        $request->authenticate();
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
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
}
