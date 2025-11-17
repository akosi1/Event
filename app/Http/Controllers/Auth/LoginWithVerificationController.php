<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use App\Helpers\IpGeolocation;
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
            
            // Log lockout attempt
            $this->logLoginAttempt($email, null, 'locked_out');
            
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

            // Log failed attempt
            $this->logLoginAttempt($email, null, 'failed');

            if ($attempts >= $this->maxAttempts) {
                // Lock out user
                $lockoutEnd = now()->addSeconds($this->lockoutDuration)->timestamp;
                Cache::put($lockoutKey, $lockoutEnd, $this->lockoutDuration);
                Cache::forget($attemptsKey);

                // Log lockout
                $this->logLoginAttempt($email, null, 'locked_out');

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

        // Send email using the Blade template
        Mail::send('emails.email-verify', ['otp' => $code], function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Login Verification Code - MCC Event & Portfolio Organizer')
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

        // Log successful login with login_at timestamp
        $loginLog = $this->logLoginAttempt($user->email, $user->id, 'success');
        
        // Store login log ID in session for logout tracking
        Session::put('current_login_log_id', $loginLog->id);

        Session::forget('login_verification');

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        $email = Auth::check() ? $user->email : null;

        // Track logout with geolocation
        if ($user) {
            $loginLogId = Session::get('current_login_log_id');
            
            if ($loginLogId) {
                $this->logLogout($loginLogId);
            }
        }

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

    /**
     * Log login attempt with IP and geolocation.
     */
    protected function logLoginAttempt(string $email, ?int $userId, string $status): ?LoginLog
    {
        try {
            $ip = IpGeolocation::getRealIp();
            $geolocation = IpGeolocation::getGeolocation($ip);
            $userAgent = IpGeolocation::getUserAgent();

            return LoginLog::create([
                'user_id' => $userId,
                'email_attempted' => $email,
                'ip_address' => $geolocation['ip'],
                'user_agent' => $userAgent,
                'status' => $status,
                'city' => $geolocation['city'],
                'region' => $geolocation['region'],
                'country' => $geolocation['country'],
                'country_code' => $geolocation['country_code'],
                'latitude' => $geolocation['latitude'],
                'longitude' => $geolocation['longitude'],
                'login_at' => $status === 'success' ? now() : null,
            ]);
        } catch (\Exception $e) {
            // Log error but don't break login flow
            \Log::error('Failed to log login attempt: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Log logout with IP and geolocation.
     */
    protected function logLogout(int $loginLogId): void
    {
        try {
            $loginLog = LoginLog::find($loginLogId);
            
            if (!$loginLog) {
                return;
            }

            $ip = IpGeolocation::getRealIp();
            $geolocation = IpGeolocation::getGeolocation($ip);
            
            $logoutTime = now();
            $sessionDuration = $loginLog->login_at ? $logoutTime->diffInSeconds($loginLog->login_at) : null;

            $loginLog->update([
                'logout_at' => $logoutTime,
                'logout_ip_address' => $geolocation['ip'],
                'logout_city' => $geolocation['city'],
                'logout_country' => $geolocation['country'],
                'logout_latitude' => $geolocation['latitude'],
                'logout_longitude' => $geolocation['longitude'],
                'session_duration' => $sessionDuration,
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to log logout: ' . $e->getMessage());
        }
    }
}