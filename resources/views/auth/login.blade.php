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
    <style>
        /* ===== TERMS LINK (Styled like forgot-password) ===== */
.terms-link {
    margin-bottom: 20px;
}

.terms-link a {
    color: #ffffff;
    text-decoration: none;
    font-size: 15px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    font-family: 'Poppins', sans-serif;
    transition: all 0.3s ease;
}

.terms-link a:hover {
    text-decoration: underline;
    color: #e0e0e0;
}

.error-msg {
    color: #ff6b6b;
    font-size: 12px;
    margin-top: 6px;
    font-family: 'Poppins', sans-serif;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
    padding: 8px 12px;
    background: rgba(255, 107, 107, 0.15);
    border-left: 3px solid #ff6b6b;
    display: flex;
    align-items: center;
    gap: 6px;
}

.mb-4 div {
    padding: 10px 14px;
    background: rgba(217, 237, 247, 0.95);
    border: 1px solid #bee5eb;
    border-radius: 4px;
    color: #0c5460;
    font-size: 13px;
    margin-bottom: 18px;
}

/* ===== POPUP STYLES ===== */
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
    from { transform: translateX(120%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

.popup-vertical.active { 
    display: flex; 
    align-items: center; 
    gap: 12px; 
}

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

.popup-vertical .countdown-display {
    font-size: 18px;
    font-weight: 700;
    color: #e74c3c;
    line-height: 1;
    text-align: right;
    min-width: 40px;
    flex-shrink: 0;
}

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

.form-disabled .password-toggle {
    opacity: 0.4;
    pointer-events: none;
}

/* ===== TERMS AND CONDITIONS MODAL ===== */
.terms-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.75);
    z-index: 10000;
    backdrop-filter: blur(4px);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.terms-modal.active {
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 1;
}

.terms-modal-content {
    background: white;
    width: 90%;
    max-width: 800px;
    max-height: 85vh;
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    transform: scale(0.9);
    transition: transform 0.3s ease;
    overflow: hidden;
}

.terms-modal.active .terms-modal-content {
    transform: scale(1);
}

.terms-modal-header {
    background: linear-gradient(135deg, #6b2c91, #4a1a5c);
    color: white;
    padding: 24px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 3px solid #8e44ad;
    flex-shrink: 0;
}

.terms-modal-header h2 {
    margin: 0;
    font-size: 26px;
    font-weight: 700;
    font-family: 'Oswald', sans-serif;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.terms-close-btn {
    background: transparent;
    border: none;
    color: white;
    font-size: 36px;
    cursor: pointer;
    padding: 0;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.3s ease;
    line-height: 0;
    font-family: Arial, sans-serif;
}

.terms-close-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: rotate(90deg);
}

.terms-modal-body {
    padding: 30px;
    overflow-y: auto;
    flex: 1;
    background: #f8f9fa;
}

.terms-modal-body::-webkit-scrollbar {
    width: 10px;
}

.terms-modal-body::-webkit-scrollbar-track {
    background: #e0e0e0;
    border-radius: 10px;
}

.terms-modal-body::-webkit-scrollbar-thumb {
    background: #6b2c91;
    border-radius: 10px;
}

.terms-modal-body::-webkit-scrollbar-thumb:hover {
    background: #8e44ad;
}

.terms-section {
    background: white;
    padding: 24px;
    margin-bottom: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.terms-section:last-child {
    margin-bottom: 0;
}

.terms-section h3 {
    color: #6b2c91;
    font-size: 20px;
    font-weight: 700;
    margin: 0 0 16px 0;
    font-family: 'Poppins', sans-serif;
}

.terms-section p {
    color: #333;
    font-size: 15px;
    line-height: 1.7;
    margin: 0 0 12px 0;
    font-family: 'Poppins', sans-serif;
    text-align: justify;
}

.terms-section p:last-child {
    margin-bottom: 0;
}

.terms-section ul {
    margin: 12px 0;
    padding-left: 24px;
}

.terms-section ul li {
    color: #333;
    font-size: 15px;
    line-height: 1.7;
    margin-bottom: 8px;
    font-family: 'Poppins', sans-serif;
}

.terms-section ul li:last-child {
    margin-bottom: 0;
}

.contact-info {
    background: #f0f0f0;
    padding: 16px;
    border-radius: 6px;
    font-size: 14px !important;
}

.terms-footer-section {
    background: linear-gradient(135deg, rgba(107, 44, 145, 0.05), rgba(74, 26, 92, 0.05));
}

.terms-footer-section p {
    margin-bottom: 8px;
}

.terms-modal-footer {
    background: white;
    padding: 20px 30px;
    border-top: 1px solid #e0e0e0;
    display: flex;
    justify-content: flex-end;
    flex-shrink: 0;
}

.btn-accept-terms {
    background: linear-gradient(135deg, #6b2c91, #4a1a5c);
    color: white;
    border: none;
    padding: 12px 32px;
    font-size: 16px;
    font-weight: 600;
    font-family: 'Poppins', sans-serif;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(107, 44, 145, 0.3);
    text-transform: uppercase;
    letter-spacing: 1px;
}

.btn-accept-terms:hover {
    background: linear-gradient(135deg, #8e44ad, #6b2c91);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(107, 44, 145, 0.4);
}

.btn-accept-terms:active {
    transform: translateY(0);
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    .auth-wrapper { padding: 15px; }
    .auth-container { padding: 20px 15px; max-width: 100%; }
    .auth-header { margin-bottom: 25px; }
    .auth-header h1 { font-size: 28px; }
    .auth-header p { font-size: 13px; }
    .form-group { margin-bottom: 18px; }
    .form-control {
        padding: 16px 45px 6px 45px;
        font-size: 15px;
    }
    .input-icon { left: 14px; width: 16px; height: 16px; }
    .password-toggle { right: 14px; font-size: 15px; }
    .input-label { left: 45px; font-size: 15px; }
    .form-control:focus + .input-label,
    .form-control:not(:placeholder-shown) + .input-label {
        font-size: 10px; left: 40px;
    }
    .dropdown-btn {
        padding: 16px 40px 6px 45px;
        font-size: 15px;
    }
    .select-label { left: 45px; font-size: 15px; }
    .dropdown-btn.has-value .select-label { display: none; }
    #deptText { left: 45px; font-size: 15px; }
    .arrow { right: 14px; }
    .item { padding: 12px 12px 12px 40px; font-size: 15px; }
    .item i { left: 12px; font-size: 15px; }
    .btn-submit { padding: 14px; font-size: 15px; margin-bottom: 18px; }
    .btn-secondary { padding: 11px 18px; font-size: 15px; }
    .forgot-password a { font-size: 14px; }
    .terms-link a { font-size: 14px; }
    .divider { margin: 18px 0; }
    .signup-link p { font-size: 14px; margin-bottom: 6px; }
    .error-msg { font-size: 11px; padding: 7px 10px; }
    .popup-vertical { width: 300px; padding: 14px; }
    
    /* Terms Modal Responsive */
    .terms-modal-content {
        width: 95%;
        max-height: 90vh;
    }
    .terms-modal-header {
        padding: 20px 20px;
    }
    .terms-modal-header h2 {
        font-size: 22px;
    }
    .terms-close-btn {
        font-size: 32px;
        width: 36px;
        height: 36px;
    }
    .terms-modal-body {
        padding: 20px;
    }
    .terms-section {
        padding: 20px;
    }
    .terms-section h3 {
        font-size: 18px;
    }
    .terms-section p,
    .terms-section ul li {
        font-size: 14px;
    }
    .terms-modal-footer {
        padding: 16px 20px;
    }
    .btn-accept-terms {
        padding: 11px 28px;
        font-size: 15px;
    }
}

@media (max-width: 480px) {
    .auth-container { padding: 20px 12px; }
    .auth-header h1 { font-size: 26px; letter-spacing: 0.5px; }
    .auth-header p { font-size: 12px; }
    .form-control { padding: 14px 40px 5px 40px; font-size: 14px; }
    .input-icon { left: 12px; width: 14px; height: 14px; }
    .password-toggle { right: 12px; font-size: 14px; }
    .input-label { left: 40px; font-size: 14px; }
    .form-control:focus + .input-label,
    .form-control:not(:placeholder-shown) + .input-label {
        font-size: 10px; left: 35px;
    }
    .dropdown-btn { padding: 14px 35px 5px 40px; font-size: 14px; }
    .select-label { left: 40px; font-size: 14px; }
    .dropdown-btn.has-value .select-label { display: none; }
    #deptText { left: 40px; font-size: 14px; }
    .arrow { right: 12px; }
    .item { padding: 11px 10px 11px 38px; font-size: 14px; }
    .item i { left: 10px; font-size: 14px; }
    .btn-submit { padding: 13px; font-size: 14px; letter-spacing: 1.5px; }
    .btn-secondary { padding: 10px 16px; font-size: 14px; }
    .forgot-password { margin-bottom: 16px; }
    .forgot-password a { font-size: 13px; }
    .terms-link { margin-bottom: 16px; }
    .terms-link a { font-size: 13px; }
    .divider { margin: 16px 0; }
    .divider span { font-size: 13px; }
    .signup-link p { font-size: 13px; }
    .popup-vertical { width: 280px; padding: 12px; gap: 10px; }
    .popup-vertical .icon-circle { width: 36px; height: 36px; }
    .popup-vertical .icon-circle i { font-size: 16px; }
    .popup-vertical h3 { font-size: 13px; }
    .popup-vertical p { font-size: 11px; }
    
    /* Terms Modal Small Mobile */
    .terms-modal-content {
        width: 98%;
        max-height: 92vh;
        border-radius: 8px;
    }
    .terms-modal-header {
        padding: 18px 16px;
    }
    .terms-modal-header h2 {
        font-size: 19px;
        letter-spacing: 0.5px;
    }
    .terms-close-btn {
        font-size: 28px;
        width: 32px;
        height: 32px;
    }
    .terms-modal-body {
        padding: 16px;
    }
    .terms-section {
        padding: 16px;
        margin-bottom: 16px;
    }
    .terms-section h3 {
        font-size: 17px;
        margin-bottom: 12px;
    }
    .terms-section p,
    .terms-section ul li {
        font-size: 13px;
        line-height: 1.6;
    }
    .terms-section ul {
        padding-left: 20px;
    }
    .contact-info {
        padding: 12px;
        font-size: 13px !important;
    }
    .terms-modal-footer {
        padding: 14px 16px;
    }
    .btn-accept-terms {
        padding: 10px 24px;
        font-size: 14px;
        letter-spacing: 0.5px;
    }
}

@media (max-width: 360px) {
    .popup-vertical { width: 260px; padding: 10px; gap: 8px; }
    .popup-vertical .icon-circle { width: 32px; height: 32px; }
    .popup-vertical .icon-circle i { font-size: 14px; }
    .popup-vertical h3 { font-size: 12px; }
    .popup-vertical p { font-size: 10px; }
    .popup-vertical .countdown-display { font-size: 16px; }
    
    /* Terms Modal Extra Small */
    .terms-modal-header h2 {
        font-size: 17px;
    }
    .terms-section h3 {
        font-size: 16px;
    }
    .terms-section p,
    .terms-section ul li {
        font-size: 12px;
    }
    .btn-accept-terms {
        font-size: 13px;
        padding: 9px 20px;
    }
}
    </style>
</x-guest-layout>