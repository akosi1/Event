<!DOCTYPE html>
<html lang="en">
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
        
        .floating-animation {
            animation: floating 6s ease-in-out infinite;
        }
        
        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .split-container {
            display: flex;
            min-height: 100vh;
        }
        
        .left-section {
            flex: 1;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .right-section {
            flex: 1;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
        }
        
        .btn-modern {
            position: relative;
            overflow: hidden;
            padding: 1rem 2.5rem;
            font-size: 1.125rem;
            font-weight: 600;
            border-radius: 0.75rem;
            transition: all 0.4s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            text-decoration: none;
            width: 100%;
        }
        
        .btn-admin {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-student {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(245, 87, 108, 0.4);
        }
        
        .btn-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, 
                transparent 0%, 
                rgba(255, 255, 255, 0.5) 50%, 
                transparent 100%);
            transition: left 0.6s ease;
            transform: skewX(-25deg);
        }
        
        .btn-modern:hover::before {
            left: 100%;
        }
        
        .btn-modern:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.25);
        }
        
        .btn-modern:active {
            transform: translateY(-2px);
        }
        
        .button-container {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            width: 100%;
            max-width: 400px;
        }
        
        .logo-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        @media (max-width: 968px) {
            .split-container {
                flex-direction: column;
            }
            
            .right-section {
                min-height: 250px;
            }
            
            .left-section {
                padding: 3rem 1.5rem;
            }
        }
    </style>
</head>
<body class="antialiased">
    <div class="split-container">
        <!-- Left Section - White Card -->
        <div class="left-section">
            <div class="text-center max-w-xl w-full">
                <div class="mb-8">
                    <div class="logo-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4 bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent leading-tight">
                        MCC Event & Portfolio
                    </h1>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">
                        Organizer
                    </h2>
                </div>
                
                <p class="text-lg md:text-xl text-gray-600 mb-10 px-4">
                    Manage your academic journey with ease
                </p>
                
                <!-- CTA Buttons -->
                <div class="button-container mx-auto px-4">
                    <a href="/admin/login" class="btn-modern btn-admin">
                        <i class="fas fa-user-shield text-xl"></i>
                        <span>ADMIN LOGIN</span>
                    </a>
                    
                    <a href="/login" class="btn-modern btn-student">
                        <i class="fas fa-user-graduate text-xl"></i>
                        <span>STUDENT LOGIN</span>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Right Section - Gradient Background -->
        <div class="right-section">
            <!-- Background Elements -->
            <div class="absolute inset-0">
                <div class="absolute top-20 left-10 w-32 h-32 md:w-72 md:h-72 bg-white opacity-10 rounded-full floating-animation"></div>
                <div class="absolute bottom-20 right-10 w-48 h-48 md:w-96 md:h-96 bg-white opacity-5 rounded-full floating-animation" style="animation-delay: -3s;"></div>
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-white opacity-5 rounded-full floating-animation" style="animation-delay: -1.5s;"></div>
            </div>
            
            <!-- Decorative elements in gradient section -->
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="text-white text-center opacity-20">
                    <i class="fas fa-university text-9xl mb-6"></i>
                    <p class="text-3xl font-bold">Welcome to MCC</p>
                </div>
            </div>
        </div>
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
    </script>
</body>
</html>