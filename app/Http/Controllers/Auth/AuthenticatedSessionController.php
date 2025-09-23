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

    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {

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

        if (
            !($recaptcha['success'] ?? false) ||
            ($recaptcha['score'] ?? 0) < 0.5
        ) {
            \Log::warning('reCAPTCHA login blocked', [
                'ip'     => $request->ip(),
                'score'  => $recaptcha['score'] ?? 'N/A',
                'errors' => $recaptcha['error-codes'] ?? [],
            ]);

            throw ValidationException::withMessages([
                'recaptcha' => 'Suspicious activity detected. Please try again.',
            ]);
        }

   
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
