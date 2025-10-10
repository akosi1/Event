<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules for login fields.
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                'max:255',
                'regex:/^[^\s]+$/'
            ],
            'password' => [
                'required',
                'string',
                // Optional: uncomment below if you want strong password policy even during login
                // 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'
            ],
            'department' => [
                'required',
                'string',
                'in:BSIT,BSBA,BSEd,BEED,BSHM'
            ],
        ];
    }

    /**
     * Custom messages (generic to prevent credential leaks).
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Invalid login credentials.',
            'email.email' => 'Invalid login credentials.',
            'email.regex' => 'Invalid login credentials.',
            'password.required' => 'Invalid login credentials.',
            'department.required' => 'Please select your department.',
            'department.in' => 'Please select a valid department.',
        ];
    }

    /**
     * Attempt authentication.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Find user
        $user = \App\Models\User::where('email', $this->email)->first();

        // Validate department & password
        $credentialsMatch = $user &&
            $user->department === $this->department &&
            Auth::validate([
                'email' => $this->email,
                'password' => $this->password
            ]);

        if (! $credentialsMatch) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => 'Invalid login credentials.',
            ]);
        }

        // Success: log in user
        Auth::login($user, $this->boolean('remember'));

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Check for too many failed login attempts.
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
        ]);
    }

    /**
     * Generate a unique key for login rate limiting.
     */
    public function throttleKey(): string
    {
        return Str::lower($this->string('email')) . '|' . $this->ip();
    }
}
