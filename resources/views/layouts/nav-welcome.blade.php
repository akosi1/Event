<!-- Navigation Component -->
<nav class="fixed top-0 left-0 right-0 z-50 bg-transparent backdrop-blur-md border-b border-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ url('/') }}" class="flex items-center space-x-3">
                    <img src="{{ asset('images/logo.png') }}" alt="MCC Logo" class="h-10 w-10 object-contain">
                    <span class="text-white font-bold text-xl font-comfortaa tracking-wide">mcc events</span>
                </a>
            </div>
            
            <!-- Navigation Links (Center) - Optional for future use -->
            <div class="hidden md:block">
                <div class="ml-10 flex items-baseline space-x-4">
                    <!-- Add navigation links here if needed -->
                </div>
            </div>
            
            <!-- Sign In / Sign Up Buttons (Right) -->
            <div class="flex items-center space-x-3">
                @if (Route::has('login'))
                    @auth   
                        <a href="{{ url('/dashboard') }}" class="nav-btn nav-btn-primary">
                            <i class="fas fa-tachometer-alt"></i>
                            <span class="hidden sm:inline">dashboard</span>
                        </a>
                    @else
                        <!-- Sign In Button -->
                        <a href="{{ route('login') }}" class="nav-btn nav-btn-ghost">
                            <i class="fas fa-sign-in-alt"></i>
                            <span class="hidden sm:inline">sign in</span>
                        </a>
                        
                        <!-- Sign Up Button -->
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="nav-btn nav-btn-primary">
                                <i class="fas fa-user-plus"></i>
                                <span class="hidden sm:inline">sign up</span>
                            </a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </div>
</nav>

<style>
    /* Font Classes */
    .font-comfortaa {
        font-family: 'Comfortaa', cursive;
    }
    
    .font-nunito {
        font-family: 'Nunito', sans-serif;
    }
    
    /* Navigation Button Styles */
    .nav-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 25px;
        font-size: 14px;
        font-weight: 700;
        font-family: 'Nunito', sans-serif;
        text-decoration: none;
        transition: all 0.3s ease;
        min-height: 42px;
        text-transform: lowercase;
        letter-spacing: 0.5px;
    }
    
    .nav-btn-ghost {
        background: rgba(255, 255, 255, 0.12);
        border: 2px solid rgba(255, 255, 255, 0.25);
        color: white;
        backdrop-filter: blur(10px);
    }
    
    .nav-btn-ghost:hover {
        background: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.4);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(255, 255, 255, 0.1);
    }
    
    .nav-btn-primary {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 50%, #e53935 100%);
        border: 2px solid transparent;
        color: white;
        box-shadow: 0 4px 15px rgba(229, 57, 53, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .nav-btn-primary:before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }
    
    .nav-btn-primary:hover:before {
        left: 100%;
    }
    
    .nav-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(229, 57, 53, 0.4);
    }
    
    /* Mobile responsiveness */
    @media (max-width: 640px) {
        .nav-btn {
            padding: 8px 14px;
            font-size: 13px;
            min-height: 38px;
        }
        
        .font-comfortaa {
            font-size: 18px;
        }
    }
</style>