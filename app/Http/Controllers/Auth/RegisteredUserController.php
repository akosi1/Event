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
    use Illuminate\Support\Facades\Log;
    use Illuminate\Validation\Rules;
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
            $otpVerified = session('otp_verified');

            Log::info('Registration page accessed', [
                'verified_email' => $verifiedEmail,
                'otp_verified' => $otpVerified,
                'session_data' => session()->all(),
            ]);

            if (!$verifiedEmail || !$otpVerified) {
                Log::warning('Registration access denied - email not verified');
                return redirect()->route('ms365.verify')
                    ->withErrors(['email' => 'Please verify your McLawis College email first.']);
            }

            $otpRecord = OtpVerification::where('email', $verifiedEmail)
                ->whereNotNull('verified_at')
                ->where('verified_at', '>=', Carbon::now()->subHour())
                ->first();

            if (!$otpRecord) {
                Log::warning('OTP verification expired or not found', ['email' => $verifiedEmail]);
                session()->forget(['verified_email', 'email', 'otp_verified', 'email_verified']);
                return redirect()->route('ms365.verify')
                    ->withErrors(['email' => 'Email verification has expired. Please verify again.']);
            }

            return view('auth.register');
        }

        /**
         * Handle an incoming registration request.
         *
         * @throws \Illuminate\Validation\ValidationException
         */
        public function store(Request $request): RedirectResponse
        {
            $verifiedEmail = session('verified_email');
            $otpVerified = session('otp_verified');

            Log::info('Registration attempt', [
                'verified_email' => $verifiedEmail,
                'otp_verified' => $otpVerified,
                'request_email' => $request->email,
                'request_data' => $request->except('password', 'password_confirmation'),
            ]);

            if (!$verifiedEmail || !$otpVerified) {
                Log::warning('Registration denied - email not verified in session');
                return redirect()->route('ms365.verify')
                    ->withErrors(['email' => 'Please verify your McLawis College email first.']);
            }

            $otpRecord = OtpVerification::where('email', $verifiedEmail)
                ->whereNotNull('verified_at')
                ->where('verified_at', '>=', Carbon::now()->subHour())
                ->first();

            if (!$otpRecord) {
                Log::warning('OTP record expired during registration', ['email' => $verifiedEmail]);
                session()->forget(['verified_email', 'email', 'otp_verified', 'email_verified']);
                return redirect()->route('ms365.verify')
                    ->withErrors(['email' => 'Email verification has expired. Please verify again.']);
            }

            // ✅ Step 1: Sanitize ALL text inputs (trim + remove internal spaces)
            $inputs = $request->only([
                'id_number',
                'first_name',
                'middle_name',
                'last_name',
                'email',
                'department',
                'password',
                'password_confirmation'
            ]);

            // Sanitize text fields: trim and enforce no spaces
            $sanitized = [];
            foreach (['id_number', 'first_name', 'middle_name', 'last_name'] as $field) {
                $value = trim($inputs[$field] ?? '');
                if ($field !== 'middle_name' || $value !== '') { // middle_name can be null
                    if (preg_match('/\s/', $inputs[$field] ?? '')) {
                        return back()
                            ->withInput($request->except('password', 'password_confirmation'))
                            ->withErrors([$field => ucfirst(str_replace('_', ' ', $field)) . ' must not contain spaces.']);
                    }
                    $sanitized[$field] = $value;
                } else {
                    $sanitized[$field] = null;
                }
            }

            // Password: enforce no spaces
            $password = trim($inputs['password'] ?? '');
            $passwordConfirmation = trim($inputs['password_confirmation'] ?? '');

            if (preg_match('/\s/', $inputs['password'] ?? '') || preg_match('/\s/', $inputs['password_confirmation'] ?? '')) {
                return back()
                    ->withInput($request->except('password', 'password_confirmation'))
                    ->withErrors(['password' => 'Password must not contain spaces.']);
            }

            // Email: must match verified email (case-insensitive)
            $requestEmail = trim($inputs['email'] ?? '');
            if (strtolower($requestEmail) !== strtolower($verifiedEmail)) {
                return back()
                    ->withInput($request->except('password', 'password_confirmation'))
                    ->withErrors(['email' => 'The email must match your verified McLawis College email.']);
            }

            // Department: allow only predefined values
            $allowedDepts = ['BSIT', 'BSBA', 'BSED', 'BEED', 'BSHM'];
            if (!in_array($inputs['department'], $allowedDepts)) {
                return back()
                    ->withInput($request->except('password', 'password_confirmation'))
                    ->withErrors(['department' => 'Invalid department selected.']);
            }

            // ✅ Step 2: Validate with sanitized data
            $request->merge(array_merge($sanitized, [
                'email' => $verifiedEmail,
                'department' => $inputs['department'],
                'password' => $password,
                'password_confirmation' => $passwordConfirmation,
            ]));

            $validated = $request->validate([
                'id_number' => [
                    'required',
                    'string',
                    'max:255',
                    'unique:' . User::class,
                    'regex:/^\S*$/',
                ],
                'first_name' => [
                    'required',
                    'string',
                    'max:255',
                    'regex:/^\S*$/',
                ],
                'middle_name' => [
                    'nullable',
                    'string',
                    'max:255',
                    'regex:/^\S*$/',
                ],
                'last_name' => [
                    'required',
                    'string',
                    'max:255',
                    'regex:/^\S*$/',
                ],
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    'unique:' . User::class,
                ],
                'department' => [
                    'required',
                    'string',
                    'in:' . implode(',', $allowedDepts),
                ],
                'password' => [
                    'required',
                    'confirmed',
                    'regex:/^\S*$/',
                    Rules\Password::defaults(),
                ],
            ]);

            try {
                $user = User::create([
                    'id_number' => $validated['id_number'],
                    'first_name' => $validated['first_name'],
                    'middle_name' => $validated['middle_name'] ?? null,
                    'last_name' => $validated['last_name'],
                    'email' => $verifiedEmail, // Enforce verified email
                    'department' => $validated['department'],
                    'password' => Hash::make($validated['password']),
                    'role' => 'user',
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]);

                Log::info('User registered successfully', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);

                $otpRecord->delete();

                event(new Registered($user));
                Auth::login($user);

                session()->forget([
                    'verified_email',
                    'email',
                    'otp_verified',
                    'email_verified',
                    'ms365_verification_started',
                ]);

                return redirect()->route('dashboard')
                    ->with('success', 'Registration completed successfully! Welcome to EventAps.');

            } catch (\Exception $e) {
                Log::error('Registration failed', [
                    'error' => $e->getMessage(),
                    'email' => $verifiedEmail,
                    'trace' => $e->getTraceAsString(),
                ]);

                return back()
                    ->withInput($request->except('password', 'password_confirmation'))
                    ->withErrors(['error' => 'Registration failed. Please try again.']);
            }
        }
    }