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
    
    /* ===== GLOBAL RESET & ACCESSIBILITY ===== */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    -webkit-tap-highlight-color: transparent; /* Removes mobile tap glow */
}

html, body {
    height: 100%;
    overflow: hidden;
    font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* ===== BACKGROUND OVERLAY ===== */
body::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(74, 26, 92, 0.8) 0%, rgba(107, 44, 145, 0.8) 50%, rgba(61, 26, 120, 0.8) 100%);
    z-index: 1;
}

/* ===== AUTH CONTAINER ===== */
.auth-wrapper {
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    position: relative;
    z-index: 2;
}

.auth-container {
    background: transparent;
    width: 100%;
    max-width: 450px;
    animation: fadeIn 0.6s ease-out;
    padding: 30px;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
}

/* ===== HEADER ===== */
.auth-header {
    text-align: center;
    margin-bottom: 30px;
}

.auth-header h1 {
    font-size: 38px;
    font-weight: 600;
    color: #ffffff;
    margin-bottom: 8px;
    letter-spacing: 1px;
    font-family: 'Oswald', sans-serif;
    text-transform: uppercase;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.auth-header p {
    color: #e0e0e0;
    font-size: 15px;
    font-weight: 400;
    font-family: 'Roboto Condensed', sans-serif;
    text-transform: uppercase;
    letter-spacing: 1px;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
}

/* ===== SCROLLABLE FORM ===== */
.auth-form {
    max-height: calc(100vh - 200px);
    overflow-y: auto;
    scrollbar-width: none;
    padding-right: 5px;
}

.auth-form::-webkit-scrollbar {
    display: none;
}

.auth-form:hover,
.auth-form:focus-within {
    scrollbar-width: auto;
}

.auth-form:hover::-webkit-scrollbar,
.auth-form:focus-within::-webkit-scrollbar {
    width: 8px;
}

.auth-form:hover::-webkit-scrollbar-track,
.auth-form:focus-within::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
}

.auth-form:hover::-webkit-scrollbar-thumb,
.auth-form:focus-within::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 10px;
}

/* ===== FORM GROUPS ===== */
.form-group {
    margin-bottom: 22px;
}

.input-wrapper {
    position: relative;
}

/* ===== INPUT STYLES (EMAIL & PASSWORD) ===== */
.form-control {
    width: 100%;
    padding: 18px 18px 8px 50px;
    border: 2px solid #ddd;
    border-radius: 0;
    font-size: 16px;
    outline: none; /* 🔥 REMOVED FOCUS OUTLINE */
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.95);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    font-family: 'Poppins', sans-serif;
    font-weight: 400;
    color: #000000;
}

.form-control:focus {
    border-color: #6b2c91;
    background: rgba(255, 255, 255, 1);
    box-shadow: 0 4px 15px rgba(107, 44, 145, 0.2);
    outline: none; /* 🔥 REMOVED FOCUS OUTLINE */
}

.form-control.input-error {
    border-color: #ff6b6b;
    box-shadow: 0 4px 15px rgba(255, 107, 107, 0.2);
}

/* ===== INPUT ICONS ===== */
.input-icon {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    width: 18px;
    height: 18px;
    color: #666;
    transition: color 0.3s ease;
    z-index: 3;
}

.form-control:focus ~ .input-icon {
    color: #6b2c91;
}

.form-control.input-error ~ .input-icon {
    color: #ff6b6b;
}

/* ===== FLOATING LABELS ===== */
.input-label {
    position: absolute;
    left: 50px;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
    font-size: 16px;
    pointer-events: none;
    transition: all 0.3s ease;
    background: transparent;
    padding: 0 5px;
    font-family: 'Poppins', sans-serif;
    font-weight: 400;
}

.form-control:focus + .input-label,
.form-control:not(:placeholder-shown) + .input-label {
    top: 6px;
    transform: translateY(0);
    font-size: 11px;
    color: #6b2c91;
    background: rgba(255, 255, 255, 1);
    left: 45px;
    font-weight: 600;
}

.form-control.input-error:focus + .input-label,
.form-control.input-error:not(:placeholder-shown) + .input-label {
    color: #ff6b6b;
}

/* ===== CUSTOM DROPDOWN ===== */
.dropdown-wrapper {
    position: relative;
}

.dropdown-btn {
    width: 100%;
    padding: 18px 45px 8px 50px;
    background: rgba(255, 255, 255, 0.95);
    border: 2px solid #ddd;
    border-radius: 0;
    font-size: 16px;
    color: #000;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    min-height: 54px;
    position: relative;
    outline: none; /* 🔥 REMOVED FOCUS OUTLINE */
}

.dropdown-btn:hover {
    border-color: #6b2c91;
}

.dropdown-btn.active {
    border-color: #6b2c91;
    background: rgba(255, 255, 255, 1);
    box-shadow: 0 4px 15px rgba(107, 44, 145, 0.2);
    outline: none; /* 🔥 REMOVED FOCUS OUTLINE */
}

.select-label {
    position: absolute;
    left: 50px;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
    font-size: 16px;
    pointer-events: none;
    transition: all 0.3s ease;
    font-family: 'Poppins', sans-serif;
    font-weight: 400;
    z-index: 2;
}

