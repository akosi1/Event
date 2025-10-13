<x-guest-layout>
    <link rel="stylesheet" href="{{ asset('user/auth/auth.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="preload" as="image" href="{{ asset('images/mcc background.jpg') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        html, body {
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
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, rgba(74, 26, 92, 0.8) 0%, rgba(107, 44, 145, 0.8) 50%, rgba(61, 26, 120, 0.8) 100%);
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
            to { opacity: 1; }
        }
        
        /* Countdown Display Styles */
        .countdown-display {
            font-size: 48px;
            font-weight: 700;
            color: #e74c3c;
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
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
    </style>

    <div class="auth-wrapper">
        <div class="auth-container">
            <div class="auth-header">
                <h1>Welcome Back</h1>
                <p>Sign in to your E&amp;P-O account</p>
            </div>
            <div class="auth-form" id="authForm">
                <x-auth-session-status class="mb-4" :status="session('status')" />
                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf

                    <div class="form-group">
                        <div class="input-wrapper">
                            <input id="email" type="email" name="email" 
                                value="{{ old('email') }}" 
                                class="form-control @error('email') input-error @enderror" 
                                placeholder=" " 
                                required 
                                autocomplete="username" 
                                autofocus
                                oninput="this.value = this.value.replace(/\s+/g, '')"
                                onblur="this.value = this.value.trim()">
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
                                class="form-control @error('password') input-error @enderror" 
                                placeholder=" " 
                                required 
                                autocomplete="current-password"
                                oninput="this.value = this.value.replace(/\s+/g, '')"
                                onblur="this.value = this.value.trim()">
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
                                <div class="item" data-val="BSEd" data-icon="fa-chalkboard-teacher"><i class="fas fa-chalkboard-teacher"></i><span>BSEd</span></div>
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

            // Restore department selection
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

            // Format time display (MM:SS)
            function formatTime(totalSeconds) {
                const minutes = Math.floor(totalSeconds / 60);
                const seconds = totalSeconds % 60;
                return `${minutes}:${seconds.toString().padStart(2, '0')}`;
            }

            // Lockout Popup
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
                <div class="countdown-display" id="countdownDisplay">0:00</div>
                <div class="countdown-label">Time Remaining</div>
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
                
                // Calculate total duration for progress bar
                const now = Math.floor(Date.now() / 1000);
                const totalDuration = endTime - now;

                function update() {
                    const currentTime = Math.floor(Date.now() / 1000);
                    const remaining = endTime - currentTime;

                    if (remaining <= 0) {
                        clearInterval(interval);
                        localStorage.removeItem('lockoutEnd');
                        authForm.classList.remove('form-disabled');
                        lockoutPopup.classList.remove('active');
                        submitBtn.disabled = false;
                        showToast('success', 'Account Unlocked', 'You can now try logging in again');
                        return;
                    }

                    // Update countdown display (MM:SS)
                    countdownEl.textContent = formatTime(remaining);
                    
                    // Update progress bar
                    const elapsed = totalDuration - remaining;
                    const percent = Math.max(0, ((totalDuration - elapsed) / totalDuration) * 100);
                    progressEl.style.width = percent + '%';
                }

                update();
                interval = setInterval(update, 1000); // Update every second
            }

            // Check for existing lockout on page load
            const stored = localStorage.getItem('lockoutEnd');
            if (stored) {
                const end = parseInt(stored);
                const now = Math.floor(Date.now() / 1000);
                if (end > now) {
                    startLockout(end);
                } else {
                    localStorage.removeItem('lockoutEnd');
                }
            }

            // Loading Popup
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

            // Handle server-side errors
            @if($errors->has('locked_out'))
                const lockoutEnd = {{ $errors->first('lockout_end') ?? 'null' }};
                if (lockoutEnd) {
                    startLockout(lockoutEnd);
                }
            @elseif($errors->has('failed_attempt'))
                const remaining = {{ $errors->first('remaining') }};
                showToast('warning', 'Login Failed', `${remaining} ${remaining === 1 ? 'attempt' : 'attempts'} remaining`);
            @endif

            // Form submission with reCAPTCHA
            loginForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Check if department is selected
                if (!deptInput.value.trim()) {
                    showToast('warning', 'Department Required', 'Please select your department');
                    return;
                }

                // Show loading popup
                loadingPopup.classList.add('active');

                // Execute reCAPTCHA
                grecaptcha.ready(() => {
                    grecaptcha.execute('{{ env("RECAPTCHA_SITE_KEY") }}', { action: 'login' })
                        .then(token => {
                            document.getElementById('recaptchaResponse').value = token;
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