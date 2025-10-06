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

                    <div class="form-group">
                        <div class="input-wrapper">
                            <input id="email" type="email" name="email" value="{{ old('email') }}" 
                                class="form-control @error('email') input-error @enderror" placeholder=" " required autocomplete="username" autofocus>
                            <label class="input-label">Email Address</label>
                            <i class="fas fa-envelope input-icon"></i>
                        </div>
                        @error('email')
                            <div class="error-msg"><i class="fas fa-exclamation-circle"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper">
                            <input id="password" type="password" name="password" 
                                class="form-control @error('password') input-error @enderror" placeholder=" " required autocomplete="current-password">
                            <label class="input-label">Password</label>
                            <i class="fas fa-lock input-icon"></i>
                        </div>
                        @error('password')
                            <div class="error-msg"><i class="fas fa-exclamation-circle"></i>{{ $message }}</div>
                        @enderror
                    </div>

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
                                <div class="item" data-val="BSIT" data-icon="fa-laptop-code"><i class="fas fa-laptop-code"></i><span>BSIT</span></div>
                                <div class="item" data-val="BSBA" data-icon="fa-briefcase"><i class="fas fa-briefcase"></i><span>BSBA</span></div>
                                <div class="item" data-val="BSEd" data-icon="fa-chalkboard-teacher"><i class="fas fa-chalkboard-teacher"></i><span>BSED</span></div>
                                <div class="item" data-val="BEED" data-icon="fa-school"><i class="fas fa-school"></i><span>BEED</span></div>
                                <div class="item" data-val="BSHM" data-icon="fa-hotel"><i class="fas fa-hotel"></i><span>BSHM</span></div>
                            </div>
                            <input type="hidden" name="department" id="department" value="{{ old('department') }}" required>
                        </div>
                        @error('department')
                            <div class="error-msg"><i class="fas fa-exclamation-circle"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <input type="hidden" name="g-recaptcha-response" id="recaptchaResponse">

                    <button type="submit" class="btn-submit" id="submitBtn" disabled>
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
        </div>
    </div>

    <!-- Lockout Countdown Card (Top Right) -->
    <div class="lockout-card" id="lockoutCard">
        <div class="lockout-icon-circle">
            <i class="fas fa-lock"></i>
        </div>
        <h3>Account Locked</h3>
        <p>Too many failed attempts</p>
        <div class="countdown-display" id="countdownDisplay">60</div>
        <small>seconds remaining</small>
        <div class="progress-line">
            <div class="progress-fill" id="progressFill"></div>
        </div>
    </div>

    <link rel="stylesheet" href="{{ asset('user/login/login.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background: url("{{ asset('images/mcc background.jpg') }}") center/cover no-repeat;
        }

        /* Toast Styling */
        .swal2-popup.swal2-toast {
            background: white !important;
            border-left: 4px solid #e74c3c !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
            padding: 12px 16px !important;
            border-radius: 8px !important;
        }
        .swal2-popup.swal2-toast .swal2-title {
            color: #2c3e50 !important;
            font-size: 14px !important;
            font-weight: 600 !important;
            margin: 0 !important;
        }

        /* Lockout Card - Small Compact Design */
        .lockout-card {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            border-radius: 10px;
            padding: 20px;
            width: 320px;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            z-index: 9999;
            display: none;
            animation: slideInRight 0.3s ease-out;
        }
        .lockout-card.active { display: block; }

        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        .lockout-icon-circle {
            width: 70px;
            height: 70px;
            margin: 0 auto 12px;
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(231,76,60,0.25);
        }
        .lockout-icon-circle i {
            font-size: 30px;
            color: white;
        }

        .lockout-card h3 {
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
            margin: 0 0 3px 0;
        }
        .lockout-card p {
            font-size: 12px;
            color: #95a5a6;
            margin: 0 0 12px 0;
        }

        .countdown-display {
            font-size: 48px;
            font-weight: 700;
            color: #e74c3c;
            line-height: 1;
            margin-bottom: 3px;
        }
        .lockout-card small {
            font-size: 11px;
            color: #b0b0b0;
            display: block;
            margin-bottom: 12px;
        }

        .progress-line {
            width: 100%;
            height: 4px;
            background: #f0f0f0;
            border-radius: 2px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            width: 100%;
            background: linear-gradient(90deg, #e74c3c, #c0392b);
            border-radius: 2px;
            transition: width 1s linear;
        }

        /* Disabled Form State */
        .form-disabled .form-control,
        .form-disabled .dropdown-btn {
            opacity: 0.4;
            pointer-events: none;
            background: #f5f5f5 !important;
        }
        .form-disabled .btn-submit {
            opacity: 0.4;
            pointer-events: none;
            background: #95a5a6 !important;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .lockout-card {
                top: 15px;
                right: 15px;
                width: 280px;
                padding: 18px;
            }
            .lockout-icon-circle {
                width: 60px;
                height: 60px;
            }
            .lockout-icon-circle i { font-size: 26px; }
            .lockout-card h3 { font-size: 17px; }
            .countdown-display { font-size: 42px; }
        }

        @media (max-width: 480px) {
            .lockout-card {
                top: 10px;
                right: 10px;
                left: 10px;
                width: auto;
                max-width: 320px;
                margin: 0 auto;
                padding: 16px;
            }
            .lockout-icon-circle {
                width: 55px;
                height: 55px;
            }
            .lockout-icon-circle i { font-size: 24px; }
            .lockout-card h3 { font-size: 16px; }
            .countdown-display { font-size: 38px; }
        }
    </style>

    <script src="https://www.google.com/recaptcha/api.js?render={{ env('RECAPTCHA_SITE_KEY') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const deptBtn = document.getElementById('deptBtn');
            const deptMenu = document.getElementById('deptMenu');
            const deptText = document.getElementById('deptText');
            const deptInput = document.getElementById('department');
            const deptIcon = document.getElementById('deptIcon');
            const authForm = document.getElementById('authForm');
            const lockoutCard = document.getElementById('lockoutCard');
            const countdownDisplay = document.getElementById('countdownDisplay');
            const progressFill = document.getElementById('progressFill');
            const submitBtn = document.getElementById('submitBtn');
            
            let interval = null;
            const TOTAL_SECONDS = 60;

            // Department dropdown
            const oldValue = deptInput.value;
            if (oldValue) {
                document.querySelectorAll('.item').forEach(item => {
                    if (item.dataset.val === oldValue) {
                        deptText.textContent = item.dataset.val;
                        deptInput.value = item.dataset.val;
                        deptIcon.className = 'fas ' + item.dataset.icon + ' input-icon';
                        deptBtn.classList.add('has-value');
                    }
                });
            }

            deptBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                deptMenu.classList.toggle('show');
            });

            document.querySelectorAll('.item').forEach(item => {
                item.addEventListener('click', function() {
                    deptText.textContent = this.dataset.val;
                    deptInput.value = this.dataset.val;
                    deptIcon.className = 'fas ' + this.dataset.icon + ' input-icon';
                    deptBtn.classList.add('has-value');
                    deptMenu.classList.remove('show');
                });
            });

            document.addEventListener('click', (e) => {
                if (!deptBtn.contains(e.target)) deptMenu.classList.remove('show');
            });

            // Lockout countdown
            function startLockout(endTime) {
                if (interval) clearInterval(interval);
                localStorage.setItem('lockoutEnd', endTime);
                
                authForm.classList.add('form-disabled');
                lockoutCard.classList.add('active');
                submitBtn.disabled = true;

                function update() {
                    const now = Math.floor(Date.now() / 1000);
                    const remaining = endTime - now;

                    if (remaining <= 0) {
                        clearInterval(interval);
                        localStorage.removeItem('lockoutEnd');
                        authForm.classList.remove('form-disabled');
                        lockoutCard.classList.remove('active');
                        submitBtn.disabled = false;
                        
                        Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        }).fire({
                            icon: 'success',
                            title: 'Account Unlocked',
                            text: 'You can now try again.'
                        });
                        return;
                    }

                    countdownDisplay.textContent = remaining;
                    progressFill.style.width = (remaining / TOTAL_SECONDS * 100) + '%';
                }

                update();
                interval = setInterval(update, 1000);
            }

            // Check existing lockout
            const stored = localStorage.getItem('lockoutEnd');
            if (stored) {
                const end = parseInt(stored);
                if (end > Math.floor(Date.now() / 1000)) {
                    startLockout(end);
                } else {
                    localStorage.removeItem('lockoutEnd');
                }
            }

            // Toast helper
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
            });

            // Handle errors
            @if($errors->has('locked_out'))
                const lockoutEnd = {{ $errors->first('lockout_end') ?? 'null' }};
                if (lockoutEnd) startLockout(lockoutEnd);
            @elseif($errors->has('failed_attempt'))
                const remaining = {{ $errors->first('remaining') }};
                Toast.fire({
                    icon: 'warning',
                    title: 'Login Failed',
                    html: `Invalid credentials.<br><strong style="color: #e74c3c;">${remaining} ${remaining === 1 ? 'attempt' : 'attempts'}</strong> remaining.`
                });
            @endif

            // reCAPTCHA
            grecaptcha.ready(() => {
                grecaptcha.execute('{{ env("RECAPTCHA_SITE_KEY") }}', { action: 'login' })
                    .then(token => {
                        document.getElementById('recaptchaResponse').value = token;
                        submitBtn.disabled = false;
                    });
            });

            // Form submit
            document.getElementById('loginForm').addEventListener('submit', function(e) {
                e.preventDefault();
                if (!deptInput.value.trim()) {
                    Toast.fire({ icon: 'warning', title: 'Department Required', text: 'Please select your department.' });
                    return;
                }

                Toast.fire({ icon: 'info', title: 'Signing in...', text: 'Please wait', timer: null, timerProgressBar: false });

                grecaptcha.ready(() => {
                    grecaptcha.execute('{{ env("RECAPTCHA_SITE_KEY") }}', { action: 'login' })
                        .then(token => {
                            document.getElementById('recaptchaResponse').value = token;
                            this.submit();
                        })
                        .catch(() => {
                            Swal.close();
                            Toast.fire({ icon: 'error', title: 'Verification Failed', text: 'Please try again.' });
                        });
                });
            });
        });
    </script>
</x-guest-layout>