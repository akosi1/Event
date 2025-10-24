<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class Recaptcha implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('recaptcha.secret_key') ?? env('RECAPTCHA_SECRET_KEY'),
            'response' => $value,
            'remoteip' => request()->ip(),
        ]);

        if (!$response->successful()) {
            $fail('Unable to verify reCAPTCHA. Please try again.');
            return;
        }

        $result = $response->json();

        if (!$result['success']) {
            $fail('reCAPTCHA verification failed. Please try again.');
            return;
        }

        // v3: check score
        if (isset($result['score']) && $result['score'] < (float) config('recaptcha.min_score', 0.5)) {
            $fail('Suspicious activity detected. Please try again.');
            return;
        }
    }
}