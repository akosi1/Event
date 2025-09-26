    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Login - EventAP</title>
        
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Roboto+Condensed:wght@300;400;700&family=Oswald:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
                justify-content: flex-end;
                align-items: center;
                position: relative;
                overflow: hidden;
                padding-right: 100px;
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

            .login-container {
                background: transparent;
                backdrop-filter: none;
                border-radius: 0;
                padding: 50px;
                width: 450px;
                max-width: 90vw;
                box-shadow: none;
                position: relative;
                z-index: 2;
                border: none;
                animation: fadeIn 0.6s ease-out;
            }

            .brand-logo {
                text-align: center;
                margin-bottom: 15px;
            }

            .brand-logo h2 {
                font-size: 36px;
                font-weight: 700;
                color: #ffffff;
                margin-bottom: 0;
                letter-spacing: 2px;
                font-family: 'Oswald', sans-serif;
                text-transform: uppercase;
                text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            }

            .welcome-title {
                font-size: 42px;
                font-weight: 600;
                color: #ffffff;
                text-align: center;
                margin-bottom: 8px;
                letter-spacing: 1px;
                font-family: 'Oswald', sans-serif;
                text-transform: uppercase;
                text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            }

            .welcome-subtitle {
                text-align: center;
                color: #e0e0e0;
                margin-bottom: 35px;
                font-size: 16px;
                font-weight: 400;
                font-family: 'Roboto Condensed', sans-serif;
                text-transform: uppercase;
                letter-spacing: 1px;
                text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
            }

            .alert {
                margin-bottom: 20px;
                font-size: 14px;
                font-family: 'Roboto Condensed', sans-serif;
                border-radius: 0;
            }

            .form-group {
                margin-bottom: 25px;
                position: relative;
            }

            .input-wrapper {
                position: relative;
            }

            .form-control {
                width: 100%;
                padding: 20px 20px 10px 55px;
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

            .form-control.is-invalid {
                border-color: #dc3545;
            }

            .input-icon {
                position: absolute;
                left: 18px;
                top: 50%;
                transform: translateY(-50%);
                width: 20px;
                height: 20px;
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
                left: 55px;
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
                top: 8px;
                transform: translateY(0);
                font-size: 12px;
                color: #6b2c91;
                background: rgba(255, 255, 255, 1);
                border-radius: 0;
                left: 50px;
                font-weight: 600;
            }

            .forgot-password {
                text-align: center;
                margin-bottom: 25px;
            }

            .forgot-password a {
                color: #ffffff;
                text-decoration: none;
                font-size: 14px;
                font-family: 'Roboto Condensed', sans-serif;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
            }

            .forgot-password a:hover {
                text-decoration: underline;
                color: #e0e0e0;
            }

            .login-btn {
                width: 100%;
                padding: 18px;
                background: linear-gradient(135deg, #e53e3e 0%, #d53f41 100%);
                color: white;
                border: none;
                border-radius: 0;
                font-size: 18px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                font-family: 'Oswald', sans-serif;
                text-transform: uppercase;
                letter-spacing: 2px;
                box-shadow: 0 4px 15px rgba(229, 62, 62, 0.3);
            }

            .login-btn:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 25px rgba(229, 62, 62, 0.4);
                background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
            }

            .login-btn:active {
                transform: translateY(-1px);
            }

            .login-btn:disabled {
                opacity: 0.7;
                cursor: not-allowed;
            }

            .btn-loading {
                position: relative;
            }

            .btn-loading::after {
                content: '';
                position: absolute;
                left: 50%;
                top: 50%;
                transform: translate(-50%, -50%);
                width: 20px;
                height: 20px;
                border: 2px solid transparent;
                border-top: 2px solid white;
                border-radius: 50%;
                animation: spin 1s linear infinite;
            }

            @keyframes spin {
                0% { transform: translate(-50%, -50%) rotate(0deg); }
                100% { transform: translate(-50%, -50%) rotate(360deg); }
            }

            .back-link {
                text-align: center;
                margin-top: 25px;
            }

            .back-link a {
                color: #ffffff;
                text-decoration: none;
                font-size: 14px;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                font-family: 'Roboto Condensed', sans-serif;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                font-weight: 500;
                text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
            }

            .back-link a:hover {
                text-decoration: underline;
                color: #e0e0e0;
            }

            /* Mobile Responsive */
            @media (max-width: 768px) {
                body {
                    padding: 20px;
                    justify-content: center;
                    padding-right: 20px;
                }

                .login-container {
                    width: 100%;
                    max-width: none;
                    padding: 40px 30px;
                    margin: 0;
                }

                .brand-logo h2 {
                    font-size: 32px;
                }

                .welcome-title {
                    font-size: 36px;
                    margin-bottom: 8px;
                }

                .welcome-subtitle {
                    font-size: 14px;
                    margin-bottom: 30px;
                }

                .form-control {
                    padding: 18px 15px 8px 50px;
                    font-size: 16px;
                    border-radius: 0;
                }

                .input-icon {
                    left: 15px;
                    width: 18px;
                    height: 18px;
                }

                .input-label {
                    left: 50px;
                    font-size: 15px;
                }

                .form-control:focus + .input-label,
                .form-control:not(:placeholder-shown) + .input-label {
                    top: 6px;
                    font-size: 11px;
                    left: 45px;
                }

                .login-btn {
                    padding: 16px;
                    font-size: 16px;
                    border-radius: 0;
                }
            }

            @media (max-width: 480px) {
                .login-container {
                    padding: 30px 20px;
                }

                .brand-logo h2 {
                    font-size: 28px;
                }

                .welcome-title {
                    font-size: 32px;
                }

                .form-control {
                    padding: 16px 12px 6px 45px;
                    border-radius: 0;
                }

                .input-icon {
                    left: 12px;
                    width: 16px;
                    height: 16px;
                }

                .login-btn {
                    padding: 15px;
                    font-size: 15px;
                    border-radius: 0;
                }
            }

            /* Custom focus states for better UX */
            .input-wrapper:focus-within {
                transform: scale(1.02);
                transition: transform 0.2s ease;
            }

            .input-wrapper:not(:focus-within) {
                transform: scale(1);
                transition: transform 0.2s ease;
            }

            /* Add subtle animations */
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }

            .login-container {
                animation: fadeIn 0.6s ease-out;
            }
        </style>
    </head>
    <body>
        <div class="login-container">
            <div class="brand-logo">
                <h2>MCC Admin Event</h2>
            </div>

            <h1 class="welcome-title">Welcome Back</h1>
            <p class="welcome-subtitle">Sign in to your admin account</p>
            
            <div class="alert alert-danger" style="display: none;" id="errorAlert">
                <div id="errorMessages"></div>
            </div>

            <form method="POST" action="{{ route('admin.login.post') }}" id="loginForm">
                @csrf
                
                <div class="form-group">
                    <div class="input-wrapper">
                        <input type="email" 
                            class="form-control @error('email') is-invalid @enderror" 
                            id="email" 
                            name="email" 
                            value="{{ old('email') }}"
                            placeholder=" "
                            required>
                        <label class="input-label">Email Address</label>
                        <i class="fas fa-envelope input-icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-wrapper">
                        <input type="password" 
                            class="form-control @error('password') is-invalid @enderror" 
                            id="password" 
                            name="password" 
                            placeholder=" "
                            required>
                        <label class="input-label">Password</label>
                        <i class="fas fa-lock input-icon" id="passwordIcon"></i>
                    </div>
                </div>

                <div class="forgot-password" style="display: none;">
                    <a href="#">Forgot your password?</a>
                </div>

                <button type="submit" class="btn login-btn" id="loginButton">
                    Sign In
                </button>
            </form>

            <div class="back-link">
                <a href="{{ url('/') }}">
                    <i class="fas fa-arrow-left"></i>
                    Back to homepage
                </a>
            </div>
        </div>

        <script>
            document.getElementById('loginForm').addEventListener('submit', function() {
                const button = document.getElementById('loginButton');
                button.classList.add('btn-loading');
                button.disabled = true;
                button.innerHTML = '';
            });

            // Password toggle functionality
            document.getElementById('passwordIcon').addEventListener('click', function() {
                const passwordField = document.getElementById('password');
                const icon = this;
                
                if (passwordField.type === 'password') {
                    passwordField.type = 'text';
                    icon.classList.remove('fa-lock');
                    icon.classList.add('fa-unlock');
                } else {
                    passwordField.type = 'password';
                    icon.classList.remove('fa-unlock');
                    icon.classList.add('fa-lock');
                }
            });

            // Enhanced interactive effects
            document.addEventListener('DOMContentLoaded', function() {
                const inputs = document.querySelectorAll('.form-control');
                
                inputs.forEach(input => {
                    input.addEventListener('focus', function() {
                        this.parentElement.style.transform = 'scale(1.02)';
                        this.parentElement.style.transition = 'transform 0.2s ease';
                    });
                    
                    input.addEventListener('blur', function() {
                        this.parentElement.style.transform = 'scale(1)';
                    });
                    
                    // Add ripple effect on focus
                    input.addEventListener('focus', function() {
                        this.style.boxShadow = '0 4px 15px rgba(107, 44, 145, 0.2)';
                    });
                    
                    input.addEventListener('blur', function() {
                        this.style.boxShadow = '0 4px 15px rgba(0, 0, 0, 0.1)';
                    });
                });
            });
        </script>
    </body>
    </html>