<x-guest-layout>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            background: url("{{ asset('images/mcc background.jpg') }}") center/cover no-repeat;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;
            margin: 0;
            padding: 0;
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

        .auth-wrapper {
            width: 100%;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0;
            position: relative;
            z-index: 2;
            overflow: hidden;
        }

        .auth-container {
            background: transparent;
            backdrop-filter: none;
            border-radius: 0;
            padding: 30px;
            width: 100%;
            max-width: 420px;
            box-shadow: none;
            position: relative;
            z-index: 2;
            border: none;
            animation: fadeIn 0.6s ease-out;
            overflow: hidden;
            height: fit-content;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 24px;
        }

        .auth-header h1 {
            font-size: 28px;
            font-weight: 600;
            color: #ffffff;
            text-align: center;
            margin-bottom: 6px;
            letter-spacing: 1px;
            font-family: 'Oswald', sans-serif;
            text-transform: uppercase;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .auth-header p {
            text-align: center;
            color: #e0e0e0;
            margin-bottom: 0;
            font-size: 14px;
            font-weight: 400;
            font-family: 'Roboto Condensed', sans-serif;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

        .auth-form {
            width: 100%;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .input-wrapper {
            position: relative;
        }

        .form-control,
        .form-select {
            width: 100%;
            padding: 16px 16px 6px 45px;
            border: 2px solid #ddd;
            border-radius: 0;
            font-size: 16px;
            outline: none;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            font-family: 'Poppins', sans-serif;
            font-weight: 400;
            color: #000000;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #6b2c91;
            background: rgba(255, 255, 255, 1);
            box-shadow: 0 4px 15px rgba(107, 44, 145, 0.2);
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            color: #666;
            transition: color 0.3s ease;
            z-index: 3;
            cursor: pointer;
        }

        .form-control:focus ~ .input-icon,
        .form-select:focus ~ .input-icon {
            color: #6b2c91;
        }

        .input-label {
            position: absolute;
            left: 45px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-size: 16px;
            pointer-events: none;
            transition: all 0.3s ease;
            background: transparent;
            padding: 0 5px;
            font-family: 'Poppins', sans-serif;
            font-weight: 400;
            z-index: 2;
        }

        .form-control:focus + .input-label,
        .form-control:not(:placeholder-shown) + .input-label,
        .form-select:focus + .select-label,
        .form-select:not(:placeholder-shown) + .select-label {
            top: 8px;
            transform: translateY(0);
            font-size: 11px;
            color: #6b2c91;
            font-weight: 600;
            left: 40px;
            background: transparent;
            border-radius: 0;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        .form-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%23666' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 12px center;
            background-repeat: no-repeat;
            background-size: 16px;
            padding-right: 40px;
        }

        .form-select option {
            background: white;
            color: #000;
            padding: 10px;
        }

        .select-label {
            position: absolute;
            left: 45px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-size: 16px;
            pointer-events: none;
            transition: all 0.3s ease;
            background: transparent;
            padding: 0 5px;
            font-family: 'Poppins', sans-serif;
            font-weight: 400;
            z-index: 2;
        }

        .btn-submit {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #e53e3e 0%, #d53f41 100%);
            color: white;
            border: none;
            border-radius: 0;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Oswald', sans-serif;
            text-transform: uppercase;
            letter-spacing: 2px;
            box-shadow: 0 4px 15px rgba(229, 62, 62, 0.3);
            margin-bottom: 20px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(229, 62, 62, 0.4);
            background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
        }

        .btn-submit:active {
            transform: translateY(-1px);
        }

        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .auth-links {
            text-align: center;
        }

        .auth-links p {
            color: #ffffff;
            font-size: 14px;
            font-family: 'Roboto Condensed', sans-serif;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
            margin: 0;
        }

        .auth-links a {
            color: #ffffff;
            text-decoration: none;
            font-weight: 600;
        }

        .auth-links a:hover {
            text-decoration: underline;
            color: #e0e0e0;
        }

        .error-msg {
            color: #ff6b6b;
            font-size: 11px;
            margin-top: 4px;
            font-family: 'Poppins', sans-serif;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        .mb-4 {
            margin-bottom: 18px;
            background: rgba(217, 237, 247, 0.9);
            border: 1px solid #bee5eb;
            padding: 8px 12px;
            border-radius: 4px;
            color: #0c5460;
            font-size: 13px;
        }

        /* MOBILE RESPONSIVE ADJUSTMENTS */
        @media (max-width: 768px) {
            .auth-container {
                padding: 24px;
                max-width: 95vw;
            }

            .auth-header h1 {
                font-size: 26px;
            }

            .auth-header p {
                font-size: 13px;
            }

            .form-control,
            .form-select {
                padding: 14px 14px 6px 42px;
                font-size: 15px;
            }

            .input-icon {
                left: 12px;
                width: 16px;
                height: 16px;
            }

            .input-label,
            .select-label {
                left: 42px;
                font-size: 15px;
            }

            .form-control:focus + .input-label,
            .form-control:not(:placeholder-shown) + .input-label,
            .form-select:focus + .select-label,
            .form-select:not(:placeholder-shown) + .select-label {
                top: 6px;
                left: 38px;
                font-size: 10px;
            }
        }

        @media (max-width: 480px) {
            .auth-container {
                padding: 20px;
            }

            .auth-header h1 {
                font-size: 24px;
                margin-bottom: 5px;
            }

            .auth-header p {
                font-size: 12px;
            }

            .form-control,
            .form-select {
                padding: 12px 12px 6px 40px;
                font-size: 14px;
            }

            .input-icon {
                left: 10px;
                width: 14px;
                height: 14px;
            }

            .input-label,
            .select-label {
                left: 40px;
                font-size: 14px;
            }

            .form-control:focus + .input-label,
            .form-control:not(:placeholder-shown) + .input-label,
            .form-select:focus + .select-label,
            .form-select:not(:placeholder-shown) + .select-label {
                top: 5px;
                left: 36px;
                font-size: 10px;
            }

            .btn-submit {
                padding: 14px;
                font-size: 15px;
            }
        }

        /* Add subtle animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .auth-container {
            animation: fadeIn 0.6s ease-out;
        }

        /* Enhanced interactive effects */
        .input-wrapper:focus-within {
            transform: scale(1.01);
            transition: transform 0.2s ease;
        }

        .input-wrapper:not(:focus-within) {
            transform: scale(1);
            transition: transform 0.2s ease;
        }
    </style>

    <div class="auth-wrapper">
        <div class="auth-container">
            <div class="auth-header">
                <h1>Welcome Back</h1>
                <p>Sign in to your EventAps account</p>
            </div>

            <div class="auth-form">
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- ID NUMBER FIELD -->
                    <div class="form-group">
                        <div class="input-wrapper">
                            <input id="id_number" type="text" name="id_number" value="{{ old('id_number') }}" 
                                   class="form-control" placeholder=" " required autocomplete="username" autofocus>
                            <label class="input-label">ID Number (e.g. 2019-1313)</label>
                            <i class="fas fa-id-card input-icon"></i>
                        </div>
                        <x-input-error :messages="$errors->get('id_number')" class="error-msg" />
                    </div>

                    <!-- PASSWORD FIELD -->
                    <div class="form-group">
                        <div class="input-wrapper">
                            <input id="password" type="password" name="password" 
                                   class="form-control" placeholder=" " required autocomplete="current-password">
                            <label class="input-label">Password</label>
                            <i class="fas fa-lock input-icon"></i>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="error-msg" />
                    </div>

                    <!-- DEPARTMENT FIELD -->
                    <div class="form-group">
                        <div class="input-wrapper">
                            <select id="department" name="department" class="form-select" required>
                                <option value=""></option>
                                <option value="BSIT" {{ old('department') == 'BSIT' ? 'selected' : '' }}>BSIT</option>
                                <option value="BSBA" {{ old('department') == 'BSBA' ? 'selected' : '' }}>BSBA</option>
                                <option value="BSED" {{ old('department') == 'BSED' ? 'selected' : '' }}>BSED</option>
                                <option value="BEED" {{ old('department') == 'BEED' ? 'selected' : '' }}>BEED</option>
                                <option value="BSHM" {{ old('department') == 'BSHM' ? 'selected' : '' }}>BSHM</option>
                            </select>
                            <label class="select-label">Select Your Department</label>
                            <i class="fas fa-graduation-cap input-icon"></i>
                        </div>
                        <x-input-error :messages="$errors->get('department')" class="error-msg" />
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-sign-in-alt"></i>
                        {{ __('Sign In') }}
                    </button>

                    <div class="auth-links">
                        <p>{{ __("Don't have an account?") }} 
                           <a href="{{ route('register') }}">{{ __('Sign up here') }}</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>