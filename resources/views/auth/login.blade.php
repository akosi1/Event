<x-guest-layout>
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('user/login_register/login_register.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

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

                    <!-- Email -->
                    <div class="form-group">
                        <div class="input-wrapper">
                            <input id="email" type="email" name="email" value="{{ old('email') }}"
                                class="form-control" placeholder=" " required autocomplete="username" autofocus>
                            <label class="input-label">Email Address</label>
                            <i class="fas fa-envelope input-icon"></i>
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="error-msg" />
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
                                <option value="BSIT" {{ old('department') == 'BSIT' ? 'selected' : '' }}>BSIT</option>
                                <option value="BSBA" {{ old('department') == 'BSBA' ? 'selected' : '' }}>BSBA</option>
                                <option value="BSED" {{ old('department') == 'BSED' ? 'selected' : '' }}>BSED</option>
                                <option value="BEED" {{ old('department') == 'BEED' ? 'selected' : '' }}>BEED</option>
                                <option value="BSHM" {{ old('department') == 'BSHM' ? 'selected' : '' }}>BSHM</option>
                            </select>
                            <label class="select-label">Select Your Department</label>
                            <i class="fas fa-graduation-cap input-icon"></i>
                        </div>
                        <x-input-error :messages="$errors->get('department')" class="error-msg" />
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
                            <a href="{{ route('register') }}">{{ __('Sign up here') }}</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- reCAPTCHA v3 Script -->
    @if(config('services.recaptcha.site_key'))
        <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                grecaptcha.ready(function () {
                    grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', { action: 'login' })
                        .then(function (token) {
                            document.getElementById('g-recaptcha-response').value = token;
                        })
                        .catch(function (error) {
                            console.error("reCAPTCHA error:", error);
                        });
                });
            });
        </script>
    @else
        <script>
            console.warn('⚠️ Google reCAPTCHA site key is not configured in services.php or .env');
        </script>
    @endif

    <!-- Custom JS -->
    <script src="{{ asset('user/login_register/login_register.js') }}"></script>
</x-guest-layout>   