<x-guest-layout>
    <div class="auth-wrapper">
        <div class="auth-container">
            <div class="auth-header">
                <h1>Complete Registration</h1>
                <p>Create your E&P-O account</p>
            </div>

            <div class="auth-form">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Student ID -->
                    <div class="form-group">
                        <div class="input-wrapper">
                            <input id="id_number" type="text" name="id_number" 
                                   value="{{ old('id_number') }}" 
                                   placeholder=" " required autocomplete="off" class="form-control">
                            <label class="input-label">Student ID Number</label>
                            <i class="fas fa-id-card input-icon"></i>
                        </div>
                        <x-input-error :messages="$errors->get('id_number')" class="error-msg" />
                    </div>

                    <!-- First Name and Last Name Row -->
                    <div class="form-row">
                        <div class="form-group">
                            <div class="input-wrapper">
                                <input id="first_name" type="text" name="first_name" 
                                       value="{{ old('first_name') }}" 
                                       placeholder=" " required autofocus autocomplete="given-name" class="form-control">
                                <label class="input-label">First Name</label>
                                <i class="fas fa-user input-icon"></i>
                            </div>
                            <x-input-error :messages="$errors->get('first_name')" class="error-msg" />
                        </div>

                        <div class="form-group">
                            <div class="input-wrapper">
                                <input id="last_name" type="text" name="last_name" 
                                       value="{{ old('last_name') }}" 
                                       placeholder=" " required autocomplete="family-name" class="form-control">
                                <label class="input-label">Last Name</label>
                                <i class="fas fa-user input-icon"></i>
                            </div>
                            <x-input-error :messages="$errors->get('last_name')" class="error-msg" />
                        </div>
                    </div>

                    <!-- Middle Name -->
                    <div class="form-group">
                        <div class="input-wrapper">
                            <input id="middle_name" type="text" name="middle_name" 
                                   value="{{ old('middle_name') }}" 
                                   placeholder=" " autocomplete="additional-name" class="form-control">
                            <label class="input-label">Middle Name (Optional)</label>
                            <i class="fas fa-user-circle input-icon"></i>
                        </div>
                        <x-input-error :messages="$errors->get('middle_name')" class="error-msg" />
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <div class="input-wrapper">
                            <input id="email" type="email" name="email" 
                                   value="{{ old('email', session('verified_email')) }}" 
                                   placeholder=" " required 
                                   autocomplete="username"
                                   readonly
                                   class="form-control"
                                   style="background-color: rgba(245, 245, 245, 0.95); cursor: not-allowed;">
                            <label class="input-label">McLawis College Email</label>
                            <i class="fas fa-envelope input-icon"></i>
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="error-msg" />
                    </div>

                    <!-- Department Dropdown -->
                    <div class="form-group">
                        <div class="dropdown-wrapper">
                            <div class="dropdown-btn" id="deptBtn">
                                <div class="dropdown-content">
                                    <i class="fas fa-graduation-cap input-icon" id="deptIcon"></i>
                                    <label class="select-label" id="deptLabel">Select Department</label>
                                    <span id="deptText"></span>
                                </div>
                                <i class="fas fa-chevron-down arrow" id="deptArrow"></i>
                            </div>
                            <div class="menu" id="deptMenu">
                                <div class="item" data-val="BSIT" data-full="Bachelor of Science in Information Technology" data-icon="fa-laptop-code">
                                    <i class="fas fa-laptop-code"></i><span>BSIT</span>
                                </div>
                                <div class="item" data-val="BSBA" data-full="Bachelor of Science in Business Administration" data-icon="fa-briefcase">
                                    <i class="fas fa-briefcase"></i><span>BSBA</span>
                                </div>
                                <div class="item" data-val="BSED" data-full="Bachelor of Science in Education" data-icon="fa-chalkboard-teacher">
                                    <i class="fas fa-chalkboard-teacher"></i><span>BSEd</span>
                                </div>
                                <div class="item" data-val="BEED" data-full="Bachelor of Elementary Education" data-icon="fa-school">
                                    <i class="fas fa-school"></i><span>BEEd</span>
                                </div>
                                <div class="item" data-val="BSHM" data-full="Bachelor of Science in Hospitality Management" data-icon="fa-hotel">
                                    <i class="fas fa-hotel"></i><span>BSHM</span>
                                </div>
                            </div>
                            <input type="hidden" name="department" id="department" value="{{ old('department') }}" required>
                        </div>
                        <x-input-error :messages="$errors->get('department')" class="error-msg" />
                    </div>

                    <!-- Password and Confirm Password Row -->
                    <div class="form-row">
                        <div class="form-group">
                            <div class="input-wrapper">
                                <input id="password" type="password" name="password" 
                                       placeholder=" " required autocomplete="new-password" class="form-control">
                                <label class="input-label">Password</label>
                                <i class="fas fa-lock input-icon"></i>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="error-msg" />
                        </div>

                        <div class="form-group">
                            <div class="input-wrapper">
                                <input id="password_confirmation" type="password" 
                                       name="password_confirmation" 
                                       placeholder=" " required autocomplete="new-password" class="form-control">
                                <label class="input-label">Confirm Password</label>
                                <i class="fas fa-check-circle input-icon"></i>
                            </div>
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

                    <div class="form-footer">
                        <div class="divider">
                            <span>or</span>
                        </div>
                        
                        <div class="signup-link">
                            <p>Already registered?</p>
                            <a href="{{ route('login') }}" class="btn-signin">
                                Sign in here
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deptBtn = document.getElementById('deptBtn');
            const deptMenu = document.getElementById('deptMenu');
            const deptText = document.getElementById('deptText');
            const deptInput = document.getElementById('department');
            const deptArrow = document.getElementById('deptArrow');
            const deptIcon = document.getElementById('deptIcon');
            const items = document.querySelectorAll('.item');

            // Set old value if exists
            const oldValue = deptInput.value;
            if (oldValue) {
                items.forEach(item => {
                    if (item.dataset.val === oldValue || item.dataset.full === oldValue) {
                        deptText.textContent = item.dataset.val;
                        deptInput.value = item.dataset.val;
                        deptIcon.className = 'fas ' + item.dataset.icon + ' input-icon';
                        deptBtn.classList.add('has-value');
                    }
                });
            }

            deptBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                deptMenu.classList.toggle('show');
                deptBtn.classList.toggle('active');
            });

            items.forEach(item => {
                item.addEventListener('click', function() {
                    const val = this.dataset.val;
                    const icon = this.dataset.icon;
                    
                    deptText.textContent = val;
                    deptInput.value = val;
                    deptIcon.className = 'fas ' + icon + ' input-icon';
                    deptBtn.classList.add('has-value');
                    
                    deptMenu.classList.remove('show');
                    deptBtn.classList.remove('active');
                });
            });

            document.addEventListener('click', function() {
                deptMenu.classList.remove('show');
                deptBtn.classList.remove('active');
            });
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

        .auth-wrapper {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            z-index: 2;
        }

        .auth-container {
            background: transparent;
            width: 100%;
            max-width: 450px;
            animation: fadeIn 0.6s ease-out;
            padding: 30px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .auth-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .auth-header h1 {
            font-size: 38px;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 8px;
            letter-spacing: 1px;
            font-family: 'Oswald', sans-serif;
            text-transform: uppercase;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .auth-header p {
            color: #e0e0e0;
            font-size: 15px;
            font-weight: 400;
            font-family: 'Roboto Condensed', sans-serif;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

        .verified-email {
            margin-top: 16px;
            padding: 10px 14px;
            background: rgba(217, 237, 247, 0.95);
            border: 1px solid #bee5eb;
            border-radius: 4px;
            color: #0c5460;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-family: 'Poppins', sans-serif;
        }

        .auth-form {
            max-height: calc(100vh - 200px);
            overflow-y: auto;
            scrollbar-width: none;
            padding-right: 5px;
        }

        .auth-form::-webkit-scrollbar {
            display: none;
        }

        .auth-form:hover,
        .auth-form:focus-within {
            scrollbar-width: auto;
        }

        .auth-form:hover::-webkit-scrollbar,
        .auth-form:focus-within::-webkit-scrollbar {
            width: 8px;
        }

        .auth-form:hover::-webkit-scrollbar-track,
        .auth-form:focus-within::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        .auth-form:hover::-webkit-scrollbar-thumb,
        .auth-form:focus-within::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 10px;
        }

        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 0;
        }

        .form-row .form-group {
            flex: 1;
        }

        .form-group {
            margin-bottom: 22px;
        }

        .input-wrapper {
            position: relative;
        }

        .form-control {
            width: 100%;
            padding: 18px 18px 8px 50px;
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

        .form-control:focus {
            border-color: #6b2c91;
            background: rgba(255, 255, 255, 1);
            box-shadow: 0 4px 15px rgba(107, 44, 145, 0.2);
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            color: #666;
            transition: color 0.3s ease;
            z-index: 3;
        }

        .form-control:focus ~ .input-icon {
            color: #6b2c91;
        }

        .input-label {
            position: absolute;
            left: 50px;
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
        }

        .form-control:focus + .input-label,
        .form-control:not(:placeholder-shown) + .input-label {
            top: 6px;
            transform: translateY(0);
            font-size: 11px;
            color: #6b2c91;
            background: rgba(255, 255, 255, 1);
            left: 45px;
            font-weight: 600;
        }

        /* Custom Dropdown Styles */
        .dropdown-wrapper {
            position: relative;
        }

        .dropdown-btn {
            width: 100%;
            padding: 18px 45px 8px 50px;
            background: rgba(255, 255, 255, 0.95);
            border: 2px solid #ddd;
            border-radius: 0;
            font-size: 16px;
            color: #000;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            min-height: 54px;
        }

        .dropdown-btn:hover {
            border-color: #6b2c91;
        }

        .dropdown-btn.active {
            border-color: #6b2c91;
            background: rgba(255, 255, 255, 1);
            box-shadow: 0 4px 15px rgba(107, 44, 145, 0.2);
        }

        .dropdown-content {
            display: flex;
            align-items: center;
            flex: 1;
            position: relative;
        }

        .dropdown-content .input-icon {
            position: absolute;
            left: -34px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            transition: color 0.3s ease;
        }

        .dropdown-btn.active .input-icon,
        .dropdown-btn.has-value .input-icon {
            color: #6b2c91;
        }

        .select-label {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-size: 16px;
            pointer-events: none;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
            font-weight: 400;
        }

        .dropdown-btn.active .select-label,
        .dropdown-btn.has-value .select-label {
            top: -14px;
            transform: translateY(0);
            font-size: 11px;
            color: #6b2c91;
            background: rgba(255, 255, 255, 1);
            padding: 0 5px;
            left: -5px;
            font-weight: 600;
        }

        #deptText {
            font-size: 16px;
            color: #000;
            font-family: 'Poppins', sans-serif;
            font-weight: 400;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .dropdown-btn.has-value #deptText {
            opacity: 1;
        }

        .arrow {
            color: #666;
            transition: all 0.3s ease;
            font-size: 12px;
        }

        .dropdown-btn.active .arrow {
            transform: rotate(180deg);
            color: #6b2c91;
        }

        .menu {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background: white;
            border: 2px solid #6b2c91;
            border-top: none;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(107, 44, 145, 0.2);
        }

        .menu.show {
            max-height: 250px;
            overflow-y: auto;
        }

        .item {
            padding: 12px 15px 12px 45px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 16px;
            color: #000;
            position: relative;
            font-family: 'Poppins', sans-serif;
        }

        .item:last-child {
            border-bottom: none;
        }

        .item:hover {
            background: rgba(107, 44, 145, 0.1);
        }

        .item:active {
            background: rgba(107, 44, 145, 0.2);
        }

        .item i {
            position: absolute;
            left: 15px;
            color: #6b2c91;
            font-size: 16px;
        }

        .menu::-webkit-scrollbar {
            width: 6px;
        }

        .menu::-webkit-scrollbar-thumb {
            background: #6b2c91;
            border-radius: 3px;
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
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            font-family: 'Oswald', sans-serif;
            text-transform: uppercase;
            letter-spacing: 2px;
            box-shadow: 0 4px 15px rgba(229, 62, 62, 0.3);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(229, 62, 62, 0.4);
            background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
        }

        .btn-submit:active {
            transform: translateY(-1px);
        }

        .btn-signin {
            display: inline-block;
            width: 100%;
            padding: 12px 20px;
            background: transparent;
            color: #ffffff;
            border: 2px solid rgba(255, 255, 255, 0.6);
            border-radius: 0;
            font-size: 16px;
            font-weight: 500;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
            margin-top: 8px;
            font-family: 'Poppins', sans-serif;
            letter-spacing: 0.5px;
        }

        .btn-signin:hover {
            background: rgba(107, 44, 145, 0.3);
            border-color: rgba(255, 255, 255, 0.9);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 255, 255, 0.2);
        }

        .btn-signin:active {
            background: rgba(107, 44, 145, 0.5);
            transform: translateY(0);
        }

        .form-footer {
            text-align: center;
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

        .signup-link p {
            color: #ffffff;
            font-size: 15px;
            margin-bottom: 8px;
            font-family: 'Poppins', sans-serif;
        }

        .error-msg {
            color: #ff6b6b;
            font-size: 11px;
            margin-top: 4px;
            font-family: 'Poppins', sans-serif;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        @media (max-width: 768px) {
            .auth-wrapper {
                padding: 15px;
            }

            .auth-container {
                padding: 20px 15px;
                max-width: 100%;
            }

            .auth-header {
                margin-bottom: 25px;
            }

            .auth-header h1 {
                font-size: 28px;
            }

            .auth-header p {
                font-size: 13px;
            }

            .form-group {
                margin-bottom: 18px;
            }

            .form-control {
                padding: 16px 14px 6px 45px;
                font-size: 15px;
            }

            .dropdown-btn {
                padding: 16px 40px 6px 45px;
            }

            .dropdown-content .input-icon {
                left: -31px;
            }

            .input-icon {
                left: 14px;
                width: 16px;
                height: 16px;
            }

            .input-label {
                left: 45px;
                font-size: 15px;
            }

            .form-control:focus + .input-label,
            .form-control:not(:placeholder-shown) + .input-label {
                font-size: 10px;
                left: 40px;
            }

            .dropdown-btn.active .select-label,
            .dropdown-btn.has-value .select-label {
                left: -5px;
            }

            .item {
                padding: 12px 12px 12px 40px;
                font-size: 15px;
            }

            .item i {
                left: 12px;
            }

            .btn-submit {
                padding: 14px;
                font-size: 15px;
                margin-bottom: 18px;
            }

            .btn-signin {
                padding: 11px 18px;
                font-size: 15px;
            }

            .divider {
                margin: 18px 0;
            }

            .signup-link p {
                font-size: 14px;
                margin-bottom: 6px;
            }
        }

        @media (max-width: 580px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }

            .form-row .form-group {
                margin-bottom: 18px;
            }
        }

        @media (max-width: 480px) {
            .auth-container {
                padding: 20px 12px;
            }

            .auth-header h1 {
                font-size: 26px;
                letter-spacing: 0.5px;
            }

            .auth-header p {
                font-size: 12px;
            }

            .form-control {
                padding: 14px 12px 5px 40px;
                font-size: 14px;
            }

            .dropdown-btn {
                padding: 14px 35px 5px 40px;
            }

            .dropdown-content .input-icon {
                left: -28px;
            }

            .input-icon {
                left: 12px;
                width: 14px;
                height: 14px;
            }

            .btn-submit {
                padding: 13px;
                font-size: 14px;
                letter-spacing: 1.5px;
            }

            .btn-signin {
                padding: 10px 16px;
                font-size: 14px;
            }

            .divider {
                margin: 16px 0;
            }

            .divider span {
                font-size: 13px;
            }

            .signup-link p {
                font-size: 13px;
            }
        }
    </style>
</x-guest-layout>