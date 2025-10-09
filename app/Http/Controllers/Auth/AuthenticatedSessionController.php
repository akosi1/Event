    <?php

    namespace App\Http\Controllers\Auth;

    use App\Http\Controllers\Controller;
    use App\Http\Requests\Auth\LoginRequest;
    use App\Rules\Recaptcha;
    use App\Services\LoginAttemptService;
    use Illuminate\Http\RedirectResponse;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Validation\ValidationException;
    use Illuminate\View\View;

    class AuthenticatedSessionController extends Controller
    {
        protected LoginAttemptService $loginAttemptService;

        public function __construct(LoginAttemptService $loginAttemptService)
        {
            $this->loginAttemptService = $loginAttemptService;
        }

        public function create(): View
        {
            return view('auth.login');
        }

        public function store(LoginRequest $request): RedirectResponse
        {
            $identifier = $request->input('email') . '|' . $request->ip();

            if ($this->loginAttemptService->isLockedOut($identifier)) {
                $remainingTime = $this->loginAttemptService->getRemainingLockoutTime($identifier);
                $lockoutEnd = $this->loginAttemptService->getLockoutEndTimestamp($identifier);

                return back()->withErrors([
                    'locked_out' => true,
                    'seconds' => $remainingTime,
                    'lockout_end' => $lockoutEnd,
                    'message' => "Too many login attempts. Please try again in {$remainingTime} seconds."
                ])->withInput($request->only('email', 'department'));
            }

            $request->validate([
                'g-recaptcha-response' => ['required', new Recaptcha()],
                'department' => ['required', 'in:BSIT,BSBA,BSEd,BEED,BSHM'],
            ], [
                'g-recaptcha-response.required' => 'Please complete the reCAPTCHA verification.',
            ]);

            try {
                $this->authenticateWithDepartment($request);
                $this->loginAttemptService->clear($identifier);
                $request->session()->regenerate();

                return redirect()->intended('/dashboard');

            } catch (ValidationException $e) {
                $this->loginAttemptService->increment($identifier);
                $remaining = $this->loginAttemptService->getRemainingAttempts($identifier);

                if ($remaining === 0) {
                    $lockoutTime = $this->loginAttemptService->getRemainingLockoutTime($identifier);
                    $lockoutEnd = $this->loginAttemptService->getLockoutEndTimestamp($identifier);

                    return back()->withErrors([
                        'locked_out' => true,
                        'seconds' => $lockoutTime,
                        'lockout_end' => $lockoutEnd,
                        'message' => "Too many login attempts. Your account has been locked for {$lockoutTime} seconds."
                    ])->withInput($request->only('email', 'department'));
                }

                return back()->withErrors([
                    'failed_attempt' => true,
                    'remaining' => $remaining,
                    'message' => $remaining === 1 
                        ? "Invalid credentials or department. You have 1 attempt remaining."
                        : "Invalid credentials or department. You have {$remaining} attempts remaining."
                ])->withInput($request->only('email', 'department'));
            }
        }

        protected function authenticateWithDepartment(Request $request): void
        {
            $credentials = $request->only('email', 'password');

            if (! Auth::attempt($credentials, $request->boolean('remember'))) {
                throw ValidationException::withMessages([
                    'email' => [trans('auth.failed')],
                ]);
            }

            $user = Auth::user();
            if ($user->department !== $request->input('department')) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                throw ValidationException::withMessages([
                    'department' => ['The selected department does not match your account.'],
                ]);
            }
        }

        public function destroy(Request $request): RedirectResponse
        {
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/');
        }

        public function checkAttempts(Request $request)
        {
            $identifier = $request->input('email') . '|' . $request->ip();

            return response()->json([
                'is_locked' => $this->loginAttemptService->isLockedOut($identifier),
                'remaining_attempts' => $this->loginAttemptService->getRemainingAttempts($identifier),
                'lockout_time' => $this->loginAttemptService->getRemainingLockoutTime($identifier)
            ]);
        }
    }
