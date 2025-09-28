<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RecaptchaController extends Controller
{
    public function verify(Request $request)
    {
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret'),
            'response' => $request->input('g-recaptcha-response'),
        ]);

        $data = $response->json();

        if ($data['success']) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 422);
    }
}
