<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Step 1: Sanitize input (trim and remove internal spaces if required)
        $email = trim($request->input('email'));
        
        // Enforce "no spaces" rule (as per your security policy for CREATE/UPDATE)
        if (preg_match('/\s/', $email)) {
            return back()
                ->withInput(['email' => $email])
                ->withErrors(['email' => 'Email must not contain spaces.']);
        }

        // Step 2: Validate sanitized input
        $validator = Validator::make(['email' => $email], [
            'email' => ['required', 'email', 'max:254'],
        ]);

        if ($validator->fails()) {
            return back()
                ->withInput(['email' => $email])
                ->withErrors($validator);
        }

        // Step 3: Send reset link (using sanitized email)
        $status = Password::sendResetLink(['email' => $email]);

        // Step 4: Respond (avoid user enumeration if desired)
        // Optional: Always return generic success to prevent email enumeration
        // But Laravel's default behavior is acceptable if you're okay with status feedback.

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()
                ->withInput(['email' => $email])
                ->withErrors(['email' => __($status)]);
    }
}