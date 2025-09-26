<x-guest-layout>
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('user/login_register/login_register.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <div class="auth-container otp-mode">
        <div class="floating-particles" id="particles"></div>
        <div class="diagonal-section otp-mode"></div>

        <!-- Welcome Section -->
        <div class="welcome-section otp-mode">
            <h1>VERIFY YOUR EMAIL</h1>
            <p>Enter the verification code sent to your email</p>
            <div class="email-info">
                <i class="fas fa-envelope"></i>
                <span>{{ $maskedEmail }}</span>
            </div>
        </div>

        <!-- OTP Form Section -->
        <div class="form-section otp-mode">
            <div class="form-content">
                <div class="form-title">Email Verification</div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                @if(session('message'))
                    <div class="success-message">
                        <i class="fas fa-check-circle"></i>
                        {{ session('message') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('otp.verify') }}" id="otp-form">
                    @csrf

                    <!-- OTP Input -->
                    <div class="form-group">
                        <div class="otp-inputs">
                            <input type="text" class="otp-digit" maxlength="1" data-index="0">
                            <input type="text" class="otp-digit" maxlength="1" data-index="1">
                            <input type="text" class="otp-digit" maxlength="1" data-index="2">
                            <input type="text" class="otp-digit" maxlength="1" data-index="3">
                            <input type="text" class="otp-digit" maxlength="1" data-index="4">
                            <input type="text" class="otp-digit" maxlength="1" data-index="5">
                        </div>
                        <input type="hidden" name="otp_code" id="otp_code" value="{{ old('otp_code') }}">
                        <x-input-error :messages="$errors->get('otp_code')" class="error-msg" />
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-submit" id="verify-btn">
                        <i class="fas fa-shield-alt"></i>
                        {{ __('Verify Code') }}
                    </button>

                    <div class="auth-links">
                        <div class="resend-section">
                            <p>Didn't receive the code?</p>
                            <form method="POST" action="{{ route('otp.resend') }}" class="resend-form">
                                @csrf
                                <button type="submit" class="link-btn" id="resend-btn">
                                    <i class="fas fa-redo"></i>
                                    Resend Code
                                </button>
                            </form>
                        </div>
                        
                        <p class="back-link">
                            <a href="{{ $type === 'registration' ? route('register') : route('login') }}">
                                <i class="fas fa-arrow-left"></i>
                                Back to {{ $type === 'registration' ? 'Registration' : 'Login' }}
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const otpInputs = document.querySelectorAll('.otp-digit');
        const hiddenInput = document.getElementById('otp_code');
        const form = document.getElementById('otp-form');
        const verifyBtn = document.getElementById('verify-btn');
        const resendBtn = document.getElementById('resend-btn');
        
        // Auto-focus first input
        otpInputs[0].focus();
        
        // Handle OTP input
        otpInputs.forEach((input, index) => {
            input.addEventListener('input', function(e) {
                const value = e.target.value;
                
                // Only allow digits
                if (!/^\d$/.test(value) && value !== '') {
                    e.target.value = '';
                    return;
                }
                
                if (value !== '') {
                    // Move to next input
                    if (index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }
                }
                
                updateHiddenInput();
            });
            
            input.addEventListener('keydown', function(e) {
                // Handle backspace
                if (e.key === 'Backspace' && e.target.value === '' && index > 0) {
                    otpInputs[index - 1].focus();
                }
                
                // Handle paste
                if (e.key === 'Enter') {
                    e.preventDefault();
                    if (getOTPValue().length === 6) {
                        form.submit();
                    }
                }
            });
            
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const paste = (e.clipboardData || window.clipboardData).getData('text');
                const digits = paste.replace(/\D/g, '').slice(0, 6);
                
                digits.split('').forEach((digit, i) => {
                    if (otpInputs[i]) {
                        otpInputs[i].value = digit;
                    }
                });
                
                updateHiddenInput();
                
                if (digits.length === 6) {
                    verifyBtn.focus();
                }
            });
        });
        
        function updateHiddenInput() {
            const otpValue = getOTPValue();
            hiddenInput.value = otpValue;
            
            // Enable/disable submit button
            if (otpValue.length === 6) {
                verifyBtn.disabled = false;
                verifyBtn.classList.remove('disabled');
            } else {
                verifyBtn.disabled = true;
                verifyBtn.classList.add('disabled');
            }
        }
        
        function getOTPValue() {
            return Array.from(otpInputs).map(input => input.value).join('');
        }
        
        // Handle form submission
        form.addEventListener('submit', function(e) {
            if (getOTPValue().length !== 6) {
                e.preventDefault();
                return;
            }
            
            verifyBtn.disabled = true;
            verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
        });
        
        // Handle resend
        resendBtn.addEventListener('click', function(e) {
            e.target.disabled = true;
            e.target.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        });
        
        // Auto-submit when all digits are entered
        updateHiddenInput();
    });
    </script>

    <style>
    .auth-container.otp-mode {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .email-info {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 16px;
        padding: 12px 20px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 25px;
        font-family: 'Courier New', monospace;
        font-size: 14px;
        color: rgba(255, 255, 255, 0.9);
    }
    
    .otp-inputs {
        display: flex;
        justify-content: center;
        gap: 12px;
        margin: 20px 0;
    }
    
    .otp-digit {
        width: 50px;
        height: 60px;
        text-align: center;
        font-size: 24px;
        font-weight: bold;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.1);
        color: white;
        transition: all 0.3s ease;
    }
    
    .otp-digit:focus {
        outline: none;
        border-color: #dc2626;
        background: rgba(255, 255, 255, 0.15);
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.3);
        transform: scale(1.05);
    }
    
    .success-message {
        background: rgba(34, 197, 94, 0.2);
        border: 1px solid rgba(34, 197, 94, 0.5);
        color: #10b981;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .resend-section {
        text-align: center;
        margin: 20px 0;
    }
    
    .resend-form {
        display: inline;
    }
    
    .link-btn {
        background: none;
        border: none;
        color: #dc2626;
        text-decoration: underline;
        cursor: pointer;
        font-size: 14px;
        padding: 4px 0;
        transition: color 0.3s ease;
    }
    
    .link-btn:hover {
        color: #b91c1c;
    }
    
    .back-link {
        text-align: center;
        margin-top: 16px;
    }
    
    .btn-submit:disabled,
    .btn-submit.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    @media (max-width: 768px) {
        .otp-digit {
            width: 40px;
            height: 50px;
            font-size: 20px;
        }
        
        .otp-inputs {
            gap: 8px;
        }
    }
    </style>
</x-guest-layout>