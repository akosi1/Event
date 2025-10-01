<x-guest-layout>
    <div class="auth-wrapper">
        <div class="auth-container">
            <div class="auth-header">
                <div class="ms-logo">
                    <img src="images/logo.png" alt="Logo" width="48" height="48">
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
                    <!-- <div class="form-options">
                        <label class="remember-me">
                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            Keep me signed in
                        </label>
                    </div> -->
                    <button type="submit" class="btn-primary">
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
                        <div class="divider">or</div>
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
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            background: #f5f5f5;
            color: #323130;
        }
        
        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        
        .auth-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            max-height: 95vh;
            display: flex;
            flex-direction: column;
        }
        
        .auth-header {
            text-align: center;
            padding: 32px 24px 24px;
            border-bottom: 1px solid #edebe9;
            flex-shrink: 0;
        }
        
        .ms-logo { margin-bottom: 16px; display: flex; justify-content: center; }
        
        .auth-header h1 {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #1a1a1a;
        }
        
        .auth-header p { font-size: 14px; color: #666; }
        
        .auth-form {
            padding: 24px;
            overflow-y: auto;
            flex: 1;
        }
        
        .auth-form::-webkit-scrollbar { width: 8px; }
        .auth-form::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        .auth-form::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 10px; }
        
        .form-group { margin-bottom: 16px; }
        
        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }
        
        .input-wrapper i {
            position: absolute;
            left: 14px;
            color: #0078d4;
            font-size: 16px;
            pointer-events: none;
        }
        
        .input-wrapper input {
            width: 100%;
            padding: 14px 14px 14px 44px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 15px;
            transition: border-color 0.2s;
            outline: none;
        }
        
        .input-wrapper input:focus { border-color: #0078d4; }
        
        .form-options { margin-bottom: 16px; }
        
        .remember-me {
            display: flex;
            align-items: center;
            font-size: 14px;
            cursor: pointer;
            user-select: none;
        }
        
        .remember-me input {
            margin-right: 8px;
            width: 16px;
            height: 16px;
            cursor: pointer;
        }
        
        .btn-primary, .btn-secondary {
            width: 100%;
            padding: 14px;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: #0078d4;
            color: white;
            border: none;
            margin-bottom: 20px;
        }
        
        .btn-primary:hover { background: #106ebe; }
        
        .btn-secondary {
            background: white;
            color: #0078d4;
            border: 2px solid #0078d4;
            text-decoration: none;
            margin-top: 12px;
        }
        
        .btn-secondary:hover { background: #f0f7ff; }
        
        .form-footer { text-align: center; }
        
        .forgot-password { margin-bottom: 20px; }
        
        .forgot-password a {
            color: #0078d4;
            text-decoration: none;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
        }
        
        .forgot-password a:hover { text-decoration: underline; }
        
        .divider {
            color: #999;
            font-size: 14px;
            margin: 20px 0;
        }
        
        .signup-link p {
            color: #666;
            font-size: 14px;
            margin-bottom: 0;
        }
        
        .error-msg {
            color: #d13438;
            font-size: 13px;
            margin-top: 5px;
            display: block;
        }
        
        .mb-4 div {
            padding: 12px 14px;
            background: #dff6dd;
            border: 1px solid #107c10;
            border-radius: 6px;
            color: #107c10;
            font-size: 14px;
            margin-bottom: 16px;
        }

        /* Dropdown Styles */
        .dropdown-wrapper { position: relative; }
        
        .dropdown-btn {
            width: 100%;
            padding: 14px 44px;
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 15px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: border-color 0.2s;
        }
        
        .dropdown-btn:hover,
        .dropdown-btn.active { border-color: #0078d4; }
        
        .dropdown-btn.active {
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }
        
        .dropdown-text {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #666;
            flex: 1;
        }
        
        .dropdown-text i {
            position: absolute;
            left: 14px;
            color: #0078d4;
            font-size: 16px;
        }
        
        .arrow {
            color: #0078d4;
            transition: transform 0.2s;
            font-size: 12px;
        }
        
        .arrow.rotate { transform: rotate(180deg); }
        
        .menu {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background: white;
            border: 2px solid #0078d4;
            border-top: none;
            border-radius: 0 0 6px 6px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .menu.show {
            max-height: 250px;
            overflow-y: auto;
        }
        
        .item {
            padding: 12px 15px 12px 44px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid #edebe9;
            font-size: 15px;
            position: relative;
            transition: background 0.2s;
        }
        
        .item:last-child { border-bottom: none; }
        .item:hover { background: #f3f2f1; }
        .item:active { background: #deecf9; }
        
        .item i {
            position: absolute;
            left: 14px;
            color: #0078d4;
            font-size: 16px;
        }

        /* Mobile Responsive - NO SCROLLING */
        @media (max-width: 520px) {
            .auth-wrapper {
                padding: 0;
                align-items: stretch;
            }
            
            .auth-container {
                border-radius: 0;
                box-shadow: none;
                min-height: 100vh;
                max-width: 100%;
                max-height: 100vh;
                padding: 0;
            }

            .auth-header {
                padding: 48px 24px 24px;
            }

            .auth-header h1 { font-size: 20px; }
            .auth-header p { font-size: 13px; }
            
            .auth-form { padding: 24px; }
            
            .input-wrapper input,
            .dropdown-btn { 
                font-size: 16px;
                padding: 13px 13px 13px 42px;
            }
            
            .dropdown-btn { padding: 13px 42px; }
            
            .item {
                padding: 11px 12px 11px 40px;
                font-size: 16px;
            }
            
            .btn-primary,
            .btn-secondary { 
                padding: 13px;
                font-size: 16px;
            }
            
            .forgot-password a,
            .signup-link p {
                font-size: 13px;
            }
        }

        @media (max-width: 375px) {
            .auth-header { padding: 40px 20px 20px; }
            .auth-header h1 { font-size: 19px; }
            .auth-form { padding: 20px; }
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
                    deptText.textContent = this.dataset.val;
                    deptText.style.color = '#323130';
                    deptInput.value = this.dataset.full;
                    deptIcon.className = 'fas ' + this.dataset.icon;
                    
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

            deptMenu.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });
    </script>
</x-guest-layout>