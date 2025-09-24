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
        
        
    </head>
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