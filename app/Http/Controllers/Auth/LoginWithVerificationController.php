<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Cache;

class LoginWithVerificationController extends Controller
{
    protected int $maxAttempts = 3;
    protected int $lockoutDuration = 300; // 5 minutes in seconds

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function attemptLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $email = strtolower(trim($request->email));
        $lockoutKey = "login_lockout:{$email}";
        $attemptsKey = "login_attempts:{$email}";

        // Check if user is locked out
        if (Cache::has($lockoutKey)) {
            $lockoutEnd = Cache::get($lockoutKey);
            throw ValidationException::withMessages([
                'locked_out' => true,
                'lockout_end' => $lockoutEnd,
            ]);
        }

        $credentials = $request->only('email', 'password');

        if (! Auth::validate($credentials)) {
            // Increment failed attempts
            $attempts = Cache::get($attemptsKey, 0) + 1;
            Cache::put($attemptsKey, $attempts, now()->addMinutes(15));

            $remaining = $this->maxAttempts - $attempts;

            if ($attempts >= $this->maxAttempts) {
                // Lock out user
                $lockoutEnd = now()->addSeconds($this->lockoutDuration)->timestamp;
                Cache::put($lockoutKey, $lockoutEnd, $this->lockoutDuration);
                Cache::forget($attemptsKey);

                throw ValidationException::withMessages([
                    'locked_out' => true,
                    'lockout_end' => $lockoutEnd,
                ]);
            }

            throw ValidationException::withMessages([
                'failed_attempt' => true,
                'remaining' => $remaining,
            ]);
        }

        // Login successful — clear attempts
        Cache::forget($attemptsKey);
        Cache::forget($lockoutKey);

        $user = Auth::getProvider()->retrieveByCredentials($credentials);

        $code = random_int(100000, 999999);

        Session::put('login_verification', [
            'user_id' => $user->id,
            'email' => $user->email,
            'code' => $code,
            'expires_at' => now()->addMinutes(10),
        ]);

        Mail::raw("Your verification code is: {$code}\n\nThis code expires in 10 minutes.", function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Login Verification Code')
                    ->from(config('mail.from.address'), config('mail.from.name'));
        });

        return redirect()->route('login')->with('needs_verification', true);
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|digits:6',
        ]);

        $verification = Session::get('login_verification');

        if (!$verification) {
            return redirect()->route('login')->withErrors(['verification_code' => 'Session expired. Please log in again.']);
        }

        if (now()->greaterThan($verification['expires_at'])) {
            Session::forget('login_verification');
            return redirect()->route('login')->withErrors(['verification_code' => 'Verification code has expired.']);
        }

        if ((int) $request->verification_code !== (int) $verification['code']) {
            return back()->withErrors(['verification_code' => 'Invalid verification code.']);
        }

        $user = Auth::getProvider()->retrieveById($verification['user_id']);
        Auth::login($user, $request->boolean('remember'));

        Session::forget('login_verification');

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        $email = Auth::check() ? Auth::user()->email : null;

        if ($email) {
            $lockoutKey = "login_lockout:" . strtolower(trim($email));
            $attemptsKey = "login_attempts:" . strtolower(trim($email));
            Cache::forget($lockoutKey);
            Cache::forget($attemptsKey);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}