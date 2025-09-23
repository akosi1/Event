<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MCC Event & Portfolio Organizer</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
        
        .font-comfortaa {
            font-family: 'Comfortaa', cursive;
        }
        
        .font-nunito {
            font-family: 'Nunito', sans-serif;
        }
        
        /* Background styling */
        .bg-image {
            background-image: url('{!! asset("images/mcc background.jpg") !!}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        /* Add a semi-transparent purple overlay */
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(74, 26, 92, 0.8) 0%, rgba(107, 44, 145, 0.8) 50%, rgba(61, 26, 120, 0.8) 100%);
            z-index: 1;
        }
        
        /* Content container */
        .content-container {
            position: relative;
            z-index: 2;
            text-align: center;
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            transform: translateY(-20px);
            padding-top: 6rem; /* Add padding for fixed navigation */
        }
        
        /* Logo styling */
        .logo-container {
            max-width: 300px;
            margin: 0 auto 3rem;
            display: block;
        }
        
        .logo {
            width: 100%;
            height: auto;
            max-height: 150px;
            object-fit: contain;
            border-radius: 0;
        }
        
        /* Text styling with glowing effects */
        .main-title {
            color: white;
            font-weight: 800;
            text-shadow: 0 0 15px rgba(0, 150, 255, 0.6), 0 0 30px rgba(0, 150, 255, 0.4), 0 0 45px rgba(0, 150, 255, 0.2);
            font-family: 'Comfortaa', cursive;
            letter-spacing: 2px;
            animation: pulse-blue 3s infinite alternate;
            text-transform: lowercase;
            line-height: 1.1;
        }
        
        @keyframes pulse-blue {
            0% {
                text-shadow: 0 0 15px rgba(0, 150, 255, 0.6), 0 0 30px rgba(0, 150, 255, 0.4), 0 0 45px rgba(0, 150, 255, 0.2);
            }
            100% {
                text-shadow: 0 0 20px rgba(0, 150, 255, 0.8), 0 0 40px rgba(0, 150, 255, 0.6), 0 0 60px rgba(0, 150, 255, 0.4);
            }
        }
        
        .highlight-text {
            color: #FFD700;
            text-shadow: 0 0 15px rgba(255, 100, 100, 0.6), 0 0 30px rgba(255, 100, 100, 0.4), 0 0 45px rgba(255, 100, 100, 0.2);
            font-family: 'Comfortaa', cursive;
            letter-spacing: 2px;
            animation: pulse-red 3s infinite alternate;
            font-size: inherit;
            line-height: inherit;
            display: inline-block;
            text-transform: lowercase;
        }
        
        @keyframes pulse-red {
            0% {
                text-shadow: 0 0 15px rgba(255, 100, 100, 0.6), 0 0 30px rgba(255, 100, 100, 0.4), 0 0 45px rgba(255, 100, 100, 0.2);
            }
            100% {
                text-shadow: 0 0 20px rgba(255, 100, 100, 0.8), 0 0 40px rgba(255, 100, 100, 0.6), 0 0 60px rgba(255, 100, 100, 0.4);
            }
        }
        
        /* Button group styling for main CTA */
        .button-group {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }
        
        .cta-btn {
            background: rgba(255, 255, 255, 0.12);
            border: 2px solid rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(15px);
            border-radius: 30px;
            padding: 18px 36px;
            font-size: 18px;
            font-weight: 700;
            color: white;
            transition: all 0.4s ease;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            min-width: 220px;
            height: 60px;
            font-family: 'Nunito', sans-serif;
            justify-content: center;
            text-transform: lowercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
        }
        
        .cta-btn:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.6s;
        }
        
        .cta-btn:hover:before {
            left: 100%;
        }
        
        .cta-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.4);
            transform: translateY(-4px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
        }
        
        /* Add vertical highlight below buttons */
        .cta-btn:after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 4px;
            height: 0;
            background: linear-gradient(to top, rgba(255, 255, 255, 0.8), rgba(255, 255, 255, 0));
            transition: all 0.3s ease;
            border-radius: 2px;
        }
        
        .cta-btn:hover:after {
            height: 20px;
        }
        
        .cta-btn-primary {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 50%, #e53935 100%);
            border: 2px solid transparent;
            box-shadow: 0 8px 32px rgba(229, 57, 53, 0.4);
        }
        
        .cta-btn-primary:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 50px rgba(229, 57, 53, 0.5);
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .mobile-text {
                font-size: 2rem;
                line-height: 1.2;
            }
            
            .button-group {
                flex-direction: column;
                width: 100%;
                gap: 1rem;
                align-items: center;
            }
            
            .cta-btn {
                width: 100%;
                max-width: 280px;
            }
            
            .main-title {
                font-size: 2.5rem;
                line-height: 1.2;
            }
            
            .content-container {
                padding-top: 5rem;
            }
        }
    </style>
</head>
<body class="antialiased">
    <!-- Include Navigation Component -->
    @include('layouts.nav-welcome')
    
    <!-- Hero Section -->
    <section class="bg-image relative overflow-hidden">
        <!-- Semi-transparent purple overlay -->
        <div class="overlay"></div>
        
        <!-- Main Content -->
        <div class="content-container relative z-20">
            <!-- Logo -->
            <div class="logo-container">
                <img src="{{ asset('images/logo.png') }}" alt="MCC Logo" class="logo">
            </div>
            
            <!-- Main Content -->
            <div class="mb-8">
                <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-bold text-white mb-6 leading-tight mobile-text main-title">
                    mcc event & portfolio
                    <span class="highlight-text block sm:inline">
                        organizer
                    </span>
                </h1>
                
                <!-- Main CTA Buttons -->
                @if (Route::has('login'))
                    <div class="button-group">
                        @auth   
                            <a href="{{ url('/dashboard') }}" class="cta-btn cta-btn-primary">
                                <i class="fas fa-tachometer-alt text-xl"></i>
                                <span>go to dashboard</span>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="cta-btn">
                                <i class="fas fa-rocket"></i>
                                <span>get started</span>
                            </a>
                            
                            <a href="#features" class="cta-btn">
                                <i class="fas fa-info-circle"></i>
                                <span>learn more</span>
                            </a>
                        @endauth
                    </div>
                @endif
            </div>
        </div>
    </section>

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
        
        // Add click animation to buttons
        document.querySelectorAll('.cta-btn, .nav-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                let ripple = document.createElement('span');
                ripple.classList.add('ripple');
                this.appendChild(ripple);
                
                let x = e.clientX - e.target.offsetLeft;
                let y = e.clientY - e.target.offsetTop;
                
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });
        
        // Add animation to the title
        document.addEventListener('DOMContentLoaded', function() {
            const titleElement = document.querySelector('.main-title');
            if (titleElement) {
                titleElement.style.opacity = '0';
                titleElement.style.transform = 'translateY(20px)';
                titleElement.style.transition = 'all 0.8s ease';
                
                setTimeout(() => {
                    titleElement.style.opacity = '1';
                    titleElement.style.transform = 'translateY(0)';
                }, 300);
            }
        });
    </script>
</body>
</html>