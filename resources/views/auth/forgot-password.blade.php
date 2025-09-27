<x-guest-layout>
    <div class="forgot-wrapper">
        <div class="forgot-container">
            <div class="forgot-header">
                <div class="ms-logo">
                    <!-- <svg width="40" height="40" viewBox="0 0 23 23" xmlns="http://www.w3.org/2000/svg"> -->
                        <path d="M1 1h10v10H1z" fill="#f25022"/>
                        <path d="M12 1h10v10H12z" fill="#00a4ef"/>
                        <path d="M1 12h10v10H1z" fill="#ffb900"/>
                        <path d="M12 12h10v10H12z" fill="#7fba00"/>
                    </svg>
                </div>
                <h1>Reset your password</h1>
                <p>Enter your McLawis College email address and we'll send you a password reset link.</p>
            </div>

            <div class="forgot-form">
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="form-group">
                        <input 
                            id="email" 
                            type="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            placeholder="someone@mcclawis.edu.ph"
                            required 
                            autofocus
                            autocomplete="username"
                            class="ms-input @error('email') error @enderror"
                        >
                        @error('email')
                            <div class="error-msg">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-paper-plane"></i>
                            Send password reset link
                        </button>
                    </div>

                    <div class="form-footer">
                        <div class="back-link">
                            <a href="{{ route('login') }}">
                                <i class="fas fa-arrow-left"></i>
                                Back to sign in
                            </a>
                        </div>
                        
                        <div class="help-section">
                            <div class="help-item">
                                <i class="fas fa-info-circle"></i>
                                <div>
                                    <strong>Having trouble?</strong>
                                    <p>Make sure to use your official McLawis College email address (@mcclawis.edu.ph)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

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

        .forgot-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .forgot-container {
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

        .forgot-header {
            text-align: center;
            padding: 40px 40px 20px 40px;
        }

        .ms-logo {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
        }

        .forgot-header h1 {
            font-size: 24px;
            font-weight: 600;
            color: #323130;
            margin-bottom: 12px;
        }

        .forgot-header p {
            font-size: 15px;
            color: #605e5c;
            line-height: 1.4;
        }

        .forgot-form {
            padding: 0 40px 40px 40px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .ms-input {
            width: 100%;
            padding: 11px 12px;
            font-size: 15px;
            border: 1px solid #605e5c;
            border-radius: 2px;
            background: white;
            transition: all 0.2s ease;
            outline: none;
        }

        .ms-input:focus {
            border-color: #0078d4;
            box-shadow: 0 0 0 1px #0078d4;
        }

        .ms-input.error {
            border-color: #d13438;
            box-shadow: 0 0 0 1px #d13438;
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

        .btn-primary {
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
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary:hover {
            background: #106ebe;
        }

        .form-footer {
            margin-top: 32px;
        }

        .back-link {
            text-align: center;
            margin-bottom: 24px;
        }

        .back-link a {
            color: #0078d4;
            text-decoration: none;
            font-size: 15px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        .help-section {
            border-top: 1px solid #edebe9;
            padding-top: 24px;
        }

        .help-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            background: #f3f2f1;
            padding: 16px;
            border-radius: 2px;
        }

        .help-item i {
            color: #0078d4;
            margin-top: 2px;
            flex-shrink: 0;
        }

        .help-item strong {
            display: block;
            margin-bottom: 4px;
            font-size: 14px;
            color: #323130;
        }

        .help-item p {
            font-size: 14px;
            color: #605e5c;
            line-height: 1.3;
            margin: 0;
        }

        .mb-4 {
            margin-bottom: 24px;
        }

        .mb-4 div {
            padding: 16px;
            background: #dff6dd;
            border: 1px solid #107c10;
            border-radius: 2px;
            color: #107c10;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .mb-4 div::before {
            content: "\f00c";
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
        }

        @media (max-width: 520px) {
            .forgot-container {
                max-width: 100%;
                border-radius: 0;
                box-shadow: none;
                min-height: 100vh;
            }

            .forgot-header,
            .forgot-form {
                padding-left: 24px;
                padding-right: 24px;
            }

            .forgot-header {
                padding-top: 60px;
            }

            .forgot-header h1 {
                font-size: 22px;
            }
        }
    </style>
</x-guest-layout>