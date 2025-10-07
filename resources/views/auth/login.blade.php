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
        </div>
    </div>

    <link rel="stylesheet" href="{{ asset('user/login/login.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    body {
        background: url("{{ asset('images/mcc background.jpg') }}") center/cover no-repeat;
    }

    /* Popup Styles */
    .popup-vertical {
        position: fixed;
        top: 20px;
        right: 20px;
        background: white;
        border-radius: 12px;
        padding: 16px;
        width: 340px;
        min-height: 60px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        z-index: 9999;
        display: none;
        animation: slideInRight 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        overflow: hidden;
    }

    @keyframes slideInRight {
        from { 
            transform: translateX(120%); 
            opacity: 0; 
        }
        to { 
            transform: translateX(0); 
            opacity: 1; 
        }
    }

    .popup-vertical.active { 
        display: flex; 
        align-items: center; 
        gap: 12px; 
    }

    /* Icon Circle */
    .popup-vertical .icon-circle {
        width: 40px;
        height: 40px;
        flex-shrink: 0;
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(231,76,60,0.3);
    }

    .popup-vertical .icon-circle i {
        font-size: 18px;
        color: white;
    }

    /* Content Area */
    .popup-vertical .content-area {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 2px;
        min-width: 0;
    }

    .popup-vertical h3 {
        font-size: 14px;
        font-weight: 700;
        color: #2c3e50;
        margin: 0;
        line-height: 1.3;
    }

    .popup-vertical p {
        font-size: 12px;
        color: #7f8c8d;
        margin: 0;
        line-height: 1.3;
    }

    /* Countdown Display */
    .popup-vertical .countdown-display {
        font-size: 18px;
        font-weight: 700;
        color: #e74c3c;
        line-height: 1;
        text-align: right;
        min-width: 40px;
        flex-shrink: 0;
    }

    /* Smooth Progress Bar */
    .popup-vertical .progress-line {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: #ecf0f1;
        overflow: hidden;
    }

    .popup-vertical .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #e74c3c, #c0392b);
        width: 100%;
        transition: width 0.1s linear;
        transform-origin: left;
    }

    /* Loading Spinner */
    .popup-vertical .loading-text {
        font-size: 12px;
        color: #2c3e50;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .popup-vertical .loading-text i {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        100% { transform: rotate(360deg); }
    }

    /* Variants */
    .popup-vertical.success .icon-circle {
        background: linear-gradient(135deg, #2ecc71, #27ae60);
        box-shadow: 0 4px 12px rgba(46,204,113,0.3);
    }

    .popup-vertical.warning .icon-circle {
        background: linear-gradient(135deg, #f39c12, #e67e22);
        box-shadow: 0 4px 12px rgba(243,156,18,0.3);
    }

    .popup-vertical.success .countdown-display,
    .popup-vertical.success .progress-fill {
        background: linear-gradient(90deg, #2ecc71, #27ae60);
    }

    .popup-vertical.warning .countdown-display {
        color: #f39c12;
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

    /* Tablet View */
    @media (max-width: 768px) {
        .popup-vertical {
            width: 300px;
            padding: 14px;
            top: 15px;
            right: 15px;
        }
        
        .popup-vertical .icon-circle { 
            width: 36px; 
            height: 36px; 
        }
        
        .popup-vertical .icon-circle i { 
            font-size: 16px; 
        }
        
        .popup-vertical h3 { 
            font-size: 13px; 
        }
        
        .popup-vertical p { 
            font-size: 11px; 
        }
        
        .popup-vertical .countdown-display { 
            font-size: 16px;
            min-width: 35px;
        }
    }

    /* Mobile View */
    @media (max-width: 480px) {
        .popup-vertical {
            width: 280px;
            padding: 12px;
            top: 10px;
            right: 10px;
            min-height: 55px;
        }
        
        .popup-vertical .icon-circle { 
            width: 32px; 
            height: 32px; 
        }
        
        .popup-vertical .icon-circle i { 
            font-size: 14px; 
        }
        
        .popup-vertical h3 { 
            font-size: 12px; 
        }
        
        .popup-vertical p { 
            font-size: 10px; 
        }
        
        .popup-vertical .countdown-display { 
            font-size: 14px;
            min-width: 30px;
        }
        
        .popup-vertical .progress-line {
            height: 2px;
        }
    }

    /* Extra Small Mobile */
    @media (max-width: 360px) {
        .popup-vertical {
            width: 260px;
            padding: 10px;
            gap: 10px;
        }
        
        .popup-vertical .icon-circle { 
            width: 28px; 
            height: 28px; 
        }
        
        .popup-vertical .icon-circle i { 
            font-size: 12px; 
        }
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
        const submitBtn = document.getElementById('submitBtn');
        const loginForm = document.getElementById('loginForm');
        
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

        // Create Lockout Popup
        const lockoutPopup = document.createElement('div');
        lockoutPopup.className = 'popup-vertical';
        lockoutPopup.id = 'lockoutPopup';
        lockoutPopup.innerHTML = `
            <div class="icon-circle">
                <i class="fas fa-lock"></i>
            </div>
            <div class="content-area">
                <h3>Account Locked</h3>
                <p>Too many failed attempts</p>
            </div>
            <div class="countdown-display" id="countdownDisplay">60</div>
            <div class="progress-line">
                <div class="progress-fill" id="progressFill"></div>
            </div>
        `;
        document.body.appendChild(lockoutPopup);

        function startLockout(endTime) {
            if (interval) clearInterval(interval);
            localStorage.setItem('lockoutEnd', endTime);
            
            authForm.classList.add('form-disabled');
            lockoutPopup.classList.add('active');
            submitBtn.disabled = true;

            const countdownEl = document.getElementById('countdownDisplay');
            const progressEl = document.getElementById('progressFill');
            const startTime = Math.floor(Date.now() / 1000);

            function update() {
                const now = Math.floor(Date.now() / 1000);
                const remaining = endTime - now;

                if (remaining <= 0) {
                    clearInterval(interval);
                    localStorage.removeItem('lockoutEnd');
                    authForm.classList.remove('form-disabled');
                    lockoutPopup.classList.remove('active');
                    submitBtn.disabled = false;
                    showToast('success', 'Account Unlocked', 'You can now try again');
                    return;
                }

                countdownEl.textContent = remaining;
                const elapsed = now - startTime;
                const percent = Math.max(0, ((TOTAL_SECONDS - elapsed) / TOTAL_SECONDS) * 100);
                progressEl.style.width = percent + '%';
            }

            update();
            interval = setInterval(update, 100);
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

        // Create Loading Popup
        const loadingPopup = document.createElement('div');
        loadingPopup.className = 'popup-vertical';
        loadingPopup.id = 'loadingPopup';
        loadingPopup.innerHTML = `
            <div class="icon-circle">
                <i class="fas fa-spinner fa-spin"></i>
            </div>
            <div class="content-area">
                <h3>Please Wait</h3>
                <div class="loading-text">
                    <i class="fas fa-spinner fa-spin"></i> Signing in...
                </div>
            </div>
        `;
        document.body.appendChild(loadingPopup);

        // Toast Helper
        function showToast(type, title, text) {
            const toast = document.createElement('div');
            toast.className = 'popup-vertical ' + type;
            toast.innerHTML = `
                <div class="icon-circle">
                    <i class="fas ${type === 'success' ? 'fa-check' : type === 'warning' ? 'fa-exclamation-triangle' : 'fa-times'}"></i>
                </div>
                <div class="content-area">
                    <h3>${title}</h3>
                    <p>${text}</p>
                </div>
            `;
            document.body.appendChild(toast);
            setTimeout(() => toast.classList.add('active'), 10);
            setTimeout(() => {
                toast.classList.remove('active');
                setTimeout(() => toast.remove(), 400);
            }, 3000);
        }

        // Handle errors
        @if($errors->has('locked_out'))
            const lockoutEnd = {{ $errors->first('lockout_end') ?? 'null' }};
            if (lockoutEnd) startLockout(lockoutEnd);
        @elseif($errors->has('failed_attempt'))
            const remaining = {{ $errors->first('remaining') }};
            showToast('warning', 'Login Failed', `${remaining} ${remaining === 1 ? 'attempt' : 'attempts'} remaining`);
        @endif

        // Form submit handler
        loginForm.addEventListener('submit', function(e) {
            // Prevent default submission
            e.preventDefault();
            
            // Validate department selection
            if (!deptInput.value.trim()) {
                showToast('warning', 'Department Required', 'Please select your department');
                return;
            }

            // Show loading popup immediately
            loadingPopup.classList.add('active');

            // Get fresh reCAPTCHA token
            grecaptcha.ready(() => {
                grecaptcha.execute('{{ env("RECAPTCHA_SITE_KEY") }}', { action: 'login' })
                    .then(token => {
                        document.getElementById('recaptchaResponse').value = token;
                        // Submit form after getting token
                        loginForm.submit();
                    })
                    .catch(() => {
                        loadingPopup.classList.remove('active');
                        showToast('error', 'Verification Failed', 'Please try again');
                    });
            });
        });
    });
</script>
</x-guest-layout>