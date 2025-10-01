<x-guest-layout>
    <div class="auth-wrapper">
        <div class="auth-container">
            <div class="auth-header">
                <div class="ms-logo">
                    <img src="images/logo.png" alt="Logo" width="32" height="32">
                </div>
                <h1>Sign in to E&P-O</h1>
                <p>Use your ms365 email account</p>
            </div>
            <div class="auth-form" id="authForm">
                <x-auth-session-status class="mb-4" :status="session('status')" />
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group">
                        <div class="input-wrapper">
                            <i class="fas fa-envelope"></i>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" 
                                placeholder="someone@mcclawis.edu.ph" required autocomplete="username" autofocus>
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="error-msg" />
                    </div>
                    <div class="form-group">
                        <div class="input-wrapper">
                            <i class="fas fa-lock"></i>
                            <input id="password" type="password" name="password" 
                                placeholder="Password" required autocomplete="current-password">
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="error-msg" />
                    </div>
                    <div class="form-group">
                        <div class="dropdown-wrapper">
                            <div class="dropdown-btn" id="deptBtn">
                                <div class="dropdown-text">
                                    <i class="fas fa-graduation-cap" id="deptIcon"></i>
                                    <span id="deptText">Select Your Department</span>
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
                                <div class="item" data-val="BSEd" data-full="Bachelor of Science in Education" data-icon="fa-chalkboard-teacher">
                                    <i class="fas fa-chalkboard-teacher"></i><span>BSEd</span>
                                </div>
                                <div class="item" data-val="BEEd" data-full="Bachelor of Elementary Education" data-icon="fa-school">
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
        html, body {
            height: 100%;
            overflow: hidden;
            background: #f5f5f5;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }
        .auth-wrapper {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f5f5f5;
            padding: 20px;
            overflow: hidden;
        }
        .auth-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 420px;
            animation: fadeIn 0.4s ease;
            overflow: hidden;
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
            max-height: calc(100vh - 200px);
            overflow-y: auto;
            scrollbar-width: none;
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
            background: #f1f1f1;
            border-radius: 10px;
        }
        .auth-form:hover::-webkit-scrollbar-thumb,
        .auth-form:focus-within::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }
        .auth-form:hover::-webkit-scrollbar-thumb:hover,
        .auth-form:focus-within::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }
        .input-wrapper i {
            position: absolute;
            left: 15px;
            color: #0078d4;
            z-index: 1;
        }
        .input-wrapper input {
            width: 100%;
            padding: 11px 15px 11px 45px;
            border: 1px solid #605e5c;
            border-radius: 2px;
            font-size: 15px;
            background: white;
            transition: all 0.2s ease;
            outline: none;
        }
        .input-wrapper input:focus {
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
            position: relative;
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
            min-height: 16px;
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

        /* Custom Dropdown Styles */
        .dropdown-wrapper {
            position: relative;
        }
        .dropdown-btn {
            width: 100%;
            padding: 11px 45px 11px 45px;
            background: white;
            border: 1px solid #605e5c;
            border-radius: 2px;
            font-size: 15px;
            color: #323130;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.2s ease;
            -webkit-tap-highlight-color: transparent;
        }
        .dropdown-btn:hover {
            border-color: #0078d4;
        }
        .dropdown-btn.active {
            border-color: #0078d4;
            box-shadow: 0 0 0 1px #0078d4;
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }
        .dropdown-text {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #605e5c;
            flex: 1;
        }
        .dropdown-text i {
            position: absolute;
            left: 15px;
            color: #0078d4;
            font-size: 16px;
        }
        .dropdown-text span {
            text-align: left;
        }
        .arrow {
            color: #0078d4;
            transition: transform 0.2s;
            font-size: 12px;
            margin-left: auto;
        }
        .arrow.rotate {
            transform: rotate(180deg);
        }
        .menu {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background: white;
            border: 1px solid #0078d4;
            border-top: none;
            border-radius: 0 0 2px 2px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .menu.show {
            max-height: 250px;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }
        .item {
            padding: 12px 15px 12px 45px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid #edebe9;
            font-size: 15px;
            color: #323130;
            position: relative;
            -webkit-tap-highlight-color: transparent;
        }
        .item:last-child {
            border-bottom: none;
        }
        .item:hover {
            background: #f3f2f1;
        }
        .item:active {
            background: #deecf9;
        }
        .item i {
            position: absolute;
            left: 15px;
            color: #0078d4;
            font-size: 16px;
        }
        .menu::-webkit-scrollbar {
            width: 6px;
        }
        .menu::-webkit-scrollbar-thumb {
            background: #0078d4;
            border-radius: 3px;
        }

        @media (max-width: 480px) {
            .auth-wrapper { padding: 15px; }
            .auth-container { max-width: 100%; }
            .auth-header, .auth-form { padding: 25px 20px; }
            .input-wrapper input {
                padding: 10px 12px 10px 40px;
                font-size: 14px;
            }
            .input-wrapper i {
                left: 12px;
            }
            .dropdown-btn {
                padding: 10px 40px 10px 40px;
                font-size: 14px;
            }
            .dropdown-text i {
                left: 12px;
            }
            .item {
                padding: 12px 12px 12px 40px;
                font-size: 14px;
            }
            .item i {
                left: 12px;
            }
            .btn-submit,
            .btn-secondary {
                font-size: 14px;
            }
            .auth-form {
                max-height: calc(100vh - 220px);
            }
        }
    </style>
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
                        deptText.style.color = '#323130';
                        deptInput.value = item.dataset.full;
                        deptIcon.className = 'fas ' + item.dataset.icon;
                    }
                });
            }

            deptBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                deptMenu.classList.toggle('show');
                deptBtn.classList.toggle('active');
                deptArrow.classList.toggle('rotate');
            });

            items.forEach(item => {
                item.addEventListener('click', function() {
                    const val = this.dataset.val;
                    const full = this.dataset.full;
                    const icon = this.dataset.icon;
                    
                    deptText.textContent = val;
                    deptText.style.color = '#323130';
                    deptInput.value = full;
                    deptIcon.className = 'fas ' + icon;
                    
                    deptMenu.classList.remove('show');
                    deptBtn.classList.remove('active');
                    deptArrow.classList.remove('rotate');
                });
            });

            document.addEventListener('click', function() {
                deptMenu.classList.remove('show');
                deptBtn.classList.remove('active');
                deptArrow.classList.remove('rotate');
            });

            // Mobile touch support
            const authForm = document.getElementById('authForm');
            authForm.addEventListener('touchstart', function() {
                this.classList.add('show-scrollbar');
            });
            authForm.addEventListener('touchend', function() {
                this.classList.remove('show-scrollbar');
            });
            authForm.addEventListener('mouseenter', function() {
                this.classList.add('show-scrollbar');
            });
            authForm.addEventListener('mouseleave', function() {
                this.classList.remove('show-scrollbar');
            });
        });
    </script>
</x-guest-layout>