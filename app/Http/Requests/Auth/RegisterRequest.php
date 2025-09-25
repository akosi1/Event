<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class RegisterRequest extends FormRequest
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
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'email' => [
                'required', 
                'string', 
                'email', 
                'max:255', 
                'unique:users,email',
                'unique:students,email',
                'regex:/^[a-zA-Z0-9._%+-]+@mcclawis\.edu\.ph$/'
            ],
            'department' => ['required', 'string', 'in:BSIT,BSBA,BSED,BEED,BSHM'],
            'year_level' => ['required', 'integer', 'between:1,4'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
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
            'first_name.required' => 'Please enter your first name.',
            'last_name.required' => 'Please enter your last name.',
            'email.required' => 'Please enter your MS365 email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'email.regex' => 'Please use your official McClawis email address (e.g., john.doe@mcclawis.edu.ph).',
            'department.required' => 'Please select your department.',
            'department.in' => 'Please select a valid department.',
            'year_level.required' => 'Please select your year level.',
            'year_level.between' => 'Please select a valid year level (1-4).',
            'password.required' => 'Please enter a password.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }

    /**
     * Get custom attribute names.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'first_name' => 'first name',
            'last_name' => 'last name',
            'middle_name' => 'middle name',
            'email' => 'email address',
            'department' => 'department',
            'year_level' => 'year level',
            'password' => 'password',
        ];
    }
}