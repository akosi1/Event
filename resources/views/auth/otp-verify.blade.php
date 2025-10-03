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
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            overflow: hidden;
            font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: url("{{ asset('images/mcc background.jpg') }}") center/cover no-repeat;
            position: relative;
        }

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

        .otp-wrapper {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            z-index: 2;
        }

        .otp-container {
            background: transparent;
            width: 100%;
            max-width: 440px;
            animation: fadeIn 0.6s ease-out;
            padding: 40px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .otp-header {
            text-align: center;
            margin-bottom: 30px;
        }



        .otp-header h1 {
            font-size: 38px;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 12px;
            letter-spacing: 1px;
            font-family: 'Oswald', sans-serif;
            text-transform: uppercase;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .otp-header p {
            color: #e0e0e0;
            font-size: 15px;
            font-weight: 400;
            font-family: 'Poppins', sans-serif;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

        .otp-header p strong {
            color: #ffffff;
            font-weight: 600;
        }

        .otp-form {
            max-height: calc(100vh - 200px);
            overflow-y: auto;
            scrollbar-width: none;
            padding-right: 5px;
        }

        .otp-form::-webkit-scrollbar {
            display: none;
        }

        .otp-form:hover,
        .otp-form:focus-within {
            scrollbar-width: auto;
        }

        .otp-form:hover::-webkit-scrollbar,
        .otp-form:focus-within::-webkit-scrollbar {
            width: 8px;
        }

        .otp-form:hover::-webkit-scrollbar-track,
        .otp-form:focus-within::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        .otp-form:hover::-webkit-scrollbar-thumb,
        .otp-form:focus-within::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 10px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .otp-label {
            display: block;
            font-size: 15px;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 8px;
            font-family: 'Poppins', sans-serif;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
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
            border: 2px solid #ddd;
            border-radius: 0;
            background: rgba(255, 255, 255, 0.95);
            transition: all 0.3s ease;
            outline: none;
            font-family: 'Courier New', monospace;
            color: #000000;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .otp-input:focus {
            border-color: #6b2c91;
            background: rgba(255, 255, 255, 1);
            box-shadow: 0 4px 15px rgba(107, 44, 145, 0.2);
        }

        .otp-input.error {
            border-color: #ff6b6b;
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.2);
        }

        .otp-hint {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 12px;
            color: #666;
            pointer-events: none;
            font-family: 'Poppins', sans-serif;
        }

        .error-msg {
            margin-top: 8px;
            padding: 10px 14px;
            background: rgba(255, 107, 107, 0.15);
            border-left: 3px solid #ff6b6b;
            color: #ff6b6b;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-family: 'Poppins', sans-serif;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        .btn-verify {
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
            transition: all 0.3s ease;
            font-family: 'Oswald', sans-serif;
            text-transform: uppercase;
            letter-spacing: 2px;
            box-shadow: 0 4px 15px rgba(229, 62, 62, 0.3);
        }

        .btn-verify:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(229, 62, 62, 0.4);
            background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
        }

        .btn-verify:active {
            transform: translateY(-1px);
        }

        .form-footer {
            margin-top: 32px;
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

        .resend-section {
            text-align: center;
            margin-bottom: 20px;
        }

        .resend-section p {
            font-size: 15px;
            color: #ffffff;
            margin-bottom: 8px;
            font-family: 'Poppins', sans-serif;
        }

        .resend-form {
            display: inline;
        }

        .btn-resend {
            background: none;
            border: none;
            color: #ffffff;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: underline;
            padding: 4px 8px;
            font-family: 'Poppins', sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }

        .btn-resend:hover {
            color: #e0e0e0;
        }

        .back-link {
            text-align: center;
        }

        .btn-secondary {
            display: inline-block;
            width: 100%;
            padding: 10px 16px;
            background: transparent;
            color: #ffffff;
            border: 2px solid rgba(255, 255, 255, 0.5);
            border-radius: 0;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.8);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 255, 255, 0.2);
        }

        .btn-secondary:active {
            background: rgba(255, 255, 255, 0.95);
            border-color: white;
            color: #6b2c91;
            transform: translateY(0);
        }

        .mb-4 div {
            padding: 12px 16px;
            background: rgba(217, 237, 247, 0.95);
            border: 1px solid #bee5eb;
            border-radius: 4px;
            color: #0c5460;
            font-size: 14px;
            margin-bottom: 24px;
            font-family: 'Poppins', sans-serif;
        }

        @media (max-width: 768px) {
            .otp-container {
                padding: 30px 25px;
                max-width: 100%;
            }

            .otp-header h1 {
                font-size: 28px;
            }

            .otp-header p {
                font-size: 13px;
            }

            .otp-input {
                font-size: 20px;
                letter-spacing: 6px;
                padding: 14px 10px;
            }

            .otp-hint {
                font-size: 11px;
                right: 8px;
            }

            .btn-verify {
                padding: 14px;
                font-size: 15px;
            }

            .resend-section {
                padding: 14px;
            }

            .resend-section p {
                font-size: 14px;
            }

            .btn-resend {
                font-size: 14px;
            }

            .back-link a {
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            .otp-wrapper {
                padding: 15px;
            }

            .otp-container {
                padding: 25px 20px;
            }

            .otp-header {
                margin-bottom: 25px;
            }

            .otp-header h1 {
                font-size: 24px;
                margin-bottom: 10px;
            }

            .otp-header p {
                font-size: 12px;
                line-height: 1.5;
            }

            .otp-label {
                font-size: 14px;
            }

            .otp-input {
                font-size: 18px;
                letter-spacing: 4px;
                padding: 12px 8px;
            }

            .otp-hint {
                font-size: 10px;
                right: 6px;
            }

            .btn-verify {
                padding: 12px;
                font-size: 14px;
                letter-spacing: 1.5px;
            }

            .form-footer {
                margin-top: 24px;
            }

            .resend-section {
                padding: 12px;
                margin-bottom: 16px;
            }

            .resend-section p {
                font-size: 13px;
            }

            .btn-resend {
                font-size: 13px;
            }

            .back-link a {
                font-size: 13px;
            }
        }

        @media (max-width: 360px) {
            .otp-header h1 {
                font-size: 20px;
            }

            .otp-input {
                font-size: 16px;
                letter-spacing: 3px;
            }
        }
    </style>
</x-guest-layout>