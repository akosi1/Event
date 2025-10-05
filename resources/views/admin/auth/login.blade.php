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
        </style>
        <link rel="stylesheet" href="{{ asset('user/admin/adminlogin.css') }}">
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
                        <label class="input-label">MS365 Email</label>
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
                    Back to main page
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