<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OtpVerification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Carbon\Carbon;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View|RedirectResponse
    {
        $verifiedEmail = session('verified_email');

        if (!$verifiedEmail) {
            return redirect()->route('ms365.verify')
                             ->with('error', 'Please verify your McLawis College email first.');
        }

        $otpRecord = OtpVerification::where('email', $verifiedEmail)
                                    ->whereNotNull('verified_at')
                                    ->where('created_at', '>=', Carbon::now()->subHours(1))
                                    ->first();

        if (!$otpRecord) {
            return redirect()->route('ms365.verify')
                             ->with('error', 'Email verification has expired. Please verify again.');
        }

        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $verifiedEmail = session('verified_email');

        if (!$verifiedEmail) {
            return redirect()->route('ms365.verify')
                             ->with('error', 'Please verify your McLawis College email first.');
        }

        $otpRecord = OtpVerification::where('email', $verifiedEmail)
                                    ->whereNotNull('verified_at')
                                    ->where('created_at', '>=', Carbon::now()->subHours(1))
                                    ->first();

        if (!$otpRecord) {
            return redirect()->route('ms365.verify')
                             ->with('error', 'Email verification has expired. Please verify again.');
        }

        // reCAPTCHA v3 validation
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
        if (!($recaptcha['success'] ?? false) || ($recaptcha['score'] ?? 0) < 0.5) {
            \Log::warning('reCAPTCHA registration blocked', [
                'ip'     => $request->ip(),
                'score'  => $recaptcha['score'] ?? 'N/A',
                'errors' => $recaptcha['error-codes'] ?? [],
            ]);

            throw ValidationException::withMessages([
                'recaptcha' => 'Suspicious activity detected. Please try again.',
            ]);
        }

        $request->validate([
            'first_name'   => ['required', 'string', 'max:255'],
            'middle_name'  => ['nullable', 'string', 'max:255'],
            'last_name'    => ['required', 'string', 'max:255'],
            'email'        => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:' . User::class,
                function ($attribute, $value, $fail) use ($verifiedEmail) {
                    if ($value !== $verifiedEmail) {
                        $fail('The email must match your verified McLawis College email.');
                    }
                },
            ],
            'department'   => ['required', 'string', 'in:BSIT,BSBA,BSED,BEED,BSHM'],
            'password'     => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'first_name'        => $request->first_name,
            'middle_name'       => $request->middle_name,
            'last_name'         => $request->last_name,
            'email'             => $verifiedEmail,
            'department'        => $request->department,
            'password'          => Hash::make($request->password),
            'role'              => 'user',
            'status'            => 'active',
            'email_verified_at' => now(),
        ]);

        $otpRecord->delete();

        event(new Registered($user));
        Auth::login($user);

        session()->forget(['verified_email', 'email']);

        return redirect(route('dashboard', absolute: false));
    }
}
