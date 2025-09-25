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
            body {
                font-family: 'Inter', sans-serif;
            }
            
            /* Background styling - Using your image with a soft purple overlay */
            .bg-image {
                background-image: url('{!! asset("images/background.jpg") !!}');
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
            
            /* Add a semi-transparent purple overlay for better text contrast */
            .overlay {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(135deg, rgba(74, 26, 92, 0.8) 0%, rgba(107, 44, 145, 0.8) 50%, rgba(61, 26, 120, 0.8) 100%);
                z-index: 1;
            }
            
            /* Content container - place over the overlay */
            .content-container {
                position: relative;
                z-index: 2;
                text-align: center;
                padding: 2rem;
                max-width: 1200px;
                margin: 0 auto;
                /* Move content slightly up */
                transform: translateY(-20px);
            }
            
            /* Remove glow effects from buttons */
            .login-btn {
                background: rgba(255, 255, 255, 0.1);
                border: 2px solid rgba(255, 255, 255, 0.3);
                backdrop-filter: blur(10px);
                border-radius: 0;
                padding: 12px 24px;
                font-size: 16px;
                font-weight: 600;
                color: white;
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                text-decoration: none;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                min-width: 140px;
                height: 48px;
                font-family: 'Barlow', sans-serif;
            }
            
            .login-btn:hover {
                background: rgba(255, 255, 255, 0.2);
                border-color: rgba(255, 255, 255, 0.5);
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            }
            
            .register-btn {
                background: linear-gradient(135deg, #e53935 0%, #d32f2f 100%);
                border: none;
                border-radius: 0;
                padding: 12px 24px;
                font-size: 16px;
                font-weight: 600;
                color: white;
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                text-decoration: none;
                box-shadow: 0 2px 8px rgba(229, 57, 53, 0.2);
                min-width: 140px;
                height: 48px;
                font-family: 'Barlow', sans-serif;
            }
            
            .register-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(229, 57, 53, 0.3);
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
                font-weight: 700;
                text-shadow: 0 0 10px rgba(0, 128, 255, 0.8), 0 0 20px rgba(0, 128, 255, 0.6), 0 0 30px rgba(0, 128, 255, 0.4);
                font-family: 'Barlow', sans-serif;
                letter-spacing: 1px;
                animation: pulse-blue 2s infinite alternate;
            }
            
            @keyframes pulse-blue {
                0% {
                    text-shadow: 0 0 10px rgba(0, 128, 255, 0.8), 0 0 20px rgba(0, 128, 255, 0.6), 0 0 30px rgba(0, 128, 255, 0.4);
                }
                100% {
                    text-shadow: 0 0 15px rgba(0, 128, 255, 0.9), 0 0 25px rgba(0, 128, 255, 0.7), 0 0 40px rgba(0, 128, 255, 0.5);
                }
            }
            
            .highlight-text {
                color: #FFD700;
                text-shadow: 0 0 10px rgba(255, 0, 0, 0.8), 0 0 20px rgba(255, 0, 0, 0.6), 0 0 30px rgba(255, 0, 0, 0.4);
                font-family: 'Barlow', sans-serif;
                letter-spacing: 1px;
                animation: pulse-red 2s infinite alternate;
                /* Make the size of "Organizer" match the main title */
                font-size: inherit;
                line-height: inherit;
                display: inline-block;
            }
            
            @keyframes pulse-red {
                0% {
                    text-shadow: 0 0 10px rgba(255, 0, 0, 0.8), 0 0 20px rgba(255, 0, 0, 0.6), 0 0 30px rgba(255, 0, 0, 0.4);
                }
                100% {
                    text-shadow: 0 0 15px rgba(255, 0, 0, 0.9), 0 0 25px rgba(255, 0, 0, 0.7), 0 0 40px rgba(255, 0, 0, 0.5);
                }
            }
            
            /* Button group styling */
            .button-group {
                display: flex;
                justify-content: center;
                gap: 1rem;
                margin-top: 2rem;
                flex-wrap: wrap;
            }
            
            /* Responsive design */
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
                    gap: 1rem;
                }
                
                .button-group .btn {
                    width: 100%;
                    max-width: 280px;
                }
                
                .main-title {
                    font-size: 2.5rem;
                    line-height: 1.2;
                }
                
                .highlight-text {
                    font-size: inherit;
                    line-height: inherit;
                }
            }
        </style>
    </head>
            <!-- test !--> 
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <body class="antialiased">
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
                    <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-bold text-white mb-4 sm:mb-6 leading-tight mobile-text">
                        MCC Event & Portfolio
                        <span class="highlight-text block sm:inline">
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
                                <!-- Updated LOGIN button with square shape and matched size -->
                                <a href="{{ route('login') }}" class="login-btn">
                                    <i class="fas fa-sign-in-alt"></i>
                                    <span>LOGIN</span>
                                </a>
                                
                                <!-- Updated REGISTER button with red gradient and matched size -->
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="register-btn">
                                        <i class="fas fa-user-plus"></i>
                                        <span>REGISTER</span>
                                    </a>
                                @endif
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
            document.querySelectorAll('.btn, .login-btn, .register-btn').forEach(button => {
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
                    
                    // Delay animation slightly after page load
                    setTimeout(() => {
                        titleElement.style.opacity = '1';
                        titleElement.style.transform = 'translateY(0)';
                    }, 300);
                }
            });
        </script>
    </body>
    </html>