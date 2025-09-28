<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'g-recaptcha-response' => 'required', // validate that reCAPTCHA field exists
        ]);

        // reCAPTCHA verification
        $recaptchaToken = $request->input('g-recaptcha-response');

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => config('services.recaptcha.secret_key'), // store this in config/services.php
            'response' => $recaptchaToken,
            'remoteip' => $request->ip(),
        ]);

        $recaptcha = $response->json();
        if (!($recaptcha['success'] ?? false) || ($recaptcha['score'] ?? 0) < 0.5) {
            \Log::warning('Admin reCAPTCHA login blocked', [
                'ip'     => $request->ip(),
                'score'  => $recaptcha['score'] ?? 'N/A',
                'errors' => $recaptcha['error-codes'] ?? [],
            ]);

            throw ValidationException::withMessages([
                'recaptcha' => 'Suspicious activity detected. Please try again.',
            ]);
        }

        // Proceed with regular login
        if (Auth::attempt($request->only('email', 'password'))) {
            if (Auth::user()->isAdmin()) {
                $request->session()->regenerate();
                return redirect()->intended(route('admin.dashboard'));
            } else {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => 'You do not have admin access.',
                ]);
            }
        }

        throw ValidationException::withMessages([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
