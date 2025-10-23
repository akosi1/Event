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

        /* Password toggle icon */
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
                            <a href="{{ route('password.request') }}"><i class="fas fa-key"></i> Forgot your
                                password?</a>
                        </div>
                        <div class="terms-link">
                            <a href="#" id="viewTermsBtn"><i class="fas fa-file-contract"></i> View Terms and Conditions</a>
                        </div>
                        <div class="signup-link">
                            <a href="{{ route('ms365.verify') }}" class="btn-secondary">Create account with ms365
                                email</a>
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

    {{-- Terms Modal --}}
    <div class="terms-modal" id="termsModal">
        <div class="terms-modal-content">
            <div class="terms-modal-header">
                <h2>User Terms and Conditions</h2>
                <button class="terms-close-btn" id="closeTermsBtn">&times;</button>
            </div>
            <div class="terms-modal-body">
                <div class="terms-section">
                    <h3>1. Acceptance of Terms</h3>
                    <p>By creating an account or accessing the MCC Event and Portfolio Organizer System, you acknowledge that you have read, understood, and agree to be bound by these Terms and Conditions. If you do not agree with any part of these terms, you must not use this system.</p>
                </div>

                <div class="terms-section">
                    <h3>2. User Accounts</h3>
                    <p>To access the full features of the system, you must register for an account by providing accurate, complete, and current information. You are solely responsible for maintaining the confidentiality of your account credentials, including your password. Any activity that occurs under your account is your responsibility. If you suspect unauthorized access to your account, you must notify the system administrator immediately.</p>
                </div>

                <div class="terms-section">
                    <h3>3. Proper Use of the System</h3>
                    <p>This platform is intended exclusively for event management and portfolio organization purposes. Users agree to use the system in a lawful and respectful manner. Prohibited behaviors include, but are not limited to:</p>
                    <ul>
                        <li>Posting spam, false information, or misleading content</li>
                        <li>Uploading offensive, defamatory, or inappropriate materials</li>
                        <li>Impersonating other individuals or organizations</li>
                        <li>Disrupting the normal operation of the system</li>
                    </ul>
                </div>

                <div class="terms-section">
                    <h3>4. Content Ownership</h3>
                    <p>You retain full ownership of all portfolios, documents, images, and other materials you upload to the system. By uploading content, you grant the MCC Event and Portfolio Organizer System a non-exclusive, royalty-free license to display, store, and distribute your content solely for the purpose of providing the service. You represent and warrant that you have the necessary rights to upload and share all content you submit.</p>
                </div>

                <div class="terms-section">
                    <h3>5. Privacy and Data Use</h3>
                    <p>The system collects limited personal information necessary for account creation and management, including your name, email address, department affiliation, and uploaded files. This information is used exclusively for:</p>
                    <ul>
                        <li>Account authentication and management</li>
                        <li>Event coordination and portfolio display</li>
                        <li>Communication regarding system updates or events</li>
                    </ul>
                    <p>We are committed to protecting your privacy and will not share your personal information with third parties without your consent, except as required by law.</p>
                </div>

                <div class="terms-section">
                    <h3>6. Prohibited Activities</h3>
                    <p>Users must not engage in any activity that compromises the security, integrity, or availability of the system. This includes:</p>
                    <ul>
                        <li>Attempting to gain unauthorized access to system resources or other user accounts</li>
                        <li>Uploading malicious software, viruses, or harmful code</li>
                        <li>Reverse engineering, decompiling, or attempting to extract source code</li>
                        <li>Using automated tools or bots to access the system</li>
                        <li>Overloading system resources or attempting denial-of-service attacks</li>
                    </ul>
                </div>

                <div class="terms-section">
                    <h3>7. Account Termination</h3>
                    <p>The system administrator reserves the right to suspend or terminate user accounts at any time, with or without notice, for violations of these Terms and Conditions. Reasons for account termination may include, but are not limited to:</p>
                    <ul>
                        <li>Violation of proper use guidelines</li>
                        <li>Posting inappropriate or offensive content</li>
                        <li>Engaging in prohibited activities</li>
                        <li>Providing false or misleading information during registration</li>
                    </ul>
                    <p>Users whose accounts have been terminated may not re-register without explicit permission from the administrator.</p>
                </div>

                <div class="terms-section">
                    <h3>8. System Updates and Maintenance</h3>
                    <p>The MCC Event and Portfolio Organizer System may undergo periodic maintenance, updates, and improvements. During these periods, the system may be temporarily unavailable. We will make reasonable efforts to notify users in advance of scheduled maintenance, but we do not guarantee uninterrupted access to the system.</p>
                </div>

                <div class="terms-section">
                    <h3>9. Limitation of Liability</h3>
                    <p>The MCC Event and Portfolio Organizer System is provided "as is" without warranties of any kind, either express or implied. The developers and administrators are not responsible for:</p>
                    <ul>
                        <li>Loss of data due to technical failures or user error</li>
                        <li>Misuse of information by other users</li>
                        <li>Damages resulting from unauthorized access to user accounts</li>
                        <li>Interruptions in service or system availability</li>
                    </ul>
                    <p>Users are advised to maintain backups of important documents and information stored on the system.</p>
                </div>

                <div class="terms-section">
                    <h3>10. Contact Information</h3>
                    <p>For questions, concerns, or support regarding these Terms and Conditions or the use of the MCC Event and Portfolio Organizer System, please contact us at:</p>
                    <p class="contact-info">
                        <strong>Email:</strong> events@gmail.com<br>
                        <strong>Subject Line:</strong> User Support - Terms and Conditions Inquiry
                    </p>
                    <!-- <p>We aim to respond to all inquiries within 2-3 business days.</p> -->
                </div>

                <div class="terms-section terms-footer-section">
                    <p><strong>Last Updated:</strong> October 23, 2025</p>
                    <p>By continuing to use this system, you acknowledge that you have read and accepted these Terms and Conditions.</p>
                </div>
            </div>
            <div class="terms-modal-footer">
                <button class="btn-accept-terms" id="acceptTermsBtn">I Understand</button>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://www.google.com/recaptcha/api.js?render={{ env('RECAPTCHA_SITE_KEY') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const authForm = document.getElementById('authForm');
            const submitBtn = document.getElementById('submitBtn');
            const loginForm = document.getElementById('loginForm');
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            let interval = null;
            let isSubmitting = false;

            // Password visibility toggle
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Toggle eye icon
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });

            // Utility: Format time as MM:SS
            const formatTime = (s) => `${Math.floor(s/60)}:${(s%60).toString().padStart(2,'0')}`;

            // Lockout Popup
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

            function startLockout(endTime) {
                if (interval) clearInterval(interval);
                localStorage.setItem('lockoutEnd', endTime);
                authForm.classList.add('form-disabled');
                lockoutPopup.classList.add('active');
                submitBtn.disabled = true;

                const countdownEl = document.getElementById('countdownDisplay');
                const progressEl = document.getElementById('progressFill');
                const total = endTime - Math.floor(Date.now() / 1000);

                function update() {
                    const now = Math.floor(Date.now() / 1000);
                    const remain = endTime - now;
                    if (remain <= 0) {
                        clearInterval(interval);
                        localStorage.removeItem('lockoutEnd');
                        authForm.classList.remove('form-disabled');
                        lockoutPopup.classList.remove('active');
                        submitBtn.disabled = false;
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

            // Loading popup
            const loadingPopup = document.createElement('div');
            loadingPopup.className = 'popup-vertical';
            loadingPopup.id = 'loadingPopup';
            loadingPopup.innerHTML = `
                <div class="icon-circle"><i class="fas fa-spinner fa-spin"></i></div>
                <div class="content-area">
                    <h3>Please Wait</h3>
                    <div class="loading-text"><i class="fas fa-spinner fa-spin"></i> Signing in...</div>
                </div>`;
            document.body.appendChild(loadingPopup);

            // SweetAlert-style toast
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

            // Handle backend validation messages
            @if ($errors->has('locked_out'))
                const lockoutEnd = {{ $errors->first('lockout_end') ?? 'null' }};
                if (lockoutEnd) startLockout(lockoutEnd);
            @elseif ($errors->has('failed_attempt'))
                const remaining = {{ $errors->first('remaining') }};
                showToast('warning', 'Login Failed',
                    `${remaining} ${remaining === 1 ? 'attempt' : 'attempts'} remaining`);
            @endif

            // Submit with reCAPTCHA
            loginForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Prevent double submission
                if (isSubmitting) {
                    return;
                }
                
                isSubmitting = true;
                submitBtn.disabled = true;
                loadingPopup.classList.add('active');
                
                grecaptcha.ready(() => {
                    grecaptcha.execute('{{ env('RECAPTCHA_SITE_KEY') }}', {
                            action: 'login'
                        })
                        .then(token => {
                            document.getElementById('recaptchaResponse').value = token;
                            loginForm.submit();
                        })
                        .catch(() => {
                            loadingPopup.classList.remove('active');
                            submitBtn.disabled = false;
                            isSubmitting = false;
                            showToast('error', 'Verification Failed', 'Please try again.');
                        });
                });
            });

            // ===== TERMS MODAL FUNCTIONALITY =====
            const termsModal = document.getElementById('termsModal');
            const viewTermsBtn = document.getElementById('viewTermsBtn');
            const closeTermsBtn = document.getElementById('closeTermsBtn');
            const acceptTermsBtn = document.getElementById('acceptTermsBtn');

            // Open modal
            if (viewTermsBtn) {
                viewTermsBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    termsModal.classList.add('active');
                    document.body.style.overflow = 'hidden';
                });
            }

            // Close modal
            function closeModal() {
                termsModal.classList.remove('active');
                document.body.style.overflow = '';
            }

            if (closeTermsBtn) {
                closeTermsBtn.addEventListener('click', closeModal);
            }

            if (acceptTermsBtn) {
                acceptTermsBtn.addEventListener('click', closeModal);
            }

            // Close when clicking outside
            termsModal.addEventListener('click', function(e) {
                if (e.target === termsModal) {
                    closeModal();
                }
            });

            // Close with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && termsModal.classList.contains('active')) {
                    closeModal();
                }
            });
        });
    </script>
</x-guest-layout>