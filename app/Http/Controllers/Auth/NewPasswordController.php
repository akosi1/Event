<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        // Optional: Validate token/email early (Laravel does this in store, but safe to show form)
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // ✅ Step 1: Sanitize inputs
        $email = trim($request->input('email'));
        $password = trim($request->input('password'));
        $passwordConfirmation = trim($request->input('password_confirmation'));
        $token = trim($request->input('token'));

        // ✅ Enforce "no spaces" in password fields (per your security policy)
        if (preg_match('/\s/', $request->input('password') ?? '') || 
            preg_match('/\s/', $request->input('password_confirmation') ?? '')) {
            return back()
                ->withInput($request->only('email', 'token'))
                ->withErrors(['password' => 'Password must not contain any spaces.']);
        }

        // ✅ Re-inject sanitized values into request for validation
        $request->merge([
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $passwordConfirmation,
            'token' => $token,
        ]);

        // ✅ Step 2: Validate with strict rules
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email', 'max:254'],
            'password' => [
                'required',
                'confirmed',
                'regex:/^\S*$/',
                Rules\Password::defaults(),
            ],
        ]);

        // ✅ Step 3: Attempt password reset
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // ✅ Step 4: Handle response
        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()
                ->withInput($request->only('email', 'token'))
                ->withErrors(['email' => __($status)]);
    }
}