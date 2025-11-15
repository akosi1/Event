<x-guest-layout>
    <link rel="stylesheet" href="{{ asset('user/auth/auth.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="preload" as="image" href="{{ asset('images/mcc background.jpg') }}">

    <style>
        * {
            margin: 0;
            padding: 0;s
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            font-family: 'Poppins', 'Segoe UI', sans-serif;
        }

        body {
            background: url("{{ asset('images/mcc background.jpg') }}") center/cover no-repeat;
            margin: 0;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(74, 26, 92, 0.8), rgba(107, 44, 145, 0.8), rgba(61, 26, 120, 0.8));
            z-index: 1;
        }

        .auth-wrapper {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 2;
            opacity: 0;
            animation: fadeIn 0.6s ease-out forwards;
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }

        .countdown-display {
            font-size: 48px;
            font-weight: 700;
            color: #e74c3c;
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            margin: 20px 0;
        }

        .countdown-label {
            font-size: 12px;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: -10px;
            margin-bottom: 20px;
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
            transition: color 0.3s ease;
            z-index: 4;
            font-size: 16px;
        }

        .password-toggle:hover {
            color: #6b2c91;
        }

        .form-control:focus ~ .password-toggle {
            color: #6b2c91;
        }

        #verification_code {
            text-align: center;
            font-size: 24px;
            letter-spacing: 8px;
        }

        .form-disabled {
            pointer-events: none;
            opacity: 0.7;
        }
    </style>

    <div class="auth-wrapper">
        <div class="auth-container">
            @if (session('needs_verification'))
                <!-- VERIFICATION STEP -->
                <div class="auth-header">
                    <h1>Verify Your Identity</h1>
                    <p>We sent a 6-digit code to <strong>{{ session('login_verification.email') }}</strong></p>
                </div>
                <div class="auth-form">
                    <form method="POST" action="{{ route('login.verify') }}" id="verifyForm">
                        @csrf
                        <div class="form-group">
                            <div class="input-wrapper">
                                <input id="verification_code" type="text" name="verification_code"
                                    class="form-control @error('verification_code') input-error @enderror"
                                    placeholder=" " required maxlength="6" inputmode="numeric"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,6)"
                                    autocomplete="off" autofocus>
                                <label class="input-label">Verification Code</label>
                                <i class="fas fa-key input-icon"></i>
                            </div>
                            @error('verification_code')
                                <div class="error-msg"><i class="fas fa-exclamation-circle"></i>{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-check"></i> Verify & Sign In
                        </button>
                        <div class="form-footer">
                        <p class="text-center" style="margin-top: 15px; color: #ffffff;">
                            Didn’t receive the code? 
                            <a href="{{ route('login') }}" style="color: #ffffff; font-weight: bold; text-shadow: 0 0 3px rgba(0,0,0,0.6);">
                                Go back to resend
                            </a>
                        </p>
                            <div class="back-link">
                                <a href="{{ url('/') }}" class="btn-secondary">Back to main page</a>
                            </div>
                        </div>
                    </form>
                </div>
            @else
            
                <!-- ORIGINAL LOGIN FORM WITH 3-ATTEMPT LOCKOUT -->
                <div class="auth-header">
                    <h1>Welcome Back</h1>
                    <p>Sign in to your E&amp;P-O account</p>
                </div>
                <div class="auth-form" id="authForm">
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('login.store') }}" id="loginForm">
                        @csrf

                        {{-- Email Field --}}
                        <div class="form-group">
                            <div class="input-wrapper">
                                <input id="email" type="email" name="email" value="{{ old('email') }}"
                                    class="form-control @error('email') input-error @enderror" placeholder=" " required
                                    autocomplete="username" autofocus oninput="this.value = this.value.replace(/\s+/g, '')"
                                    onblur="this.value = this.value.trim()">
                                <label class="input-label">Email Address</label>
                                <i class="fas fa-envelope input-icon"></i>
                            </div>
                            @error('email')
                                <div class="error-msg"><i class="fas fa-exclamation-circle"></i>{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Password Field --}}
                        <div class="form-group">
                            <div class="input-wrapper">
                                <input id="password" type="password" name="password"
                                    class="form-control @error('password') input-error @enderror" placeholder=" " required
                                    autocomplete="current-password" oninput="this.value = this.value.replace(/\s+/g, '')"
                                    onblur="this.value = this.value.trim()">
                                <label class="input-label">Password</label>
                                <i class="fas fa-lock input-icon"></i>
                                <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                            </div>
                            @error('password')
                                <div class="error-msg"><i class="fas fa-exclamation-circle"></i>{{ $message }}</div>
                            @enderror
                        </div>

                        <input type="hidden" name="g-recaptcha-response" id="recaptchaResponse">

                        <button type="submit" class="btn-submit" id="submitBtn">
                            <i class="fas fa-sign-in-alt"></i> Sign in
                        </button>

                        <div class="form-footer">
                            <div class="forgot-password">
                                <a href="{{ route('password.request') }}"><i class="fas fa-key"></i> Forgot your password?</a>
                            </div>
                            <div class="signup-link">
                                <a href="{{ route('ms365.verify') }}" class="btn-secondary">Create account with ms365 email</a>
                            </div>
                            <div class="divider"><span>or</span></div>
                            <div class="back-link">
                                <a href="{{ url('/') }}" class="btn-secondary">Back to main page</a>
                            </div>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://www.google.com/recaptcha/api.js?render={{ env('RECAPTCHA_SITE_KEY') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    this.classList.toggle('fa-eye');
                    this.classList.toggle('fa-eye-slash');
                });
            }

            // Only initialize lockout logic on the login form (not verification)
            const authForm = document.getElementById('authForm');
            const submitBtn = document.getElementById('submitBtn');
            const loginForm = document.getElementById('loginForm');
            let interval = null;
            let isSubmitting = false;

            // Lockout popup
            const lockoutPopup = document.createElement('div');
            lockoutPopup.className = 'popup-vertical';
            lockoutPopup.id = 'lockoutPopup';
            lockoutPopup.innerHTML = `
                <div class="icon-circle"><i class="fas fa-lock"></i></div>
                <div class="content-area">
                    <h3>Account Locked</h3>
                    <p>Too many failed attempts</p>
                </div>
                <div class="countdown-display" id="countdownDisplay">0:00</div>
                <div class="countdown-label">Time Remaining</div>
                <div class="progress-line"><div class="progress-fill" id="progressFill"></div></div>`;
            document.body.appendChild(lockoutPopup);

            const formatTime = (s) => `${Math.floor(s/60)}:${(s%60).toString().padStart(2,'0')}`;

            function startLockout(endTime) {
                if (interval) clearInterval(interval);
                localStorage.setItem('lockoutEnd', endTime);
                if (authForm) authForm.classList.add('form-disabled');
                lockoutPopup.classList.add('active');
                if (submitBtn) submitBtn.disabled = true;

                const countdownEl = document.getElementById('countdownDisplay');
                const progressEl = document.getElementById('progressFill');
                const total = endTime - Math.floor(Date.now() / 1000);

                function update() {
                    const now = Math.floor(Date.now() / 1000);
                    const remain = endTime - now;
                    if (remain <= 0) {
                        clearInterval(interval);
                        localStorage.removeItem('lockoutEnd');
                        if (authForm) authForm.classList.remove('form-disabled');
                        lockoutPopup.classList.remove('active');
                        if (submitBtn) submitBtn.disabled = false;
                        showToast('success', 'Account Unlocked', 'You can now log in again.');
                        return;
                    }
                    countdownEl.textContent = formatTime(remain);
                    progressEl.style.width = `${(remain / total) * 100}%`;
                }
                update();
                interval = setInterval(update, 1000);
            }

            // Resume lockout if active
            const stored = localStorage.getItem('lockoutEnd');
            if (stored && parseInt(stored) > Math.floor(Date.now() / 1000)) {
                startLockout(parseInt(stored));
            }

            // Toast helper
            function showToast(type, title, text) {
                const toast = document.createElement('div');
                toast.className = 'popup-vertical ' + type;
                toast.innerHTML = `
                    <div class="icon-circle">
                        <i class="fas ${type==='success'?'fa-check':type==='warning'?'fa-exclamation-triangle':'fa-times'}"></i>
                    </div>
                    <div class="content-area"><h3>${title}</h3><p>${text}</p></div>`;
                document.body.appendChild(toast);
                setTimeout(() => toast.classList.add('active'), 10);
                setTimeout(() => {
                    toast.classList.remove('active');
                    setTimeout(() => toast.remove(), 400);
                }, 3000);
            }

            // Handle backend errors (lockout or remaining attempts)
            @if ($errors->has('locked_out'))
                const lockoutEnd = {{ $errors->first('lockout_end') }};
                if (lockoutEnd) startLockout(lockoutEnd);
            @elseif ($errors->has('failed_attempt'))
                const remaining = {{ $errors->first('remaining') }};
                showToast('warning', 'Login Failed',
                    `${remaining} ${remaining === 1 ? 'attempt' : 'attempts'} remaining`);
            @endif

            // Submit login form with reCAPTCHA
            if (loginForm) {
                loginForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (isSubmitting) return;
                    isSubmitting = true;
                    if (submitBtn) submitBtn.disabled = true;

                    const loadingPopup = document.createElement('div');
                    loadingPopup.className = 'popup-vertical';
                    loadingPopup.innerHTML = `
                        <div class="icon-circle"><i class="fas fa-spinner fa-spin"></i></div>
                        <div class="content-area">
                            <h3>Please Wait</h3>
                            <div class="loading-text"><i class="fas fa-spinner fa-spin"></i> Signing in...</div>
                        </div>`;
                    document.body.appendChild(loadingPopup);
                    loadingPopup.classList.add('active');

                    grecaptcha.ready(() => {
                        grecaptcha.execute('{{ env('RECAPTCHA_SITE_KEY') }}', { action: 'login' })
                            .then(token => {
                                document.getElementById('recaptchaResponse').value = token;
                                loginForm.submit();
                            })
                            .catch(() => {
                                loadingPopup.remove();
                                if (submitBtn) submitBtn.disabled = false;
                                isSubmitting = false;
                                showToast('error', 'Verification Failed', 'Please try again.');
                            });
                    });
                });
            }
        });
    </script>
</x-guest-layout>