/* 🔥 CRITICAL: Hide label when value selected to avoid overlap */
.dropdown-btn.has-value .select-label {
    display: none;
}

#deptText {
    position: absolute;
    left: 50px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 16px;
    color: #000;
    font-family: 'Poppins', sans-serif;
    font-weight: 400;
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 1;
}

.dropdown-btn.has-value #deptText {
    opacity: 1;
}

.arrow {
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
    transition: all 0.3s ease;
    font-size: 12px;
}

.dropdown-btn.active .arrow {
    transform: translateY(-50%) rotate(180deg);
    color: #6b2c91;
}

.menu {
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    background: white;
    border: 2px solid #6b2c91;
    border-top: none;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s;
    z-index: 1000;
    box-shadow: 0 4px 15px rgba(107, 44, 145, 0.2);
}

.menu.show {
    max-height: 250px;
    overflow-y: auto;
}

.item {
    padding: 12px 15px 12px 45px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 12px;
    border-bottom: 1px solid #f0f0f0;
    font-size: 16px;
    color: #000;
    position: relative;
    font-family: 'Poppins', sans-serif;
}

.item:last-child {
    border-bottom: none;
}

.item:hover {
    background: rgba(107, 44, 145, 0.1);
}

.item:active {
    background: rgba(107, 44, 145, 0.2);
}

.item i {
    position: absolute;
    left: 15px;
    color: #6b2c91;
    font-size: 16px;
}

.menu::-webkit-scrollbar {
    width: 6px;
}

.menu::-webkit-scrollbar-thumb {
    background: #6b2c91;
    border-radius: 3px;
}

/* ===== BUTTONS ===== */
.btn-submit {
    width: 100%;
    padding: 16px;
    background: linear-gradient(135deg, #e53e3e 0%, #d53f41 100%);
    color: white;
    border: none;
    border-radius: 0;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-bottom: 20px;
    transition: all 0.3s ease;
    font-family: 'Oswald', sans-serif;
    text-transform: uppercase;
    letter-spacing: 2px;
    box-shadow: 0 4px 15px rgba(229, 62, 62, 0.3);
    outline: none; /* 🔥 */
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(229, 62, 62, 0.4);
    background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
}

.btn-submit:active {
    transform: translateY(-1px);
}

.btn-secondary {
    display: inline-block;
    width: 100%;
    padding: 12px 20px;
    background: transparent;
    color: #ffffff;
    border: 2px solid rgba(255, 255, 255, 0.6);
    border-radius: 0;
    font-size: 16px;
    font-weight: 500;
    text-decoration: none;
    text-align: center;
    transition: all 0.3s ease;
    margin-top: 8px;
    font-family: 'Poppins', sans-serif;
    letter-spacing: 0.5px;
    outline: none; /* 🔥 */
}

.btn-secondary:hover {
    background: rgba(107, 44, 145, 0.3);
    border-color: rgba(255, 255, 255, 0.9);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(255, 255, 255, 0.2);
}

.btn-secondary:active {
    background: rgba(107, 44, 145, 0.5);
    transform: translateY(0);
}

/* ===== FOOTER & MESSAGES ===== */
.form-footer {
    text-align: center;
}

.forgot-password {
    margin-bottom: 20px;
}

.forgot-password a {
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

.forgot-password a:hover {
    text-decoration: underline;
    color: #e0e0e0;
}

.divider {
    position: relative;
    margin: 20px 0;
    text-align: center;
}

.divider::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: rgba(255, 255, 255, 0.3);
}

.divider span {
    background: transparent;
    padding: 0 15px;
    color: #ffffff;
    font-size: 14px;
    position: relative;
    font-family: 'Poppins', sans-serif;
}

.signup-link p {
    color: #ffffff;
    font-size: 15px;
    margin-bottom: 8px;
    font-family: 'Poppins', sans-serif;
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

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    .auth-wrapper { padding: 15px; }
    .auth-container { padding: 20px 15px; max-width: 100%; }
    .auth-header { margin-bottom: 25px; }
    .auth-header h1 { font-size: 28px; }
    .auth-header p { font-size: 13px; }
    .form-group { margin-bottom: 18px; }
    .form-control {
        padding: 16px 14px 6px 45px;
        font-size: 15px;
    }
    .input-icon { left: 14px; width: 16px; height: 16px; }
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
    .divider { margin: 18px 0; }
    .signup-link p { font-size: 14px; margin-bottom: 6px; }
    .error-msg { font-size: 11px; padding: 7px 10px; }
}

@media (max-width: 480px) {
    .auth-container { padding: 20px 12px; }
    .auth-header h1 { font-size: 26px; letter-spacing: 0.5px; }
    .auth-header p { font-size: 12px; }
    .form-control { padding: 14px 12px 5px 40px; font-size: 14px; }
    .input-icon { left: 12px; width: 14px; height: 14px; }
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
    .divider { margin: 16px 0; }
    .divider span { font-size: 13px; }
    .signup-link p { font-size: 13px; }
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