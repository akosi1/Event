<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\Student;

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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string'], // Allow both email and student ID
            'password' => ['required', 'string'],
            'department' => ['required', 'string', 'in:BSIT,BSBA,BSED,BEED,BSHM'],
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Please enter your MS365 email or student ID.',
            'department.required' => 'Please select your department.',
            'department.in' => 'Please select a valid department.',
            'password.required' => 'Please enter your password.',
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $loginField = $this->input('email');
        $department = $this->input('department');
        $password = $this->input('password');
        
        // Determine if the input is email or student ID
        $isEmail = filter_var($loginField, FILTER_VALIDATE_EMAIL);
        
        $user = null;
        $student = null;
        
        if ($isEmail) {
            // Find user by email
            $user = User::where('email', $loginField)->first();
            if ($user) {
                // Get associated student record by email (as per your controller logic)
                $student = Student::where('email', $loginField)->first();
            }
        } else {
            // Find student by student_id first
            $student = Student::where('student_id', $loginField)->first();
            if ($student) {
                // Get associated user record by email
                $user = User::where('email', $student->email)->first();
            }
        }
        
        if (!$user) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        // Check if user account is active
        if (!$user->isActive()) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'email' => 'Your account is inactive. Please contact the administrator.',
            ]);
        }

        // Check if the selected department matches the user's registered department
        if ($user->department !== $department) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'department' => 'Access denied. You can only login with your registered department: ' . $user->getDepartmentNameAttribute(),
            ]);
        }

        // For additional validation, check student record if it exists
        if ($student) {
            // Check if student account is active
            if ($student->status !== 'active') {
                RateLimiter::hit($this->throttleKey());
                throw ValidationException::withMessages([
                    'email' => 'Your student account is inactive. Please contact the administrator.',
                ]);
            }

            // Double-check department from student record
            if ($student->department !== $department) {
                RateLimiter::hit($this->throttleKey());
                throw ValidationException::withMessages([
                    'department' => 'Department mismatch in student records. Please contact the administrator.',
                ]);
            }
        }

        // Attempt authentication using email, password, and department
        $credentials = [
            'email' => $user->email,
            'password' => $password,
            'department' => $department,
            'status' => 'active' // Ensure only active accounts can login
        ];
        
        if (!Auth::attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'password' => 'The password is incorrect.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}