<x-guest-layout>
    <div class="auth-wrapper">
        <div class="auth-container">
            <div class="auth-header">
                <div class="ms-logo">
                    <svg width="32" height="32" viewBox="0 0 23 23" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 1h10v10H1z" fill="#f25022"/>
                        <path d="M12 1h10v10H12z" fill="#00a4ef"/>
                        <path d="M1 12h10v10H1z" fill="#ffb900"/>
                        <path d="M12 12h10v10H12z" fill="#7fba00"/>
                    </svg>
                </div>
                <h1>Complete Registration</h1>
                <p>Create your EventAps account</p>
                @if(session('verified_email'))
                    <div class="verified-email">
                        <i class="fas fa-check-circle"></i>
                        Email verified: <strong>{{ session('verified_email') }}</strong>
                    </div>
                @endif
            </div>

            <form method="POST" action="{{ route('register') }}" class="auth-form">
                @csrf

                <!-- Student ID -->
                <div class="form-group">
                    <i class="fas fa-id-card"></i>
                    <input id="id_number" type="text" name="id_number" 
                           value="{{ old('id_number') }}" 
                           placeholder="Student ID Number" required autocomplete="off">
                    <x-input-error :messages="$errors->get('id_number')" class="error-msg" />
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <i class="fas fa-user"></i>
                        <input id="first_name" type="text" name="first_name" 
                               value="{{ old('first_name') }}" 
                               placeholder="First Name" required autofocus autocomplete="given-name">
                        <x-input-error :messages="$errors->get('first_name')" class="error-msg" />
                    </div>

                    <div class="form-group">
                        <i class="fas fa-user"></i>
                        <input id="last_name" type="text" name="last_name" 
                               value="{{ old('last_name') }}" 
                               placeholder="Last Name" required autocomplete="family-name">
                        <x-input-error :messages="$errors->get('last_name')" class="error-msg" />
                    </div>
                </div>

                <div class="form-group">
                    <i class="fas fa-user-circle"></i>
                    <input id="middle_name" type="text" name="middle_name" 
                           value="{{ old('middle_name') }}" 
                           placeholder="Middle Name (Optional)" autocomplete="additional-name">
                    <x-input-error :messages="$errors->get('middle_name')" class="error-msg" />
                </div>

                <div class="form-group">
                    <i class="fas fa-envelope"></i>
                    <input id="email" type="email" name="email" 
                           value="{{ old('email', session('verified_email')) }}" 
                           placeholder="McLawis College Email" required 
                           autocomplete="username"
                           readonly
                           style="background-color: #f8f9fa; cursor: not-allowed;">
                    <x-input-error :messages="$errors->get('email')" class="error-msg" />
                </div>

                <div class="form-group">
                    <i class="fas fa-graduation-cap"></i>
                    <select id="department" name="department" required>
                        <option value="">Select Department</option>
                        <option value="BSIT" {{ old('department') == 'BSIT' ? 'selected' : '' }}>Bachelor of Science in Information Technology</option>
                        <option value="BSBA" {{ old('department') == 'BSBA' ? 'selected' : '' }}>Bachelor of Science in Business Administration</option>
                        <option value="BSED" {{ old('department') == 'BSED' ? 'selected' : '' }}>Bachelor of Science in Education</option>
                        <option value="BEED" {{ old('department') == 'BEED' ? 'selected' : '' }}>Bachelor of Elementary Education</option>
                        <option value="BSHM" {{ old('department') == 'BSHM' ? 'selected' : '' }}>Bachelor of Science in Hospitality Management</option>
                    </select>
                    <x-input-error :messages="$errors->get('department')" class="error-msg" />
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <i class="fas fa-lock"></i>
                        <input id="password" type="password" name="password" 
                               placeholder="Password" required autocomplete="new-password">
                        <x-input-error :messages="$errors->get('password')" class="error-msg" />
                    </div>

                    <div class="form-group">
                        <i class="fas fa-check-circle"></i>
                        <input id="password_confirmation" type="password" 
                               name="password_confirmation" 
                               placeholder="Confirm Password" required autocomplete="new-password">
                        <x-input-error :messages="$errors->get('password_confirmation')" class="error-msg" />
                    </div>
                </div>

                <!-- Hidden fields -->
                <input type="hidden" name="role" value="student">
                <input type="hidden" name="status" value="active">

                <button type="submit" class="btn-submit">
                    <i class="fas fa-user-plus"></i>
                    Create Account
                </button>

                <div class="auth-links">
                    <p>Already registered? 
                       <a href="{{ route('login') }}">Sign in here</a>
                    </p>
                </div>
            </form>
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
            padding: 20px 15px;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        .auth-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 520px;
            animation: fadeIn 0.4s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .auth-header {
            background: white;
            color: #323130;
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
            color: #605e5c;
            font-size: 15px;
        }

        .verified-email {
            margin-top: 16px;
            padding: 12px;
            background: #dff6dd;
            border: 1px solid #107c10;
            border-radius: 4px;
            color: #107c10;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .auth-form {
            padding: 25px;
        }

        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-row .form-group {
            flex: 1;
            margin-bottom: 0;
        }

        .form-group {
            position: relative;
            margin-bottom: 16px;
        }

        .form-group i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #0078d4;
            font-size: 16px;
            z-index: 1;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 11px 12px 11px 40px;
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
            background-position: right 12px center;
            background-size: 16px;
            padding-right: 40px;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #0078d4;
            box-shadow: 0 0 0 1px #0078d4;
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
            margin: 20px 0 15px 0;
            transition: background 0.2s ease;
        }

        .btn-submit:hover {
            background: #106ebe;
        }

        .auth-links {
            text-align: center;
            color: #605e5c;
            font-size: 15px;
        }

        .auth-links a {
            color: #0078d4;
            text-decoration: none;
            font-weight: 600;
        }

        .auth-links a:hover { 
            text-decoration: underline; 
        }

        .error-msg {
            color: #d13438;
            font-size: 13px;
            margin-top: 4px;
            display: block;
        }

        @media (max-width: 580px) {
            .auth-wrapper { padding: 15px 10px; }
            
            .form-row {
                flex-direction: column;
                gap: 0;
            }

            .form-row .form-group {
                margin-bottom: 16px;
            }

            .auth-header, .auth-form {
                padding: 20px 15px;
            }
        }
    </style>
</x-guest-layout>
