<x-guest-layout>
    <div class="forgot-wrapper">
        <div class="forgot-container">
            <div class="forgot-header">
                <div class="ms-logo">
                    <img src="images/logo.png" alt="Logo" width="48" height="48">
                </div>
                <h1>Forgot your password?</h1>
                <p>Enter your McLawis College email address</p>
            </div>

            <div class="forgot-form">
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="form-group">
                        <div class="input-wrapper">
                            <i class="fas fa-envelope input-icon"></i>
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
                        </div>
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

                    <div class="divider">or</div>

                    <div class="form-footer">
                        <div class="back-link">
                            <a href="{{ route('login') }}" class="btn-secondary">
                                <i class="fas fa-arrow-left"></i>
                                Back to sign in
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            background: #f5f5f5;
            color: #323130;
        }

        .forgot-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .forgot-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            padding: 32px 24px;
        }

        .forgot-header {
            text-align: center;
            margin-bottom: 24px;
        }

        .ms-logo {
            margin-bottom: 16px;
            display: flex;
            justify-content: center;
        }

        .forgot-header h1 {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #1a1a1a;
        }

        .forgot-header p {
            font-size: 14px;
            color: #666;
        }

        .forgot-form { width: 100%; }
        .form-group { margin-bottom: 16px; }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            color: #0078d4;
            font-size: 16px;
            pointer-events: none;
        }

        .ms-input {
            width: 100%;
            padding: 14px 14px 14px 44px;
            font-size: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            transition: all 0.2s ease;
            outline: none;
            background: white;
        }

        .ms-input:focus {
            border-color: #0078d4;
        }

        .ms-input.error {
            border-color: #d13438;
        }

        .error-msg {
            margin-top: 8px;
            padding: 8px 12px;
            background: #fef0f0;
            border: 1px solid #d13438;
            border-radius: 6px;
            color: #d13438;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-primary {
            width: 100%;
            padding: 14px;
            background: #0078d4;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary:hover { background: #106ebe; }
        .btn-primary:active { background: #005a9e; }

        .divider {
            text-align: center;
            color: #999;
            font-size: 14px;
            margin: 20px 0;
            position: relative;
        }

        .form-footer { margin-top: 0; }

        .back-link { text-align: center; }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 14px;
            background: white;
            color: #0078d4;
            border: 2px solid #0078d4;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-secondary:hover {
            background: #f0f7ff;
        }

        .mb-4 { margin-bottom: 16px; }

        .mb-4 div {
            padding: 12px 14px;
            background: #dff6dd;
            border: 1px solid #107c10;
            border-radius: 6px;
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

        /* Mobile Responsive - NO SCROLLING */
        @media (max-width: 520px) {
            .forgot-wrapper {
                padding: 0;
                align-items: stretch;
            }
            
            .forgot-container {
                border-radius: 0;
                box-shadow: none;
                min-height: 100vh;
                max-width: 100%;
                padding: 48px 24px;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            .forgot-header h1 { font-size: 20px; }
            .forgot-header p { font-size: 13px; }
            
            .ms-input { 
                font-size: 16px;
                padding: 13px 13px 13px 42px;
            }
            
            .btn-primary,
            .btn-secondary { 
                padding: 13px;
                font-size: 16px;
            }
        }

        @media (max-width: 375px) {
            .forgot-container { padding: 40px 20px; }
            .forgot-header h1 { font-size: 19px; }
        }
    </style>
</x-guest-layout>