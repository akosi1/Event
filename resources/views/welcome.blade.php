<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MCC Event & Portfolio Organizer</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            overflow: hidden;
            background: linear-gradient(90deg, #c43e3e 50%, #2c1810 50%);
        }

        .content {
            text-align: center;
            z-index: 10;
            padding: 2rem;
            max-width: 800px;
        }

        .logo-container {
            max-width: 250px;
            margin: 0 auto 2rem;
        }

        .logo {
            width: 100%;
            height: auto;
            max-height: 120px;
            object-fit: contain;
            filter: drop-shadow(2px 2px 0 #000) drop-shadow(4px 4px 0 #000) drop-shadow(6px 6px 8px rgba(0, 0, 0, 0.5));
        }

        .main-title {
            font-size: 4.5rem;
            font-weight: 900;
            color: white;
            margin: 0;
            line-height: 1.1;
            text-transform: uppercase;
            text-shadow:
                2px 2px 0 #000,
                4px 4px 0 #000,
                6px 6px 8px rgba(0, 0, 0, 0.5);
        }

        .btn-get-started {
            display: inline-block;
            padding: 14px 40px;
            font-size: 1.2rem;
            font-weight: 700;
            color: white;
            background: linear-gradient(135deg, #ff4136, #ff6b6b);
            border: none;
            border-radius: 0;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(255, 65, 54, 0.5);
            letter-spacing: 1px;
            font-family: 'Inter', sans-serif;
            margin-top: 1.5rem;
        }

        .btn-get-started:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 65, 54, 0.7);
            background: linear-gradient(135deg, #ff6b6b, #ff8a8a);
        }

        .login-link {
            margin-top: 2rem;
            font-size: 0.95rem;
            opacity: 0.85;
        }

        .login-link a {
            color: #ff6b6b;
            text-decoration: underline;
            font-weight: 600;
        }

        @media (max-width: 640px) {
            .main-title { font-size: 2.8rem; }
            .btn-get-started { padding: 12px 32px; font-size: 1.1rem; }
        }
    </style>
</head>
<body>
    <div class="content">
        <!-- Logo -->
        <div class="logo-container">
            <img src="{{ asset('images/logo.png') }}" alt="MCC Logo" class="logo">
        </div>

        <!-- Title -->
        <h1 class="main-title">
            MCC EVENT &<br>
            PORTFOLIO<br>
            ORGANIZER
        </h1>

        <!-- Get Started Button -->
        <a href="{{ route('register') }}" class="btn-get-started">→ GET STARTED</a>

        <!-- Login link -->
        <div class="login-link">
            Already have an account? wapa<a href="{{ route('login') }}">Sign in here</a>
        </div>
    </div>

    <!-- Load reCAPTCHA & custom JS -->
    @if(config('services.recaptcha.site_key'))
        <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
        <script src="{{ asset('user/v3/recapcha.js') }}"></script>
    @else
        <script>
            console.warn('Google reCAPTCHA site key is not configured.');
        </script>
    @endif
</body>
</html>