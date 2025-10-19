<x-guest-layout>
    <div class="otp-wrapper">
        <div class="otp-container">
            <div class="otp-header">
                <h1>Verification</h1>
                <p>We sent a code to <strong>{{ htmlspecialchars(session('email'), ENT_QUOTES, 'UTF-8') }}</strong></p>
            </div>

            <div class="otp-form">
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('otp.verify.store') }}">
                    @csrf
                    <input type="hidden" name="email" value="{{ session('email') }}">

                    <div class="form-group">
                        <label for="otp" class="otp-label">Verification code</label>
                        <div class="otp-input-container">
                            <input 
                                id="otp" 
                                type="text" 
                                name="otp" 
                                maxlength="6" 
                                placeholder="000000"
                                required 
                                autocomplete="one-time-code"
                                autofocus
                                class="otp-input @error('otp') error @enderror"
                                pattern="[0-9]{6}"
                                inputmode="numeric"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);"
                            >
                            <div class="otp-hint">6-digit code</div>
                        </div>
                        @error('otp')
                            <div class="error-msg">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-verify">
                            <i class="fas fa-check-circle"></i>
                            Verify
                        </button>
                    </div>

                    <div class="form-footer">
                        <div class="divider">
                            <span>or</span>
                        </div>
                        
                        <div class="resend-section">
                            <p>Didn't receive the code?</p>
                            <form method="POST" action="{{ route('otp.resend') }}" class="resend-form">
                                @csrf
                                <input type="hidden" name="email" value="{{ session('email') }}">
                                <button type="submit" class="btn-resend">
                                    <i class="fas fa-sync-alt"></i>
                                    Send a new code
                                </button>
                            </form>
                        </div>
                        
                        <div class="back-link">
                            <a href="{{ route('ms365.verify') }}" class="btn-secondary">
                                Use a different email
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="{{ asset('user/otpverify/otpverify.css') }}">
    <style>
        body {
            background: url("{{ asset('images/mcc background.jpg') }}") center/cover no-repeat;
            position: relative;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const otpInput = document.getElementById('otp');
            if (otpInput) {
                otpInput.focus();
                otpInput.select();

                // Prevent non-numeric input and auto-submit on 6 digits
                otpInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/[^0-9]/g, '').slice(0, 6);
                    e.target.value = value;

                    if (value.length === 6) {
                        // Optional: auto-submit after short delay
                        setTimeout(() => {
                            if (e.target.form.checkValidity()) {
                                e.target.form.submit();
                            }
                        }, 300);
                    }
                });

                // Block paste of non-numeric data
                otpInput.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const pasted = (e.clipboardData || window.clipboardData).getData('text');
                    const numeric = pasted.replace(/[^0-9]/g, '').slice(0, 6);
                    otpInput.value = numeric;
                    if (numeric.length === 6) {
                        otpInput.form.submit();
                    }
                });
            }
        });
    </script>
</x-guest-layout>