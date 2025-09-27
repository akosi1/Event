<x-guest-layout>
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('user/login_register/login_register.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    @if(config('services.recaptcha.site_key'))
        <meta name="recaptcha-key" content="{{ config('services.recaptcha.site_key') }}">
    @endif

    <div class="auth-container">
        <div class="floating-particles" id="particles"></div>
        <div class="diagonal-section"></div>

        <!-- Welcome Section -->
        <div class="welcome-section">
            <h1 id="welcomeTitle">WELCOME BACK!</h1>
            <p id="welcomeText">Please sign in to continue</p>
        </div>

        <!-- Login Form Section -->
        <div class="form-section">
            <div class="form-content">
                <div class="form-title">Sign In</div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" id="login-form">
                    @csrf

                    <!-- MS365 Email Only -->
                    <div class="form-group">
                        <div class="input-wrapper">
                            <input id="email" type="email" name="email" value="{{ old('email') }}"
                                class="form-control" placeholder=" " required autocomplete="username" autofocus>
                            <label class="input-label">MS365 Email Address</label>
                            <i class="fas fa-envelope input-icon"></i>
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="error-msg" />
                        <div class="input-hint">
                            <small>Enter your MS365 email (e.g., firstname.lastname@mcclawis.edu.ph)</small>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <div class="input-wrapper">
                            <input id="password" type="password" name="password"
                                class="form-control" placeholder=" " required autocomplete="current-password">
                            <label class="input-label">Password</label>
                            <i class="fas fa-lock input-icon"></i>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="error-msg" />
                    </div>

                    <!-- Department -->
                    <div class="form-group">
                        <div class="input-wrapper">
                            <select id="department" name="department" class="form-select" required>
                                <option value=""></option>
                                <option value="BSIT" {{ old('department') == 'BSIT' ? 'selected' : '' }}>BSIT - Information Technology</option>
                                <option value="BSBA" {{ old('department') == 'BSBA' ? 'selected' : '' }}>BSBA - Business Administration</option>
                                <option value="BSED" {{ old('department') == 'BSED' ? 'selected' : '' }}>BSED - Secondary Education</option>
                                <option value="BEED" {{ old('department') == 'BEED' ? 'selected' : '' }}>BEED - Elementary Education</option>
                                <option value="BSHM" {{ old('department') == 'BSHM' ? 'selected' : '' }}>BSHM - Hospitality Management</option>
                            </select>
                            <label class="select-label">Select Your Department</label>
                            <i class="fas fa-graduation-cap input-icon"></i>
                        </div>
                        <x-input-error :messages="$errors->get('department')" class="error-msg" />
                    </div>

                    <!-- Remember Me -->
                    <div class="form-group">
                        <label class="remember-checkbox">
                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <span class="checkmark"></span>
                            <span class="remember-text">{{ __('Remember me') }}</span>
                        </label>
                    </div>

                    <!-- reCAPTCHA v3 hidden token -->
                    <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

                    <!-- Submit Button -->
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-sign-in-alt"></i>
                        {{ __('Sign In') }}
                    </button>

                    <div class="auth-links">
                        <p>{{ __("Don't have an account?") }}
                            <a href="{{ route('register') }}" class="auth-link-primary">{{ __('Sign up here') }}</a>
                        </p>
                        <p>
                            <a href="{{ route('password.request') }}" class="auth-link-secondary">{{ __('Forgot your password?') }}</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- reCAPTCHA v3 CDN -->
    @if(config('services.recaptcha.site_key'))
        <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
        <script src="{{ asset('user/v3/recapcha.js') }}"></script>
    @else
        <script>
            console.warn('Google reCAPTCHA site key is not configured in services.php or .env');
        </script>
    @endif

    <!-- Custom JS -->
    <script src="{{ asset('user/login_register/login_register.js') }}"></script>

    <style>
        .input-hint {
            margin-top: 4px;
            color: rgba(255, 255, 255, 0.5);
            font-size: 11px;
        }

        .remember-checkbox {
            display: flex;
            align-items: center;
            color: rgba(255, 255, 255, 0.8);
            font-size: 13px;
            cursor: pointer;
            margin: 10px 0;
        }

        .remember-checkbox input[type="checkbox"] {
            display: none;
        }

        .checkmark {
            width: 18px;
            height: 18px;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 4px;
            margin-right: 10px;
            position: relative;
            transition: all 0.3s ease;
        }

        .remember-checkbox input[type="checkbox"]:checked + .checkmark {
            background: #dc2626;
            border-color: #dc2626;
        }

        .remember-checkbox input[type="checkbox"]:checked + .checkmark::after {
            content: '\2713';
            position: absolute;
            top: -2px;
            left: 2px;
            color: white;
            font-size: 14px;
            font-weight: bold;
        }

        .auth-link-primary {
            color: #ef4444 !important;
            font-weight: 700 !important;
            text-decoration: underline !important;
        }

        .auth-link-secondary {
            color: #dc2626 !important;
            font-weight: 500 !important;
        }
    </style>
    
</x-guest-layout>