<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MCC Event & Portfolio Organizer</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            /* Split background: deep maroon + red */
            background: linear-gradient(90deg, #2a0a0a 50%, #c00000 50%);
            font-family: 'Barlow', sans-serif;
            padding: 20px;
        }

        .welcome-container {
            background: rgba(15, 8, 8, 0.85);
            padding: 60px 40px;
            text-align: center;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .logo-container {
            max-width: 250px;
            margin: 0 auto 30px;
        }

        .logo {
            width: 100%;
            height: auto;
            max-height: 120px;
            object-fit: contain;
        }

        .main-title {
            color: white;
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: 1px;
            line-height: 1.2;
        }

        .highlight-text {
            color: #ff4d4d;
            display: block;
            margin-top: 8px;
        }

        /* Button styling for all button types */
        .btn {
            color: white;
            background: #d00000;
            border: none;
            padding: 16px 45px;
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-decoration: none;
            border-radius: 0;
            position: relative;
            display: inline-block;
            font-family: 'Barlow', sans-serif;
            box-shadow: 0 4px 10px rgba(150, 0, 0, 0.4);
            margin: 5px;
        }

        .btn:hover {
            background: #ff0000;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(180, 0, 0, 0.5);
        }

        /* Dashboard button - primary style */
        .btn-dashboard {
            background: #d00000;
            color: white;
        }

        .btn-dashboard:hover {
            background: #ff0000;
        }

        /* Login button - transparent with border */
        .btn-login {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            backdrop-filter: blur(10px);
        }

        .btn-login:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.5);
        }

        /* Register button - red gradient */
        .btn-register {
            background: linear-gradient(135deg, #e53935 0%, #d32f2f 100%);
            color: white;
            border: none;
        }

        .btn-register:hover {
            background: linear-gradient(135deg, #ff5722 0%, #e53935 100%);
        }

        /* Underline animation on hover */
        .btn::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: white;
            transition: width 0.3s ease;
        }

        .btn:hover::after {
            width: 100%;
        }

        /* Button group styling */
        .button-group {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 30px;
        }

        .btn-icon {
            margin-right: 8px;
        }

        @media (max-width: 600px) {
            .main-title {
                font-size: 2.2rem;
            }
            .btn {
                width: 100%;
                max-width: 250px;
                padding: 16px;
            }
            .button-group {
                flex-direction: column;
                align-items: center;
            }
        }

        /* Animation for title */
        .main-title {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.8s ease forwards;
            animation-delay: 0.3s;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Ripple effect */
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: scale(0);
            animation: ripple-animation 0.6s linear;
            pointer-events: none;
        }

        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <!-- Logo -->
        <div class="logo-container">
            <img src="{{ asset('images/logo.png') }}" alt="MCC Logo" class="logo">
        </div>

        <!-- Title -->
        <h1 class="main-title">
            MCC Event & Portfolio
            <span class="highlight-text">Organizer</span>
        </h1>

        <!-- Authentication Logic -->
        @if (Route::has('login'))
            <div class="button-group">
                @auth   
                    <!-- If user is logged in, show Dashboard button -->
                    <a href="{{ url('/dashboard') }}" class="btn btn-dashboard">
                        <i class="fas fa-tachometer-alt btn-icon"></i>
                        <span>Go to Dashboard</span>
                    </a>
                @else
                    <!-- If user is not logged in, show Login and Register buttons -->
                    <a href="{{ route('login') }}" class="btn btn-login">
                        <i class="fas fa-sign-in-alt btn-icon"></i>
                        <span>LOGIN</span>
                    </a>
                    
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-register">
                            <i class="fas fa-user-plus btn-icon"></i>
                            <span>REGISTER</span>
                        </a>
                    @endif
                @endauth
            </div>
        @endif
    </div>

    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
        
        // Add click animation to buttons with ripple effect
        document.querySelectorAll('.btn').forEach(button => {
            button.addEventListener('click', function(e) {
                let ripple = document.createElement('span');
                ripple.classList.add('ripple');
                this.style.position = 'relative';
                this.style.overflow = 'hidden';
                this.appendChild(ripple);
                
                let rect = this.getBoundingClientRect();
                let x = e.clientX - rect.left;
                let y = e.clientY - rect.top;
                
                ripple.style.left = (x - 10) + 'px';
                ripple.style.top = (y - 10) + 'px';
                ripple.style.width = '20px';
                ripple.style.height = '20px';
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });
    </script>
</body>
</html>