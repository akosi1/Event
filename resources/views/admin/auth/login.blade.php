<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - EventAP</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Roboto+Condensed:wght@300;400;700&family=Oswald:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="{{ asset('admin/adminlogin.css') }}" rel="stylesheet">
    
    <!-- Dynamic Background Image -->
    <style>
        body {
            background: url("{{ asset('images/mcc background.jpg') }}") center/cover no-repeat;
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

    <!-- reCAPTCHA Script -->
    @if(config('services.recaptcha.site_key'))
        <script>
            window.recaptchaSiteKey = '{{ config('services.recaptcha.site_key') }}';
        </script>
        <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
    @else
        <script>
            console.warn('Google reCAPTCHA site key is not configured.');
        </script>
    @endif

    <!-- Custom JavaScript -->
    <script src="{{ asset('admin/adminlogin.js') }}"></script>
</body>
</html>