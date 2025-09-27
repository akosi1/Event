<x-guest-layout>
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('user/login_register/login_register.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    @if(config('services.recaptcha.site_key'))
        <meta name="recaptcha-key" content="{{ config('services.recaptcha.site_key') }}">
    @endif

    <div class="auth-container register-mode">
        <div class="floating-particles" id="particles"></div>
        <div class="diagonal-section register-mode"></div>

        <!-- Welcome Section -->
        <div class="welcome-section register-mode">
            <h1 id="welcomeTitle">WELCOME!</h1>
            <p id="welcomeText">Create your student account</p>
        </div>

        <!-- Register Form Section -->
        <div class="form-section register-mode">
            <div class="form-content">
                <div class="form-title">Student Registration</div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('register') }}" id="register-form">
                    @csrf

                    <!-- Name Fields Row -->
                    <div class="form-row">
                        <div class="form-group">
                            <div class="input-wrapper">
                                <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}"
                                    class="form-control" placeholder=" " required autofocus>
                                <label class="input-label">First Name</label>
                                <i class="fas fa-user input-icon"></i>
                            </div>
                            <x-input-error :messages="$errors->get('first_name')" class="error-msg" />
                        </div>

                        <div class="form-group">
                            <div class="input-wrapper">
                                <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}"
                                    class="form-control" placeholder=" " required>
                                <label class="input-label">Last Name</label>
                                <i class="fas fa-user input-icon"></i>
                            </div>
                            <x-input-error :messages="$errors->get('last_name')" class="error-msg" />
                        </div>
                    </div>

                    <!-- Middle Name -->
                    <div class="form-group">
                        <div class="input-wrapper">
                            <input id="middle_name" type="text" name="middle_name" value="{{ old('middle_name') }}"
                                class="form-control" placeholder=" ">
                            <label class="input-label">Middle Name (Optional)</label>
                            <i class="fas fa-user input-icon"></i>
                        </div>
                        <x-input-error :messages="$errors->get('middle_name')" class="error-msg" />
                    </div>

                    <!-- Student ID -->
                    <div class="form-group">
                        <div class="input-wrapper">
                            <input id="student_id" type="text" name="student_id" value="{{ old('student_id') }}"
                                class="form-control" placeholder=" " required>
                            <label class="input-label">Student ID Number</label>
                            <i class="fas fa-id-card input-icon"></i>
                        </div>
                        <x-input-error :messages="$errors->get('student_id')" class="error-msg" />
                        <div class="input-hint">
                            <small>Enter your official student ID number</small>
                        </div>
                    </div>

                    <!-- Department and Year Level Row -->
                    <div class="form-row">
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
                                <label class="select-label">Department</label>
                                <i class="fas fa-graduation-cap input-icon"></i>
                            </div>
                            <x-input-error :messages="$errors->get('department')" class="error-msg" />
                        </div>

                        <div class="form-group">
                            <div class="input-wrapper">
                                <select id="year_level" name="year_level" class="form-select" required>
                                    <option value=""></option>
                                    <option value="1" {{ old('year_level') == '1' ? 'selected' : '' }}>1st Year</option>
                                    <option value="2" {{ old('year_level') == '2' ? 'selected' : '' }}>2nd Year</option>
                                    <option value="3" {{ old('year_level') == '3' ? 'selected' : '' }}>3rd Year</option>
                                    <option value="4" {{ old('year_level') == '4' ? 'selected' : '' }}>4th Year</option>
                                </select>
                                <label class="select-label">Year Level</label>
                                <i class="fas fa-calendar-alt input-icon"></i>
                            </div>
                            <x-input-error :messages="$errors->get('year_level')" class="error-msg" />
                        </div>
                    </div>

                    <!-- Password Fields Row -->
                    <div class="form-row">
                        <div class="form-group">
                            <div class="input-wrapper">
                                <input id="password" type="password" name="password"
                                    class="form-control" placeholder=" " required>
                                <label class="input-label">Password</label>
                                <i class="fas fa-lock input-icon"></i>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="error-msg" />
                        </div>

                        <div class="form-group">
                            <div class="input-wrapper">
                                <input id="password_confirmation" type="password" name="password_confirmation"
                                    class="form-control" placeholder=" " required>
                                <label class="input-label">Confirm Password</label>
                                <i class="fas fa-lock input-icon"></i>
                            </div>
                            <x-input-error :messages="$errors->get('password_confirmation')" class="error-msg" />
                        </div>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="form-group">
                        <label class="terms-checkbox">
                            <input type="checkbox" name="terms" required>
                            <span class="checkmark"></span>
                            <span class="terms-text">
                                I agree to the <a href="#" class="terms-link">Terms and Conditions</a> and <a href="#" class="terms-link">Privacy Policy</a>
                            </span>
                        </label>
                        <x-input-error :messages="$errors->get('terms')" class="error-msg" />
                    </div>

                    <!-- reCAPTCHA v3 hidden token -->
                    <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

                    <!-- Submit Button -->
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-user-plus"></i>
                        {{ __('Create Account') }}
                    </button>

                    <div class="auth-links">
                        <p>{{ __("Already have an account?") }}
                            <a href="{{ route('login') }}" class="auth-link-primary">{{ __('Sign in here') }}</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- reCAPTCHA v3 CDN and custom logic -->
    @if(config('services.recaptcha.site_key'))
        <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
        <script src="{{ asset('user/v3/recapcha.js') }}"></script>
    @endif

    <!-- Custom JS -->
    <script src="{{ asset('user/login_register/login_register.js') }}"></script>

    <style>
        .input-hint {
            margin-top: 4px;
            color: rgba(255, 255, 255, 0.5);
            font-size: 11px;
        }

        .form-row .form-group:first-child {
            margin-right: 5px;
        }

        .form-row .form-group:last-child {
            margin-left: 5px;
        }

        .terms-checkbox {
            display: flex;
            align-items: flex-start;
            color: rgba(255, 255, 255, 0.8);
            font-size: 12px;
            cursor: pointer;
            margin: 10px 0;
            line-height: 1.4;
        }

        .terms-checkbox input[type="checkbox"] {
            display: none;
        }

        .checkmark {
            width: 16px;
            height: 16px;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 4px;
            margin-right: 8px;
            margin-top: 2px;
            position: relative;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .terms-checkbox input[type="checkbox"]:checked + .checkmark {
            background: #dc2626;
            border-color: #dc2626;
        }

        .terms-checkbox input[type="checkbox"]:checked + .checkmark::after {
            content: '\2713';
            position: absolute;
            top: -2px;
            left: 1px;
            color: white;
            font-size: 12px;
            font-weight: bold;
        }

        .terms-text {
            flex: 1;
        }

        .terms-link {
            color: #ef4444 !important;
            text-decoration: underline !important;
            font-weight: 600 !important;
        }

        .terms-link:hover {
            color: #ffffff !important;
            text-shadow: 0 0 10px rgba(239, 68, 68, 0.8) !important;
        }

        .auth-link-primary {
            color: #ef4444 !important;
            font-weight: 700 !important;
            text-decoration: underline !important;
        }

        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }

            .form-row .form-group:first-child,
            .form-row .form-group:last-child {
                margin: 0;
            }

            .form-row .form-group {
                margin-bottom: 16px;
            }
        }
    </style>
</x-guest-layout>