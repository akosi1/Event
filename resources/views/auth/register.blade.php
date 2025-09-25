<x-guest-layout>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            background: url("{{ asset('images/mcc background.jpg') }}") center/cover no-repeat;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden; /* Prevents body scroll */
            padding: 0;
            margin: 0;
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
            max-width: 100vw;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            position: relative;
            z-index: 2;
            overflow: hidden;
        }

        .auth-container {
            background: transparent;
            backdrop-filter: none;
            border-radius: 0;
            padding: 30px;
            width: 450px;
            max-width: 90vw;
            box-shadow: none;
            position: relative;
            z-index: 2;
            border: none;
            animation: fadeIn 0.6s ease-out;
            overflow: hidden;
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
            margin-bottom: 4px;
            letter-spacing: 1px;
            font-family: 'Oswald', sans-serif;
            text-transform: uppercase;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .auth-header p {
            text-align: center;
            color: #e0e0e0;
            margin-bottom: 0;
            font-size: 13px;
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
            margin-bottom: 18px;
            position: relative;
        }

        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 0;
        }

        .form-row .form-group {
            flex: 1;
            margin-bottom: 18px;
        }

        .input-wrapper {
            position: relative;
        }

        .form-control {
            width: 100%;
            padding: 16px 16px 6px 45px;
            border: 2px solid #ddd;
            border-radius: 0;
            font-size: 15px;
            outline: none;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            font-family: 'Poppins', sans-serif;
            font-weight: 400;
            color: #000000;
        }

        .form-control:focus {
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
            width: 16px;
            height: 16px;
            color: #666;
            transition: color 0.3s ease;
            z-index: 3;
            cursor: pointer;
        }

        .form-control:focus ~ .input-icon {
            color: #6b2c91;
        }

        .input-label {
            position: absolute;
            left: 45px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-size: 15px;
            pointer-events: none;
            transition: all 0.3s ease;
            background: transparent;
            padding: 0 4px;
            font-family: 'Poppins', sans-serif;
            font-weight: 400;
        }

        .form-control:focus + .input-label,
        .form-control:not(:placeholder-shown) + .input-label {
            top: 4px;
            transform: translateY(0);
            font-size: 10px;
            color: #6b2c91;
            background: rgba(255, 255, 255, 1);
            border-radius: 0;
            left: 40px;
            font-weight: 600;
        }

        .form-select {
            width: 100%;
            padding: 16px 16px 6px 45px;
            border: 2px solid #ddd;
            border-radius: 0;
            font-size: 15px;
            outline: none;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            font-family: 'Poppins', sans-serif;
            font-weight: 400;
            color: #000000;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%23666' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 12px center;
            background-repeat: no-repeat;
            background-size: 14px;
            padding-right: 35px;
        }

        .form-select:focus {
            border-color: #6b2c91;
            background: rgba(255, 255, 255, 1);
            box-shadow: 0 4px 15px rgba(107, 44, 145, 0.2);
        }

        .form-select option {
            background: white;
            color: #000;
            padding: 8px;
        }

        .select-label {
            position: absolute;
            left: 40px;
            top: 4px;
            transform: translateY(0);
            color: #6b2c91;
            font-size: 10px;
            pointer-events: none;
            background: rgba(255, 255, 255, 1);
            padding: 0 4px;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            border-radius: 0;
        }

        .btn-submit {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #e53e3e 0%, #d53f41 100%);
            color: white;
            border: none;
            border-radius: 0;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Oswald', sans-serif;
            text-transform: uppercase;
            letter-spacing: 2px;
            box-shadow: 0 4px 15px rgba(229, 62, 62, 0.3);
            margin-bottom: 18px;
            margin-top: 6px;
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
            font-size: 12px;
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
            font-size: 10px;
            margin-top: 3px;
            font-family: 'Poppins', sans-serif;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        /* Remove mobile responsiveness that causes scroll */
        @media (max-width: 768px) {
            body {
                padding: 0;
                justify-content: center;
                overflow-y: auto; /* Allow only if needed */
            }

            .auth-wrapper {
                padding: 20px 15px;
                justify-content: center;
                min-height: auto;
            }

            .auth-container {
                width: 100%;
                max-width: none;
                padding: 25px 20px;
                margin: 0;
            }

            .form-row {
                flex-direction: column;
                gap: 0;
                margin-bottom: 0;
            }

            .form-row .form-group {
                margin-bottom: 18px;
            }

            .form-group {
                margin-bottom: 18px;
            }

            .auth-header {
                margin-bottom: 20px;
            }

            .auth-header h1 {
                font-size: 24px;
                margin-bottom: 3px;
            }

            .auth-header p {
                font-size: 12px;
            }

            .form-control, .form-select {
                padding: 14px 14px 5px 40px;
                font-size: 14px;
            }

            .input-icon {
                left: 12px;
                width: 14px;
                height: 14px;
            }

            .input-label {
                left: 40px;
                font-size: 14px;
            }

            .form-control:focus + .input-label,
            .form-control:not(:placeholder-shown) + .input-label {
                top: 3px;
                font-size: 9px;
                left: 35px;
            }

            .select-label {
                left: 35px;
                top: 3px;
                font-size: 9px;
            }

            .btn-submit {
                padding: 13px;
                font-size: 14px;
                margin-top: 4px;
            }
        }

        @media (max-width: 480px) {
            .auth-wrapper {
                padding: 15px 10px;
            }

            .auth-container {
                padding: 20px 15px;
            }

            .auth-header {
                margin-bottom: 18px;
            }

            .auth-header h1 {
                font-size: 22px;
            }

            .form-control, .form-select {
                padding: 13px 12px 4px 35px;
                font-size: 13px;
            }

            .input-icon {
                left: 10px;
                width: 13px;
                height: 13px;
            }

            .input-label {
                left: 35px;
                font-size: 13px;
            }

            .form-control:focus + .input-label,
            .form-control:not(:placeholder-shown) + .input-label {
                left: 30px;
                font-size: 8px;
            }

            .select-label {
                left: 30px;
                font-size: 8px;
            }

            .btn-submit {
                padding: 12px;
                font-size: 13px;
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .auth-container {
            animation: fadeIn 0.6s ease-out;
        }

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
                <h1>Create Account</h1>
                <p>Join EventAps to get started</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="auth-form">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <div class="input-wrapper">
                            <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" 
                                   class="form-control" placeholder=" " required autofocus autocomplete="given-name">
                            <label for="first_name" class="input-label">First Name</label>
                            <i class="fas fa-user input-icon"></i>
                        </div>
                        <x-input-error :messages="$errors->get('first_name')" class="error-msg" />
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper">
                            <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" 
                                   class="form-control" placeholder=" " required autocomplete="family-name">
                            <label for="last_name" class="input-label">Last Name</label>
                            <i class="fas fa-user input-icon"></i>
                        </div>
                        <x-input-error :messages="$errors->get('last_name')" class="error-msg" />
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-wrapper">
                        <input id="middle_name" type="text" name="middle_name" value="{{ old('middle_name') }}" 
                               class="form-control" placeholder=" " autocomplete="additional-name">
                        <label for="middle_name" class="input-label">Middle Name (Optional)</label>
                        <i class="fas fa-user-circle input-icon"></i>
                    </div>
                    <x-input-error :messages="$errors->get('middle_name')" class="error-msg" />
                </div>

                <!-- ✅ UPDATED: MS365 EMAIL FIELD -->
            <div class="form-group">
                <div class="input-wrapper">
                    <input id="email" type="email" name="email" value="{{ old('email') }}" 
                        class="form-control" 
                        placeholder=" " 
                        required 
                        autocomplete="username"
                        pattern=".+@(mcc\.edu\.ph|mcclawis\.edu\.ph)$"
                        title="Please enter your official MS365 school email (e.g. johndoe@mcc.edu.ph or briannick.acorda@mcclawis.edu.ph)">
                    <label for="email" class="input-label">MS365 Email (e.g. johndoe@mcc.edu.ph)</label>
                    <i class="fas fa-envelope input-icon"></i>
                </div>
                <x-input-error :messages="$errors->get('email')" class="error-msg" />
            </div>


                <!-- ✅ ADDED: ID NUMBER FIELD -->
                <div class="form-group">
                    <div class="input-wrapper">
                        <input id="id_number" type="text" name="id_number" value="{{ old('id_number') }}" 
                               class="form-control" placeholder=" " required autocomplete="username">
                        <label for="id_number" class="input-label">ID Number (e.g. 2019-1313)</label>
                        <i class="fas fa-id-card input-icon"></i>
                    </div>
                    <x-input-error :messages="$errors->get('id_number')" class="error-msg" />
                </div>

                <div class="form-group">
                    <div class="input-wrapper">
                        <select id="department" name="department" class="form-select" required>
                            <option value=""></option>
                            <option value="BSIT" {{ old('department') == 'BSIT' ? 'selected' : '' }}>Bachelor of Science in Information Technology</option>
                            <option value="BSBA" {{ old('department') == 'BSBA' ? 'selected' : '' }}>Bachelor of Science in Business Administration</option>
                            <option value="BSED" {{ old('department') == 'BSED' ? 'selected' : '' }}>Bachelor of Science in Education</option>
                            <option value="BEED" {{ old('department') == 'BEED' ? 'selected' : '' }}>Bachelor of Elementary Education</option>
                            <option value="BSHM" {{ old('department') == 'BSHM' ? 'selected' : '' }}>Bachelor of Science in Hospitality Management</option>
                        </select>
                        <label for="department" class="select-label">Select Department</label>
                        <i class="fas fa-graduation-cap input-icon"></i>
                    </div>
                    <x-input-error :messages="$errors->get('department')" class="error-msg" />
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <div class="input-wrapper">
                            <input id="password" type="password" name="password" 
                                   class="form-control" placeholder=" " required autocomplete="new-password">
                            <label for="password" class="input-label">Password</label>
                            <i class="fas fa-lock input-icon"></i>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="error-msg" />
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper">
                            <input id="password_confirmation" type="password" name="password_confirmation" 
                                   class="form-control" placeholder=" " required autocomplete="new-password">
                            <label for="password_confirmation" class="input-label">Confirm Password</label>
                            <i class="fas fa-check-circle input-icon"></i>
                        </div>
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
</x-guest-layout>