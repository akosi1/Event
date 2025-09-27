<x-guest-layout>
    <div class="auth-wrapper">
        <div class="auth-container">
            <div class="auth-header">
                <div class="ms-logo">
                    <!-- <svg width="32" height="32" viewBox="0 0 23 23" xmlns="http://www.w3.org/2000/svg"> -->
                        <path d="M1 1h10v10H1z" fill="#f25022"/>
                        <path d="M12 1h10v10H12z" fill="#00a4ef"/>
                        <path d="M1 12h10v10H1z" fill="#ffb900"/>
                        <path d="M12 12h10v10H12z" fill="#7fba00"/>
                    </svg>
                </div>
                <h1>Sign in to EventAps</h1>
                <p>Use your McLawis College account</p>
            </div>

            <div class="auth-form">
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group">
                        <i class="fas fa-envelope"></i>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" 
                               placeholder="McLawis College Email" required autocomplete="username" autofocus>
                        <x-input-error :messages="$errors->get('email')" class="error-msg" />
                    </div>

                    <div class="form-group">
                        <i class="fas fa-lock"></i>
                        <input id="password" type="password" name="password" 
                               placeholder="Password" required autocomplete="current-password">
                        <x-input-error :messages="$errors->get('password')" class="error-msg" />
                    </div>

                    <div class="form-group">
                        <i class="fas fa-graduation-cap"></i>
                        <select id="department" name="department" required>
                            <option value="">Select Your Department</option>
                            <option value="BSIT" {{ old('department') == 'BSIT' ? 'selected' : '' }}>Bachelor of Science in Information Technology</option>
                            <option value="BSBA" {{ old('department') == 'BSBA' ? 'selected' : '' }}>Bachelor of Science in Business Administration</option>
                            <option value="BSED" {{ old('department') == 'BSED' ? 'selected' : '' }}>Bachelor of Science in Education</option>
                            <option value="BEED" {{ old('department') == 'BEED' ? 'selected' : '' }}>Bachelor of Elementary Education</option>
                            <option value="BSHM" {{ old('department') == 'BSHM' ? 'selected' : '' }}>Bachelor of Science in Hospitality Management</option>
                        </select>
                        <x-input-error :messages="$errors->get('department')" class="error-msg" />
                    </div>

                    <div class="form-options">
                        <label class="remember-me">
                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <span class="checkmark"></span>
                            Keep me signed in
                        </label>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-sign-in-alt"></i>
                        Sign in
                    </button>

                    <div class="form-footer">
                        <div class="forgot-password">
                            <a href="{{ route('password.request') }}">
                                <i class="fas fa-key"></i>
                                Forgot your password?
                            </a>
                        </div>
                        
                        <div class="divider">
                            <span>or</span>
                        </div>
                        
                        <div class="signup-link">
                            <p>Don't have an account?</p>
                            <a href="{{ route('ms365.verify') }}" class="btn-secondary">
                                Create account with McLawis email
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f5f5f5;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }

        .auth-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 420px;
            animation: fadeIn 0.4s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .auth-header {
            text-align: center;
            padding: 30px 25px 20px 25px;
            border-bottom: 1px solid #edebe9;
        }

        .ms-logo {
            margin-bottom: 16px;
            display: flex;
            justify-content: center;
        }

        .auth-header h1 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #323130;
        }

        .auth-header p {
            opacity: 0.8;
            font-size: 15px;
            color: #605e5c;
        }

        .auth-form {
            padding: 30px 25px;
        }

        .form-group {
            position: relative;
            margin-bottom: 20px;
        }

        .form-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #0078d4;
            z-index: 1;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 11px 15px 11px 45px;
            border: 1px solid #605e5c;
            border-radius: 2px;
            font-size: 15px;
            background: white;
            transition: all 0.2s ease;
            outline: none;
        }

        .form-group select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%230078d4' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 16px;
            padding-right: 45px;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #0078d4;
            box-shadow: 0 0 0 1px #0078d4;
        }

        .form-options {
            margin-bottom: 20px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            font-size: 15px;
            cursor: pointer;
            color: #323130;
        }

        .remember-me input {
            margin-right: 8px;
            width: auto;
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background: #0078d4;
            color: white;
            border: none;
            border-radius: 2px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 20px;
            transition: background 0.2s ease;
        }

        .btn-submit:hover {
            background: #106ebe;
        }

        .btn-secondary {
            display: inline-block;
            width: 100%;
            padding: 10px 16px;
            background: white;
            color: #0078d4;
            border: 1px solid #0078d4;
            border-radius: 2px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: all 0.2s ease;
            margin-top: 8px;
        }

        .btn-secondary:hover {
            background: #0078d4;
            color: white;
        }

        .form-footer {
            text-align: center;
        }

        .forgot-password {
            margin-bottom: 20px;
        }

        .forgot-password a {
            color: #0078d4;
            text-decoration: none;
            font-size: 15px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
        }

        .forgot-password a:hover {
            text-decoration: underline;
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
            background: #edebe9;
        }

        .divider span {
            background: white;
            padding: 0 15px;
            color: #605e5c;
            font-size: 14px;
        }

        .signup-link p {
            color: #605e5c;
            font-size: 15px;
            margin-bottom: 8px;
        }

        .error-msg {
            color: #d13438;
            font-size: 13px;
            margin-top: 5px;
            display: block;
        }

        .mb-4 div {
            padding: 16px;
            background: #dff6dd;
            border: 1px solid #107c10;
            border-radius: 2px;
            color: #107c10;
            font-size: 14px;
            margin-bottom: 20px;
        }

        @media (max-width: 480px) {
            .auth-wrapper { padding: 15px; }
            .auth-container { max-width: 100%; }
            .auth-header, .auth-form { padding: 25px 20px; }
        }
    </style>
</x-guest-layout>