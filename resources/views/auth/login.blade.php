<x-guest-layout>
    <div class="auth-wrapper">
        <div class="auth-container">
            <div class="auth-header">
                <h1>Welcome Back</h1>
                <p>Sign in to your E&P-O account</p>
            </div>
            <div class="auth-form" id="authForm">
                <x-auth-session-status class="mb-4" :status="session('status')" />
                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf

                    <!-- Email -->
                    <div class="form-group">
                        <div class="input-wrapper">
                            <input id="email" type="email" name="email" value="{{ old('email') }}" 
                                class="form-control @error('email') input-error @enderror" placeholder=" " required autocomplete="username" autofocus>
                            <label class="input-label">Email Address</label>
                            <i class="fas fa-envelope input-icon"></i>
                        </div>
                        @error('email')
                            <div class="error-msg">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <div class="input-wrapper">
                            <input id="password" type="password" name="password" 
                                class="form-control @error('password') input-error @enderror" placeholder=" " required autocomplete="current-password">
                            <label class="input-label">Password</label>
                            <i class="fas fa-lock input-icon"></i>
                        </div>
                        @error('password')
                            <div class="error-msg">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Department Dropdown -->
                    <div class="form-group">
                        <div class="dropdown-wrapper">
                            <div class="dropdown-btn" id="deptBtn">
                                <div class="dropdown-content">
                                    <i class="fas fa-graduation-cap input-icon" id="deptIcon"></i>
                                    <label class="select-label" id="deptLabel">Select Your Department</label>
                                    <span id="deptText"></span>
                                </div>
                                <i class="fas fa-chevron-down arrow" id="deptArrow"></i>
                            </div>
                            <div class="menu" id="deptMenu">
                                <div class="item" data-val="BSIT" data-full="Bachelor of Science in Information Technology" data-icon="fa-laptop-code">
                                    <i class="fas fa-laptop-code"></i><span>BSIT</span>
                                </div>
                                <div class="item" data-val="BSBA" data-full="Bachelor of Science in Business Administration" data-icon="fa-briefcase">
                                    <i class="fas fa-briefcase"></i><span>BSBA</span>
                                </div>
                                <div class="item" data-val="BSEd" data-full="Bachelor of Science in Education" data-icon="fa-chalkboard-teacher">
                                    <i class="fas fa-chalkboard-teacher"></i><span>BSED</span>
                                </div>
                                <div class="item" data-val="BEEd" data-full="Bachelor of Elementary Education" data-icon="fa-school">
                                    <i class="fas fa-school"></i><span>BEED</span>
                                </div>
                                <div class="item" data-val="BSHM" data-full="Bachelor of Science in Hospitality Management" data-icon="fa-hotel">
                                    <i class="fas fa-hotel"></i><span>BSHM</span>
                                </div>
                            </div>
                            <input type="hidden" name="department" id="department" value="{{ old('department') }}" required>
                        </div>
                        @error('department')
                            <div class="error-msg">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Hidden reCAPTCHA v3 Token Field -->
                    <input type="hidden" name="g-recaptcha-response" id="recaptchaResponse">

                    <!-- Submit Button -->
                    <button type="submit" class="btn-submit" id="submitBtn" disabled>
                        <i class="fas fa-sign-in-alt"></i>
                        Sign in
                    </button>

                    <div class="form-footer">
                        <div class="forgot-password">
                            <a href="{{ route('password.request') }}">
                                <i class="fas fa-key"></i>
                                Forgot your password?
                            </a>
                        </div>
                        <div class="signup-link">
                            <a href="{{ route('ms365.verify') }}" class="btn-secondary">
                                Create account with ms365 email
                            </a>
                        </div>
                        <div class="divider">
                            <span>or</span>
                        </div>
                        <div class="back-link">
                            <a href="{{ url('/') }}" class="btn-secondary">
                                Back to main page
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="{{ asset('user/login/login.css') }}">
    <style>
        body {
            background: url("{{ asset('images/mcc background.jpg') }}") center/cover no-repeat;
            position: relative;
        }
    </style>

    <!-- Google reCAPTCHA v3 Script -->
    <script src="https://www.google.com/recaptcha/api.js?render={{ env('RECAPTCHA_SITE_KEY') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const deptBtn = document.getElementById('deptBtn');
            const deptMenu = document.getElementById('deptMenu');
            const deptText = document.getElementById('deptText');
            const deptInput = document.getElementById('department');
            const deptArrow = document.getElementById('deptArrow');
            const deptIcon = document.getElementById('deptIcon');
            const items = document.querySelectorAll('.item');

            // Restore old department selection
            const oldValue = deptInput.value;
            if (oldValue) {
                items.forEach(item => {
                    if (item.dataset.val === oldValue || item.dataset.full === oldValue) {
                        deptText.textContent = item.dataset.val;
                        deptInput.value = item.dataset.val;
                        deptIcon.className = 'fas ' + item.dataset.icon + ' input-icon';
                        deptBtn.classList.add('has-value');
                    }
                });
            }

            deptBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                deptMenu.classList.toggle('show');
                deptBtn.classList.toggle('active');
            });

            items.forEach(item => {
                item.addEventListener('click', function () {
                    const val = this.dataset.val;
                    const icon = this.dataset.icon;
                    deptText.textContent = val;
                    deptInput.value = val;
                    deptIcon.className = 'fas ' + icon + ' input-icon';
                    deptBtn.classList.add('has-value');
                    deptMenu.classList.remove('show');
                    deptBtn.classList.remove('active');
                });
            });

            document.addEventListener('click', function () {
                deptMenu.classList.remove('show');
                deptBtn.classList.remove('active');
            });

            // ===== reCAPTCHA v3 Integration =====
            const submitBtn = document.getElementById('submitBtn');
            const recaptchaResponseInput = document.getElementById('recaptchaResponse');
            const loginForm = document.getElementById('loginForm');

            // Initialize reCAPTCHA and get token
            grecaptcha.ready(function () {
                // Get initial token on page load
                grecaptcha.execute('{{ env("RECAPTCHA_SITE_KEY") }}', { action: 'login' })
                    .then(function (token) {
                        recaptchaResponseInput.value = token;
                        submitBtn.disabled = false; // Enable button
                    })
                    .catch(function () {
                        console.error('reCAPTCHA v3 failed to load.');
                    });
            });

            // On form submit: get FRESH token (best practice)
            loginForm.addEventListener('submit', function (e) {
                e.preventDefault();

                grecaptcha.ready(function () {
                    grecaptcha.execute('{{ env("RECAPTCHA_SITE_KEY") }}', { action: 'login' })
                        .then(function (token) {
                            recaptchaResponseInput.value = token;
                            loginForm.submit();
                        })
                        .catch(function () {
                            alert('reCAPTCHA verification failed. Please try again.');
                        });
                });
            });
        });
    </script>
</x-guest-layout>