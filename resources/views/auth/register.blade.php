<x-guest-layout>
    <div class="auth-wrapper">
        <div class="auth-container">
            <div class="auth-header">
                <h1>Create Account</h1>
                <p>Join EventAps to get started</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="auth-form">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <i class="fas fa-user"></i>
                        <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" 
                               placeholder="First Name" required autofocus autocomplete="given-name">
                        <x-input-error :messages="$errors->get('first_name')" class="error-msg" />
                    </div>

                    <div class="form-group">
                        <i class="fas fa-user"></i>
                        <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" 
                               placeholder="Last Name" required autocomplete="family-name">
                        <x-input-error :messages="$errors->get('last_name')" class="error-msg" />
                    </div>
                </div>

                <div class="form-group">
                    <i class="fas fa-user-circle"></i>
                    <input id="middle_name" type="text" name="middle_name" value="{{ old('middle_name') }}" 
                           placeholder="Middle Name (Optional)" autocomplete="additional-name">
                    <x-input-error :messages="$errors->get('middle_name')" class="error-msg" />
                </div>

                <div class="form-group">
                    <i class="fas fa-envelope"></i>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" 
                           placeholder="Email Address" required autocomplete="username">
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
                        <input id="password_confirmation" type="password" name="password_confirmation" 
                               placeholder="Confirm Password" required autocomplete="new-password">
                        <x-input-error :messages="$errors->get('password_confirmation')" class="error-msg" />
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-user-plus"></i>
                    {{ __('Sign Up') }}
                </button>

                <div class="auth-links">
                    <p>{{ __('Already registered?') }} 
                       <a href="{{ route('login') }}">{{ __('Sign in here') }}</a>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px 15px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .auth-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 520px;
            animation: slideUp 0.6s ease;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .auth-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            padding: 25px 20px;
            border-radius: 20px 20px 0 0;
        }

        .auth-header h1 {
            font-size: 1.8rem;
            font-weight: 300;
            margin-bottom: 5px;
        }

        .auth-header p {
            opacity: 0.9;
            font-size: 0.95rem;
        }

        .auth-form {
            padding: 25px 20px;
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
            margin-bottom: 15px;
        }

        .form-group i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #667eea;
            font-size: 0.95rem;
            z-index: 1;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px 12px 10px 38px;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            font-size: 0.9rem;
            background: #f8fafc;
            transition: all 0.3s ease;
        }

        .form-group select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23667eea' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            padding-right: 40px;
        }

        .form-group select option {
            padding: 8px 12px;
            background: white;
            color: #333;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 3px 10px rgba(102, 126, 234, 0.15);
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin: 20px 0 15px 0;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }

        .auth-links {
            text-align: center;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .auth-links a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .auth-links a:hover { color: #764ba2; }

        .error-msg {
            color: #e53e3e;
            font-size: 0.75rem;
            margin-top: 3px;
            display: block;
        }

        @media (max-width: 580px) {
            .auth-wrapper { padding: 15px 10px; }
            
            .form-row {
                flex-direction: column;
                gap: 0;
            }

            .form-row .form-group {
                margin-bottom: 15px;
            }

            .auth-header, .auth-form {
                padding: 20px 15px;
            }
        }

        @media (max-width: 400px) {
            .auth-container { max-width: 100%; }
            .form-group input,
            .form-group select { font-size: 0.85rem; }
        }
    </style>
</x-guest-layout>