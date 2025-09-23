<x-guest-layout>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="{{ asset('user/login_register/login_register.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <div class="auth-container register-mode">
        <div class="floating-particles" id="particles"></div>
        <div class="diagonal-section register-mode"></div>
        
        <div class="welcome-section register-mode">
            <h1 id="welcomeTitle">WELCOME!</h1>
            <p id="welcomeText">Create your account</p>
        </div>
        
        <div class="form-section register-mode">
            <div class="form-content">
                <div class="form-title">Sign Up</div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="form-row">
                        <div class="form-group">
                            <div class="input-wrapper">
                                <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" 
                                       class="form-control" placeholder=" " required autofocus autocomplete="given-name">
                                <label class="input-label">First Name</label>
                                <i class="fas fa-user input-icon"></i>
                            </div>
                            <x-input-error :messages="$errors->get('first_name')" class="error-msg" />
                        </div>

                        <div class="form-group">
                            <div class="input-wrapper">
                                <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" 
                                       class="form-control" placeholder=" " required autocomplete="family-name">
                                <label class="input-label">Last Name</label>
                                <i class="fas fa-user input-icon"></i>
                            </div>
                            <x-input-error :messages="$errors->get('last_name')" class="error-msg" />
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper">
                            <input id="middle_name" type="text" name="middle_name" value="{{ old('middle_name') }}" 
                                   class="form-control" placeholder=" " autocomplete="additional-name">
                            <label class="input-label">Middle Name (Optional)</label>
                            <i class="fas fa-user-circle input-icon"></i>
                        </div>
                        <x-input-error :messages="$errors->get('middle_name')" class="error-msg" />
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper">
                            <input id="email" type="email" name="email" value="{{ old('email') }}" 
                                   class="form-control" placeholder=" " required autocomplete="username">
                            <label class="input-label">Email Address</label>
                            <i class="fas fa-envelope input-icon"></i>
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="error-msg" />
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper">
                            <select id="department" name="department" class="form-select" required>
                                <option value=""></option>
                                <option value="BSIT" {{ old('department') == 'BSIT' ? 'selected' : '' }}>Bachelor of Science in Information Technology</option>
                                <option value="BSBA" {{ old('department') == 'BSBA' ? 'selected' : '' }}>Bachelor of Science in Business Administration</option>
                                <option value="BSED" {{ old('department') == 'BSED' ? 'selected' : '' }}>Bachelor of Science in Education</option>
                                <option value="BEED" {{ old('department') == 'BEED' ? 'selected' : '' }}>Bachelor of Elementary Education</option>
                                <option value="BSHM" {{ old('department') == 'BSHM' ? 'selected' : '' }}>Bachelor of Science in Hospitality Management</option>
                            </select>
                            <label class="select-label">Select Department</label>
                            <i class="fas fa-graduation-cap input-icon"></i>
                        </div>
                        <x-input-error :messages="$errors->get('department')" class="error-msg" />
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <div class="input-wrapper">
                                <input id="password" type="password" name="password" 
                                       class="form-control" placeholder=" " required autocomplete="new-password">
                                <label class="input-label">Password</label>
                                <i class="fas fa-lock input-icon"></i>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="error-msg" />
                        </div>

                        <div class="form-group">
                            <div class="input-wrapper">
                                <input id="password_confirmation" type="password" name="password_confirmation" 
                                       class="form-control" placeholder=" " required autocomplete="new-password">
                                <label class="input-label">Confirm Password</label>
                                <i class="fas fa-check-circle input-icon"></i>
                            </div>
                            <x-input-error :messages="$errors->get('password_confirmation')" class="error-msg" />
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-user-plus"></i>
                        {{ __('Sign Up') }}
                    </button>

                    <div class="auth-links">
                        <p>{{ __('Already registered?') }} 
                           <a href="{{ route('login') }}">{{ __('Sign in here') }}</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('user/login_register/login_register.js') }}"></script>
    <script>
        // Set register mode on page load and ensure mobile optimization
        document.addEventListener('DOMContentLoaded', function() {
            // Force mobile layout if on mobile device
            if (window.innerWidth <= 768) {
                document.body.classList.add('mobile-view');
                
                // Ensure proper mobile styling
                const authContainer = document.querySelector('.auth-container');
                const formSection = document.querySelector('.form-section');
                
                if (authContainer) {
                    authContainer.style.display = 'flex';
                    authContainer.style.alignItems = 'center';
                    authContainer.style.justifyContent = 'center';
                    authContainer.style.minHeight = '100vh';
                }
                
                if (formSection) {
                    formSection.style.position = 'relative';
                    formSection.style.transform = 'none';
                    formSection.style.left = 'auto';
                    formSection.style.top = 'auto';
                    formSection.style.width = '100%';
                    formSection.style.maxWidth = '400px';
                    formSection.style.margin = '0 auto';
                }
            }
            
            // Initialize animator
            if (window.authAnimator) {
                window.authAnimator.isRegisterMode = true;
                
                // Force mobile layout in animator if needed
                if (window.innerWidth <= 768) {
                    window.authAnimator.forceMobileLayout();
                }
            }
        });
        
        // Handle orientation change
        window.addEventListener('orientationchange', function() {
            setTimeout(function() {
                // Recalculate layout after orientation change
                if (window.innerWidth <= 768) {
                    const authContainer = document.querySelector('.auth-container');
                    const formSection = document.querySelector('.form-section');
                    
                    if (authContainer) {
                        authContainer.style.height = window.innerHeight + 'px';
                    }
                    
                    if (formSection) {
                        formSection.style.maxWidth = window.innerWidth > window.innerHeight ? '450px' : '400px';
                    }
                }
            }, 200);
        });

        // Prevent zoom on iOS
        document.addEventListener('gesturestart', function (e) {
            e.preventDefault();
        });

        // Handle viewport height for mobile browsers
        function setViewportHeight() {
            let vh = window.innerHeight * 0.01;
            document.documentElement.style.setProperty('--vh', vh + 'px');
        }

        setViewportHeight();
        window.addEventListener('resize', setViewportHeight);
    </script>

    <style>
        /* Additional mobile-specific styles */
        @media (max-width: 768px) {
            body.mobile-view {
                height: 100vh;
                height: calc(var(--vh, 1vh) * 100);
                overflow: hidden;
            }
            
            .auth-container {
                height: 100vh !important;
                height: calc(var(--vh, 1vh) * 100) !important;
                padding: 15px !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
            }
            
            .diagonal-section {
                display: none !important;
            }
            
            .welcome-section {
                display: none !important;
            }
            
            .form-section {
                position: relative !important;
                transform: none !important;
                left: auto !important;
                top: auto !important;
                width: 100% !important;
                max-width: 400px !important;
                margin: 0 auto !important;
            }
        }
        
        /* Handle landscape orientation */
        @media (max-width: 768px) and (orientation: landscape) {
            .auth-container {
                overflow-y: auto !important;
                align-items: flex-start !important;
                padding-top: 20px !important;
                padding-bottom: 20px !important;
            }
            
            .form-section {
                margin-top: 10px !important;
                margin-bottom: 10px !important;
                max-width: 450px !important;
            }
        }
        
        /* Very small screens */
        @media (max-width: 375px) {
            .form-section {
                max-width: calc(100vw - 20px) !important;
            }
        }
    </style>
</x-guest-layout>