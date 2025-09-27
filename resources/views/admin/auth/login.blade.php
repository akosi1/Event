<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - EventAP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #8b5fbf 100%);
            min-height: 100vh;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            overflow-x: hidden;
            position: relative;
        }

        /* Animated background particles */
        .bg-particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float-particle 15s infinite linear;
        }

        .particle:nth-child(1) { width: 6px; height: 6px; left: 10%; animation-delay: 0s; }
        .particle:nth-child(2) { width: 8px; height: 8px; left: 20%; animation-delay: 2s; }
        .particle:nth-child(3) { width: 4px; height: 4px; left: 30%; animation-delay: 4s; }
        .particle:nth-child(4) { width: 10px; height: 10px; left: 40%; animation-delay: 6s; }
        .particle:nth-child(5) { width: 5px; height: 5px; left: 50%; animation-delay: 8s; }
        .particle:nth-child(6) { width: 7px; height: 7px; left: 60%; animation-delay: 10s; }
        .particle:nth-child(7) { width: 9px; height: 9px; left: 70%; animation-delay: 12s; }
        .particle:nth-child(8) { width: 3px; height: 3px; left: 80%; animation-delay: 14s; }
        .particle:nth-child(9) { width: 6px; height: 6px; left: 90%; animation-delay: 16s; }

        @keyframes float-particle {
            0% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(-100px) rotate(360deg); opacity: 0; }
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 10;
            padding: 20px;
        }

        .main-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            width: 100%;
            max-width: 1000px;
            height: 85vh;
            max-height: 650px;
            display: flex;
            box-shadow: 
                0 32px 64px rgba(102, 126, 234, 0.15),
                0 8px 32px rgba(118, 75, 162, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
            overflow: hidden;
            position: relative;
        }

        .left-section {
            flex: 1.2;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #8b5fbf 100%);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .left-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }

        .illustration {
            position: relative;
            z-index: 10;
            text-align: center;
        }

        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
        }

        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float-shape 8s ease-in-out infinite;
        }

        .shape:nth-child(1) { 
            width: 60px; height: 60px; top: 20%; left: 15%; 
            animation-delay: 0s;
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
        }
        .shape:nth-child(2) { 
            width: 40px; height: 40px; top: 60%; right: 20%; 
            animation-delay: 2s;
        }
        .shape:nth-child(3) { 
            width: 80px; height: 80px; bottom: 25%; left: 20%; 
            animation-delay: 4s;
            border-radius: 63% 37% 54% 46% / 55% 48% 52% 45%;
        }
        .shape:nth-child(4) { 
            width: 30px; height: 30px; top: 40%; left: 70%; 
            animation-delay: 6s;
            border-radius: 30% 70% 40% 60% / 50% 60% 40% 50%;
        }

        @keyframes float-shape {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            25% { transform: translateY(-20px) rotate(90deg); }
            50% { transform: translateY(0) rotate(180deg); }
            75% { transform: translateY(-15px) rotate(270deg); }
        }

        .main-illustration {
            background: rgba(255, 255, 255, 0.15);
            width: 220px;
            height: 220px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            animation: pulse 4s ease-in-out infinite;
        }

        .main-illustration::before {
            content: '';
            position: absolute;
            top: -20px;
            left: -20px;
            right: -20px;
            bottom: -20px;
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: rotate 20s linear infinite;
        }

        .illustration-icon {
            font-size: 3rem;
            color: rgba(255, 255, 255, 0.9);
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .right-section {
            flex: 1;
            padding: 40px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
        }

        .brand-logo {
            text-align: center;
            margin-bottom: 25px;
        }

        .brand-logo h2 {
            font-size: 1.6rem;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: 2px;
            margin-bottom: 6px;
        }

        .brand-tagline {
            color: #6c757d;
            font-size: 0.8rem;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .welcome-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2d3436;
            text-align: center;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }

        .welcome-subtitle {
            text-align: center;
            color: #6c757d;
            margin-bottom: 25px;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .input-wrapper {
            position: relative;
            background: rgba(248, 249, 250, 0.8);
            border-radius: 16px;
            backdrop-filter: blur(10px);
            border: 2px solid transparent;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .input-wrapper:focus-within {
            border-color: #667eea;
            background: rgba(255, 255, 255, 0.9);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
        }

        .form-control {
            border: none;
            background: transparent;
            padding: 14px 45px 14px 16px;
            font-size: 0.95rem;
            font-weight: 500;
            border-radius: 16px;
            width: 100%;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            box-shadow: none;
            background: transparent;
        }

        .form-control::placeholder {
            color: #9ca3af;
            font-weight: 500;
        }

        .input-icon {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #667eea;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .input-wrapper:focus-within .input-icon {
            color: #764ba2;
            transform: translateY(-50%) scale(1.1);
        }

        .forgot-password {
            text-align: right;
            margin-bottom: 25px;
        }

        .forgot-password a {
            color: #6c757d;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }

        .forgot-password a:hover {
            color: #667eea;
        }

        .forgot-password a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            transition: width 0.3s ease;
        }

        .forgot-password a:hover::after {
            width: 100%;
        }

        .login-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #8b5fbf 100%);
            border: none;
            border-radius: 16px;
            padding: 14px 40px;
            font-size: 1rem;
            font-weight: 600;
            color: white;
            width: 100%;
            margin-bottom: 20px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
        }

        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .login-btn:hover::before {
            left: 100%;
        }

        .login-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
        }

        .login-btn:active {
            transform: translateY(-1px);
        }

        .back-link {
            text-align: center;
        }

        .back-link a {
            color: #6c757d;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 12px;
            background: rgba(248, 249, 250, 0.5);
            backdrop-filter: blur(10px);
        }

        .back-link a:hover {
            color: #667eea;
            background: rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }

        .alert {
            border: none;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 25px;
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            backdrop-filter: blur(10px);
            border-left: 4px solid #dc3545;
        }

        /* Loading animation */
        .btn-loading {
            position: relative;
            color: transparent !important;
        }

        .btn-loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .main-card {
                flex-direction: column;
                border-radius: 0;
                min-height: 100vh;
                max-width: 100%;
            }
            
            .left-section {
                min-height: 35vh;
                flex: none;
            }
            
            .right-section {
                padding: 40px 30px;
                flex: none;
            }
            
            .main-illustration {
                width: 200px;
                height: 200px;
            }
            
            .illustration-icon {
                font-size: 3rem;
            }
            
            .welcome-title {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 480px) {
            .right-section {
                padding: 30px 20px;
            }
            
            .welcome-title {
                font-size: 1.6rem;
            }
        }
    </style>
</head>
<body>
    <div class="bg-particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="login-container">
        <div class="main-card">
            <div class="left-section">
                <div class="floating-shapes">
                    <div class="shape"></div>
                    <div class="shape"></div>
                    <div class="shape"></div>
                    <div class="shape"></div>
                </div>
                <div class="illustration">
                    <div class="main-illustration">
                        <i class="fas fa-user-shield illustration-icon"></i>
                    </div>
                </div>
            </div>

            <div class="right-section">
                <div class="brand-logo">
                    <h2>MCC Admin Event</h2>
                </div>

                <h1 class="welcome-title">Welcome Back</h1>
                <p class="welcome-subtitle">Sign in to your admin account</p>
                
                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login.post') }}" id="loginForm">
                    @csrf
                    
                    <div class="form-group">
                        <div class="input-wrapper">
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   placeholder="Email address"
                                   required>
                            <i class="fas fa-envelope input-icon"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper">
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Password"
                                   required>
                            <i class="fas fa-lock input-icon" id="passwordIcon"></i>
                        </div>
                    </div>

                    <!-- <div class="forgot-password">
                        <a href="#">Forgot your password?</a>
                    </div> -->

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
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function() {
            const button = document.getElementById('loginButton');
            button.classList.add('btn-loading');
            button.disabled = true;
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

        // Add some interactive feedback
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>