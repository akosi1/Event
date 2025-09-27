<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MCC Event & Portfolio Organizer</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .glass-effect {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .hover-scale {
            transition: transform 0.3s ease;
        }
        
        .hover-scale:hover {
            transform: translateY(-2px);
        }
        
        .floating-animation {
            animation: floating 6s ease-in-out infinite;
        }
        
        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .feature-card {
            transition: all 0.3s ease;
        }
        
        .feature-card:hover {
            transform: scale(1.02);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
        }
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-2px);
        }
        
        .button-group {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
        }
        
        @media (max-width: 768px) {
            .mobile-text {
                font-size: 2rem;
                line-height: 1.2;
            }
            
            .mobile-subtitle {
                font-size: 1.1rem;
            }
            
            .button-group {
                flex-direction: column;
                width: 100%;
            }
            
            .button-group .btn {
                width: 100%;
                max-width: 280px;
            }
        }
    </style>
</head>
<body class="antialiased">
    <!-- Hero Section -->
    <section class="min-h-screen gradient-bg flex items-center justify-center relative overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute inset-0">
            <div class="absolute top-20 left-10 w-32 h-32 md:w-72 md:h-72 bg-white opacity-10 rounded-full floating-animation"></div>
            <div class="absolute bottom-20 right-10 w-48 h-48 md:w-96 md:h-96 bg-white opacity-5 rounded-full floating-animation" style="animation-delay: -3s;"></div>
        </div>
        
        <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <!-- Main Content -->
            <div class="mb-8">

                
                <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-bold text-white mb-4 sm:mb-6 leading-tight mobile-text">
                    MCC Event & Portfolio
                    <span class="bg-gradient-to-r from-yellow-400 to-pink-400 bg-clip-text text-transparent block sm:inline">
                        Organizer
                    </span>
                </h1>
                <!-- CTA Buttons -->
                @if (Route::has('login'))
                    <div class="button-group">
                        @auth   
                            <a href="{{ url('/dashboard') }}" class="btn btn-primary text-white px-8 py-4 rounded-full font-semibold text-lg inline-flex items-center space-x-3">
                                <i class="fas fa-tachometer-alt text-xl"></i>
                                <span>Go to Dashboard</span>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-secondary text-white px-8 py-4 rounded-full font-semibold text-lg inline-flex items-center space-x-3">
                                <i class="fas fa-sign-in-alt text-xl"></i>
                                <span>LOGIN</span>
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-primary text-white px-8 py-4 rounded-full font-semibold text-lg inline-flex items-center space-x-3">
                                    <i class="fas fa-user-plus text-xl"></i>
                                    <span>REGISTER</span>
                                </a>
                            @endif
                        @endauth
                    </div>
                @endif
                
               
                </div>
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
        document.querySelectorAll('.btn').forEach(button => {
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
    </script>
</body>
</html>