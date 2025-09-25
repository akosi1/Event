<x-guest-layout>
    <link rel="stylesheet" href="{{ asset('user/login_register/login_register.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <div class="auth-container">
        <div class="floating-particles" id="particles"></div>
        <div class="diagonal-section"></div>

        <!-- Welcome Section -->
        <div class="welcome-section">
            <h1>VERIFY OTP</h1>
            <p>Enter the 6-digit code sent to your MS365 email</p>
        </div>

        <!-- OTP Form Section -->
        <div class="form-section">
            <div class="form-content">
                <div class="form-title">Email Verification</div>

                <div class="otp-info">
                    <p><i class="fas fa-envelope"></i> {{ $email }}</p>
                    <p class="otp-timer">Code expires in: <span id="countdown">10:00</span></p>
                </div>

                <form id="otp-form">
                    @csrf
                    <input type="hidden" id="email" value="{{ $email }}">
                    <input type="hidden" id="type" value="{{ $type }}">

                    <!-- OTP Input -->
                    <div class="form-group">
                        <div class="otp-inputs">
                            <input type="text" class="otp-digit" maxlength="1" pattern="[0-9]" data-index="0">
                            <input type="text" class="otp-digit" maxlength="1" pattern="[0-9]" data-index="1">
                            <input type="text" class="otp-digit" maxlength="1" pattern="[0-9]" data-index="2">
                            <input type="text" class="otp-digit" maxlength="1" pattern="[0-9]" data-index="3">
                            <input type="text" class="otp-digit" maxlength="1" pattern="[0-9]" data-index="4">
                            <input type="text" class="otp-digit" maxlength="1" pattern="[0-9]" data-index="5">
                        </div>
                        <div class="error-msg" id="otp-error" style="display: none;"></div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-submit" id="verify-btn">
                        <i class="fas fa-check"></i>
                        Verify Code
                    </button>

                    <!-- Resend Link -->
                    <div class="auth-links">
                        <p>Didn't receive the code?</p>
                        <button type="button" id="resend-btn" class="resend-link" disabled>
                            <i class="fas fa-redo"></i>
                            Resend OTP (<span id="resend-countdown">60</span>s)
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
    .otp-info {
        text-align: center;
        margin-bottom: 20px;
        padding: 15px;
        background: rgba(220, 38, 38, 0.1);
        border: 1px solid rgba(220, 38, 38, 0.2);
        border-radius: 8px;
        color: rgba(255, 255, 255, 0.9);
    }

    .otp-info p {
        margin: 5px 0;
        font-size: 13px;
    }

    .otp-timer {
        color: #ef4444 !important;
        font-weight: 600;
    }

    .otp-inputs {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-bottom: 20px;
    }

    .otp-digit {
        width: 45px;
        height: 45px;
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        background: rgba(255, 255, 255, 0.1);
        border: 2px solid rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        color: white;
        transition: all 0.3s ease;
    }

    .otp-digit:focus {
        outline: none;
        border-color: #dc2626;
        background: rgba(220, 38, 38, 0.1);
        box-shadow: 0 0 10px rgba(220, 38, 38, 0.3);
    }

    .otp-digit.filled {
        background: rgba(220, 38, 38, 0.2);
        border-color: #dc2626;
    }

    .otp-digit.error {
        border-color: #ef4444;
        background: rgba(239, 68, 68, 0.2);
        animation: shake 0.5s ease-in-out;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }

    .resend-link {
        background: none;
        border: none;
        color: rgba(255, 255, 255, 0.6);
        cursor: not-allowed;
        font-size: 13px;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .resend-link:not(:disabled) {
        color: #ef4444;
        cursor: pointer;
    }

    .resend-link:not(:disabled):hover {
        color: #ffffff;
        text-shadow: 0 0 10px rgba(239, 68, 68, 0.8);
    }

    .success-message {
        color: #10b981;
        text-align: center;
        margin-bottom: 15px;
        padding: 10px;
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.2);
        border-radius: 8px;
    }

    @media (max-width: 480px) {
        .otp-inputs {
            gap: 5px;
        }
        
        .otp-digit {
            width: 35px;
            height: 35px;
            font-size: 16px;
        }
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const otpInputs = document.querySelectorAll('.otp-digit');
        const form = document.getElementById('otp-form');
        const errorDiv = document.getElementById('otp-error');
        const resendBtn = document.getElementById('resend-btn');
        const verifyBtn = document.getElementById('verify-btn');
        
        let countdownTimer;
        let resendCountdownTimer;
        let expiryTime = 10 * 60; // 10 minutes in seconds
        let resendCooldown = 60; // 60 seconds

        // OTP input handling
        otpInputs.forEach((input, index) => {
            input.addEventListener('input', function(e) {
                const value = e.target.value;
                
                if (!/^\d$/.test(value)) {
                    e.target.value = '';
                    return;
                }
                
                e.target.classList.add('filled');
                
                // Move to next input
                if (index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
                
                // Auto-submit when all digits are filled
                if (index === otpInputs.length - 1) {
                    setTimeout(() => form.dispatchEvent(new Event('submit')), 100);
                }
            });
            
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    otpInputs[index - 1].focus();
                    otpInputs[index - 1].classList.remove('filled');
                }
            });
            
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const paste = e.clipboardData.getData('text');
                const digits = paste.replace(/\D/g, '').slice(0, 6);
                
                digits.split('').forEach((digit, i) => {
                    if (otpInputs[i]) {
                        otpInputs[i].value = digit;
                        otpInputs[i].classList.add('filled');
                    }
                });
                
                if (digits.length === 6) {
                    setTimeout(() => form.dispatchEvent(new Event('submit')), 100);
                }
            });
        });

        // Form submission
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const otpCode = Array.from(otpInputs).map(input => input.value).join('');
            
            if (otpCode.length !== 6) {
                showError('Please enter all 6 digits');
                return;
            }
            
            verifyBtn.disabled = true;
            verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
            
            try {
                const response = await fetch('{{ route("otp.verify") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        otp_code: otpCode,
                        email: document.getElementById('email').value,
                        type: document.getElementById('type').value
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showSuccess('OTP verified successfully! Redirecting...');
                    setTimeout(() => {
                        window.location.href = data.redirect_url || '{{ route("dashboard") }}';
                    }, 1500);
                } else {
                    showError(data.message || 'Invalid OTP. Please try again.');
                    clearInputs();
                    otpInputs[0].focus();
                }
            } catch (error) {
                showError('Network error. Please try again.');
                clearInputs();
                otpInputs[0].focus();
            }
            
            verifyBtn.disabled = false;
            verifyBtn.innerHTML = '<i class="fas fa-check"></i> Verify Code';
        });

        // Resend OTP
        resendBtn.addEventListener('click', async function() {
            if (resendBtn.disabled) return;
            
            resendBtn.disabled = true;
            resendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            
            try {
                const response = await fetch('{{ route("otp.resend") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showSuccess('New OTP sent successfully!');
                    startResendCooldown();
                    expiryTime = data.expires_in || 600;
                    startCountdown();
                } else {
                    showError(data.message || 'Failed to send OTP');
                    if (data.retry_after) {
                        resendCooldown = data.retry_after;
                        startResendCooldown();
                    }
                }
            } catch (error) {
                showError('Network error. Please try again.');
            }
            
            if (!resendBtn.disabled) {
                resendBtn.innerHTML = '<i class="fas fa-redo"></i> Resend OTP';
            }
        });

        function showError(message) {
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
            otpInputs.forEach(input => input.classList.add('error'));
            setTimeout(() => {
                otpInputs.forEach(input => input.classList.remove('error'));
            }, 500);
        }

        function showSuccess(message) {
            errorDiv.className = 'success-message';
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
        }

        function clearInputs() {
            otpInputs.forEach(input => {
                input.value = '';
                input.classList.remove('filled');
            });
        }

        function startCountdown() {
            clearInterval(countdownTimer);
            const countdownElement = document.getElementById('countdown');
            
            countdownTimer = setInterval(() => {
                if (expiryTime <= 0) {
                    clearInterval(countdownTimer);
                    countdownElement.textContent = 'Expired';
                    countdownElement.style.color = '#ef4444';
                    showError('OTP has expired. Please request a new one.');
                    return;
                }
                
                const minutes = Math.floor(expiryTime / 60);
                const seconds = expiryTime % 60;
                countdownElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                expiryTime--;
            }, 1000);
        }

        function startResendCooldown() {
            const resendCountdownElement = document.getElementById('resend-countdown');
            
            resendCountdownTimer = setInterval(() => {
                if (resendCooldown <= 0) {
                    clearInterval(resendCountdownTimer);
                    resendBtn.disabled = false;
                    resendBtn.innerHTML = '<i class="fas fa-redo"></i> Resend OTP';
                    return;
                }
                
                resendCountdownElement.textContent = resendCooldown;
                resendCooldown--;
            }, 1000);
        }

        // Initialize timers
        startCountdown();
        startResendCooldown();
        
        // Focus first input
        otpInputs[0].focus();
    });
    </script>
</x-guest-layout>