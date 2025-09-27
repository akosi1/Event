<x-guest-layout>
    <div class="ms365-wrapper">
        <div class="ms365-container">
            <div class="ms365-header">
                <div class="ms-logo">
                    <!-- <svg width="40" height="40" viewBox="0 0 23 23" xmlns="http://www.w3.org/2000/svg"> -->
                        <path d="M1 1h10v10H1z" fill="#f25022"/>
                        <path d="M12 1h10v10H12z" fill="#00a4ef"/>
                        <path d="M1 12h10v10H1z" fill="#ffb900"/>
                        <path d="M12 12h10v10H12z" fill="#7fba00"/>
                    </svg>
                </div>
                <h1>Sign up for EventAps</h1>
                <p>Use your McLawis College email account</p>
            </div>

            <div class="ms365-form">
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('ms365.verify.store') }}">
                    @csrf

                    <div class="form-group">
                        <input 
                            id="email" 
                            type="email" 
                            name="email" 
                            value="{{ old('email', '') }}" 
                            placeholder="someone@mcclawis.edu.ph"
                            required 
                            autocomplete="username"
                            autofocus
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
                            Send verification code
                        </button>
                    </div>

                    <div class="form-footer">
                        <p class="help-text">
                            <i class="fas fa-info-circle"></i>
                            We'll send a 6-digit verification code to your McLawis College email address.
                        </p>
                        
                        <div class="auth-links">
                            <p>Already have an account? 
                               <a href="{{ route('login') }}">Sign in here</a>
                            </p>
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

        .ms365-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .ms365-container {
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

        .ms365-header {
            text-align: center;
            padding: 40px 40px 20px 40px;
        }

        .ms-logo {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
        }

        .ms365-header h1 {
            font-size: 24px;
            font-weight: 600;
            color: #323130;
            margin-bottom: 8px;
        }

        .ms365-header p {
            font-size: 15px;
            color: #605e5c;
        }

        .ms365-form {
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
        }

        .btn-primary:hover {
            background: #106ebe;
        }

        .btn-primary:active {
            background: #005a9e;
        }

        .form-footer {
            margin-top: 32px;
        }

        .help-text {
            background: #f3f2f1;
            border: 1px solid #edebe9;
            border-radius: 2px;
            padding: 16px;
            font-size: 14px;
            color: #605e5c;
            display: flex;
            align-items: flex-start;
            gap: 8px;
            margin-bottom: 24px;
        }

        .help-text i {
            color: #0078d4;
            margin-top: 2px;
            flex-shrink: 0;
        }

        .auth-links {
            text-align: center;
            padding-top: 16px;
            border-top: 1px solid #edebe9;
        }

        .auth-links p {
            font-size: 15px;
            color: #605e5c;
        }

        .auth-links a {
            color: #0078d4;
            text-decoration: none;
            font-weight: 600;
        }

        .auth-links a:hover {
            text-decoration: underline;
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
        }

        @media (max-width: 520px) {
            .ms365-container {
                max-width: 100%;
                border-radius: 0;
                box-shadow: none;
                min-height: 100vh;
            }

            .ms365-header,
            .ms365-form {
                padding-left: 24px;
                padding-right: 24px;
            }

            .ms365-header {
                padding-top: 60px;
            }

            .ms365-header h1 {
                font-size: 22px;
            }
        }
    </style>
</x-guest-layout>