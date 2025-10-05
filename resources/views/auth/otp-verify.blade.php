<x-guest-layout>
    <div class="otp-wrapper">
        <div class="otp-container">
            <div class="otp-header">
                <h1>verification</h1>
                <p>We sent a code to <strong>{{ session('email') }}</strong></p>
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
                                autocomplete="off"
                                autofocus
                                class="otp-input @error('otp') error @enderror"
                                pattern="[0-9]{6}"
                                inputmode="numeric"
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
        // Auto-format OTP input
        const otpInput = document.getElementById('otp');
        otpInput.addEventListener('input', function(e) {
            // Remove any non-numeric characters
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
            
            // Auto-submit when 6 digits are entered
            if (e.target.value.length === 6) {
                setTimeout(() => {
                    e.target.form.submit();
                }, 500);
            }
        });

        // Auto-focus and select all on page load
        document.addEventListener('DOMContentLoaded', function() {
            otpInput.focus();
            otpInput.select();
        });
    </script>
   
</x-guest-layout>