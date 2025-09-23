// Login/Register Animation System
class AuthAnimator {
    constructor() {
        this.isRegisterMode = false;
        this.isAnimating = false;
        this.isMobile = window.innerWidth <= 768;
        this.init();
    }

    init() {
        this.initElements();
        this.createParticles();
        this.startParticleSystem();
        this.bindEvents();
        this.setupFormAnimations();
        this.handleResize();
    }

    initElements() {
        this.authContainer = document.querySelector('.auth-container');
        this.diagonalSection = document.querySelector('.diagonal-section');
        this.welcomeSection = document.querySelector('.welcome-section');
        this.welcomeTitle = document.getElementById('welcomeTitle') || this.welcomeSection?.querySelector('h1');
        this.welcomeText = document.getElementById('welcomeText') || this.welcomeSection?.querySelector('p');
        this.formSection = document.querySelector('.form-section');
        this.particlesContainer = document.getElementById('particles');
        
        // Form contents
        this.loginForm = document.getElementById('loginForm');
        this.registerForm = document.getElementById('registerForm');
        
        // If single form, we'll create the toggle functionality
        if (!this.loginForm && !this.registerForm) {
            this.singleFormMode = true;
            this.currentForm = this.formSection.querySelector('form');
        }
    }

    createParticles() {
        if (!this.particlesContainer) return;
        
        // Reduce particles on mobile for better performance
        const particleCount = this.isMobile ? 8 : 15;
        
        // Create multiple particles
        for (let i = 0; i < particleCount; i++) {
            setTimeout(() => {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 8 + 's';
                this.particlesContainer.appendChild(particle);
                
                // Remove particle after animation
                setTimeout(() => {
                    if (particle.parentNode) {
                        particle.parentNode.removeChild(particle);
                    }
                }, 12000);
            }, i * 200);
        }
    }

    startParticleSystem() {
        this.createParticles();
        this.particleInterval = setInterval(() => {
            this.createParticles();
        }, this.isMobile ? 5000 : 3000); // Slower on mobile
    }

    bindEvents() {
        // Auth links click handlers
        document.addEventListener('click', (e) => {
            if (e.target.matches('a[href*="register"]') || 
                e.target.matches('a[href*="sign-up"]') ||
                e.target.matches('a[href*="signup"]')) {
                e.preventDefault();
                this.switchToRegister();
            }
            
            if (e.target.matches('a[href*="login"]') || 
                e.target.matches('a[href*="sign-in"]') ||
                e.target.matches('a[href*="signin"]')) {
                e.preventDefault();
                this.switchToLogin();
            }
        });

        // Handle form submissions
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (form.matches('form')) {
                this.handleFormSubmit(e);
            }
        });

        // Handle window resize
        window.addEventListener('resize', () => {
            this.handleResize();
        });

        // Handle orientation change for mobile
        window.addEventListener('orientationchange', () => {
            setTimeout(() => {
                this.handleResize();
            }, 200);
        });
    }

    handleResize() {
        const wasMobile = this.isMobile;
        this.isMobile = window.innerWidth <= 768;
        
        // If switching between mobile and desktop, adjust particle system
        if (wasMobile !== this.isMobile) {
            clearInterval(this.particleInterval);
            this.startParticleSystem();
        }
        
        // Ensure proper centering on mobile
        if (this.isMobile) {
            this.ensureMobileCentering();
        }
    }

    ensureMobileCentering() {
        // Force mobile layout adjustments
        if (this.formSection) {
            this.formSection.style.position = 'relative';
            this.formSection.style.transform = 'none';
            this.formSection.style.left = 'auto';
            this.formSection.style.top = 'auto';
        }
        
        // Hide elements that aren't needed on mobile
        if (this.diagonalSection) {
            this.diagonalSection.style.display = 'none';
        }
        
        if (this.welcomeSection) {
            this.welcomeSection.style.display = 'none';
        }
    }

    switchToRegister() {
        if (this.isAnimating || this.isRegisterMode) return;
        this.isAnimating = true;
        this.isRegisterMode = true;

        // Skip welcome text update on mobile since it's hidden
        if (!this.isMobile) {
            this.updateWelcomeText('WELCOME!', 'Create your account');
        }
        
        // Add register mode classes with stagger (only on desktop)
        if (!this.isMobile) {
            setTimeout(() => {
                this.authContainer.classList.add('register-mode');
            }, 100);
            
            setTimeout(() => {
                this.diagonalSection?.classList.add('register-mode');
            }, 200);
            
            setTimeout(() => {
                this.welcomeSection?.classList.add('register-mode');
            }, 300);
        } else {
            // Immediate class addition on mobile
            this.authContainer.classList.add('register-mode');
        }
        
        setTimeout(() => {
            this.formSection.classList.add('register-mode');
        }, this.isMobile ? 100 : 400);

        // Handle form transition
        this.transitionToRegisterForm();

        setTimeout(() => {
            this.isAnimating = false;
        }, this.isMobile ? 400 : 800);
    }

    switchToLogin() {
        if (this.isAnimating || !this.isRegisterMode) return;
        this.isAnimating = true;
        this.isRegisterMode = false;

        // Skip welcome text update on mobile since it's hidden
        if (!this.isMobile) {
            this.updateWelcomeText('WELCOME BACK!', 'Please sign in to continue');
        }

        // Remove register mode classes with stagger (only on desktop)
        if (!this.isMobile) {
            setTimeout(() => {
                this.formSection.classList.remove('register-mode');
            }, 100);
            
            setTimeout(() => {
                this.welcomeSection?.classList.remove('register-mode');
            }, 200);
            
            setTimeout(() => {
                this.diagonalSection?.classList.remove('register-mode');
            }, 300);
            
            setTimeout(() => {
                this.authContainer.classList.remove('register-mode');
            }, 400);
        } else {
            // Immediate class removal on mobile
            this.formSection.classList.remove('register-mode');
            this.authContainer.classList.remove('register-mode');
        }

        // Handle form transition
        this.transitionToLoginForm();

        setTimeout(() => {
            this.isAnimating = false;
        }, this.isMobile ? 400 : 800);
    }

    updateWelcomeText(title, text) {
        // Skip on mobile since welcome section is hidden
        if (this.isMobile || !this.welcomeTitle || !this.welcomeText) return;
        
        // Fade out
        this.welcomeTitle.style.opacity = '0';
        this.welcomeText.style.opacity = '0';
        
        setTimeout(() => {
            this.welcomeTitle.textContent = title;
            this.welcomeText.textContent = text;
            
            // Fade in
            this.welcomeTitle.style.opacity = '1';
            this.welcomeText.style.opacity = '1';
        }, 300);
    }

    transitionToRegisterForm() {
        if (this.singleFormMode) {
            this.createRegisterFormContent();
        } else if (this.loginForm && this.registerForm) {
            this.loginForm.classList.add('sliding-out');
            setTimeout(() => {
                this.loginForm.style.display = 'none';
                this.registerForm.style.display = 'block';
                this.registerForm.classList.add('sliding-in');
            }, this.isMobile ? 150 : 250);
        }
    }

    transitionToLoginForm() {
        if (this.singleFormMode) {
            this.createLoginFormContent();
        } else if (this.loginForm && this.registerForm) {
            this.registerForm.classList.add('sliding-out');
            setTimeout(() => {
                this.registerForm.style.display = 'none';
                this.loginForm.style.display = 'block';
                this.loginForm.classList.add('sliding-in');
            }, this.isMobile ? 150 : 250);
        }
    }

    createRegisterFormContent() {
        const formTitle = this.formSection.querySelector('.form-title');
        const form = this.formSection.querySelector('form');
        
        // Update title
        if (formTitle) {
            formTitle.textContent = 'Sign Up';
        }
        
        // Update form action and method
        if (form) {
            form.action = '/register';
            form.method = 'POST';
        }
        
        // Create register form HTML
        const registerHTML = `
            <div class="form-row">
                <div class="form-group">
                    <div class="input-wrapper">
                        <input id="first_name" type="text" name="first_name" 
                               class="form-control" placeholder=" " required autofocus>
                        <label class="input-label">First Name</label>
                        <i class="fas fa-user input-icon"></i>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-wrapper">
                        <input id="last_name" type="text" name="last_name" 
                               class="form-control" placeholder=" " required>
                        <label class="input-label">Last Name</label>
                        <i class="fas fa-user input-icon"></i>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="input-wrapper">
                    <input id="middle_name" type="text" name="middle_name" 
                           class="form-control" placeholder=" ">
                    <label class="input-label">Middle Name (Optional)</label>
                    <i class="fas fa-user-circle input-icon"></i>
                </div>
            </div>

            <div class="form-group">
                <div class="input-wrapper">
                    <input id="email" type="email" name="email" 
                           class="form-control" placeholder=" " required>
                    <label class="input-label">Email Address</label>
                    <i class="fas fa-envelope input-icon"></i>
                </div>
            </div>

            <div class="form-group">
                <div class="input-wrapper">
                    <select id="department" name="department" class="form-select" required>
                        <option value=""></option>
                        <option value="BSIT">Bachelor of Science in Information Technology</option>
                        <option value="BSBA">Bachelor of Science in Business Administration</option>
                        <option value="BSED">Bachelor of Science in Education</option>
                        <option value="BEED">Bachelor of Elementary Education</option>
                        <option value="BSHM">Bachelor of Science in Hospitality Management</option>
                    </select>
                    <label class="select-label">Select Department</label>
                    <i class="fas fa-graduation-cap input-icon"></i>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <div class="input-wrapper">
                        <input id="password" type="password" name="password" 
                               class="form-control" placeholder=" " required>
                        <label class="input-label">Password</label>
                        <i class="fas fa-lock input-icon"></i>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-wrapper">
                        <input id="password_confirmation" type="password" name="password_confirmation" 
                               class="form-control" placeholder=" " required>
                        <label class="input-label">Confirm Password</label>
                        <i class="fas fa-check-circle input-icon"></i>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-user-plus"></i>
                Sign Up
            </button>

            <div class="auth-links">
                <p>Already registered? 
                   <a href="#" onclick="authAnimator.switchToLogin(); return false;">Sign in here</a>
                </p>
            </div>
        `;

        // Transition form content
        this.transitionFormContent(registerHTML, 'Sign Up', '/register');
    }

    createLoginFormContent() {
        // Create login form HTML
        const loginHTML = `
            <div class="form-group">
                <div class="input-wrapper">
                    <input id="email" type="email" name="email" 
                           class="form-control" placeholder=" " required autofocus>
                    <label class="input-label">Email Address</label>
                    <i class="fas fa-envelope input-icon"></i>
                </div>
            </div>

            <div class="form-group">
                <div class="input-wrapper">
                    <input id="password" type="password" name="password" 
                           class="form-control" placeholder=" " required>
                    <label class="input-label">Password</label>
                    <i class="fas fa-lock input-icon"></i>
                </div>
            </div>

            <div class="form-group">
                <div class="input-wrapper">
                    <select id="department" name="department" class="form-select" required>
                        <option value=""></option>
                        <option value="BSIT">BSIT</option>
                        <option value="BSBA">BSBA</option>
                        <option value="BSED">BSED</option>
                        <option value="BEED">BEED</option>
                        <option value="BSHM">BSHM</option>
                    </select>
                    <label class="select-label">Select Your Department</label>
                    <i class="fas fa-graduation-cap input-icon"></i>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-sign-in-alt"></i>
                Sign In
            </button>

            <div class="auth-links">
                <p>Don't have an account? 
                   <a href="#" onclick="authAnimator.switchToRegister(); return false;">Sign up here</a>
                </p>
            </div>
        `;

        // Transition form content
        this.transitionFormContent(loginHTML, 'Sign In', '/login');
    }

    transitionFormContent(newHTML, title, action) {
        const form = this.formSection.querySelector('form');
        const formContainer = form.parentElement;
        
        // Create wrapper for transition
        const wrapper = document.createElement('div');
        wrapper.className = 'form-content sliding-out';
        wrapper.innerHTML = formContainer.innerHTML;
        
        formContainer.innerHTML = '';
        formContainer.appendChild(wrapper);
        
        const transitionDelay = this.isMobile ? 150 : 250;
        
        setTimeout(() => {
            const newWrapper = document.createElement('div');
            newWrapper.className = 'form-content sliding-in';
            newWrapper.innerHTML = `
                <div class="form-title">${title}</div>
                <form method="POST" action="${action}">
                    <input type="hidden" name="_token" value="${this.getCSRFToken()}">
                    ${newHTML}
                </form>
            `;
            
            formContainer.innerHTML = '';
            formContainer.appendChild(newWrapper);
            
            // Re-bind form events and ensure mobile layout
            this.setupFormAnimations();
            if (this.isMobile) {
                this.ensureMobileCentering();
            }
        }, transitionDelay);
    }

    getCSRFToken() {
        // Try to get CSRF token from existing meta tag or form
        const metaToken = document.querySelector('meta[name="csrf-token"]');
        if (metaToken) return metaToken.getAttribute('content');
        
        const existingToken = document.querySelector('input[name="_token"]');
        if (existingToken) return existingToken.value;
        
        return '';
    }

    setupFormAnimations() {
        // Enhanced form animations
        document.querySelectorAll('input, select').forEach(input => {
            // Remove existing listeners to prevent duplicates
            input.removeEventListener('focus', this.inputFocusHandler);
            input.removeEventListener('blur', this.inputBlurHandler);
            
            // Add new listeners
            input.addEventListener('focus', this.inputFocusHandler.bind(this));
            input.addEventListener('blur', this.inputBlurHandler.bind(this));
        });
    }

    inputFocusHandler(e) {
        const wrapper = e.target.parentElement;
        if (wrapper.classList.contains('input-wrapper')) {
            if (!this.isMobile) {
                wrapper.style.transform = 'scale(1.02)';
                wrapper.style.zIndex = '10';
            }
        }
    }

    inputBlurHandler(e) {
        const wrapper = e.target.parentElement;
        if (wrapper.classList.contains('input-wrapper')) {
            if (!this.isMobile) {
                wrapper.style.transform = 'scale(1)';
                wrapper.style.zIndex = 'auto';
            }
        }
    }

    handleFormSubmit(e) {
        const submitBtn = e.target.querySelector('.btn-submit');
        if (submitBtn) {
            if (!this.isMobile) {
                submitBtn.style.transform = 'translateY(-1px)';
            }
            
            const originalHTML = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            submitBtn.disabled = true;
            
            // Re-enable button after a timeout (in case form validation fails)
            setTimeout(() => {
                if (submitBtn.disabled) {
                    submitBtn.innerHTML = originalHTML;
                    submitBtn.disabled = false;
                    if (!this.isMobile) {
                        submitBtn.style.transform = 'none';
                    }
                }
            }, 5000);
        }
    }

    // Public method to manually trigger mode switch
    setMode(mode) {
        if (mode === 'register' && !this.isRegisterMode) {
            this.switchToRegister();
        } else if (mode === 'login' && this.isRegisterMode) {
            this.switchToLogin();
        }
    }

    // Method to check if device is mobile
    checkMobile() {
        return window.innerWidth <= 768;
    }

    // Method to force mobile layout
    forceMobileLayout() {
        this.isMobile = true;
        this.ensureMobileCentering();
        
        // Restart particle system for mobile
        clearInterval(this.particleInterval);
        this.startParticleSystem();
    }

    // Cleanup method
    destroy() {
        if (this.particleInterval) {
            clearInterval(this.particleInterval);
        }
        
        // Remove event listeners
        window.removeEventListener('resize', this.handleResize);
        window.removeEventListener('orientationchange', this.handleResize);
    }
}

// Initialize the auth animator when DOM is ready
let authAnimator;

document.addEventListener('DOMContentLoaded', function() {
    authAnimator = new AuthAnimator();
    
    // Force mobile layout check
    if (window.innerWidth <= 768) {
        authAnimator.forceMobileLayout();
    }
});

// Expose to global scope for onclick handlers
window.authAnimator = authAnimator;

// Additional utility functions for Laravel integration
function updateFormMode(mode) {
    if (window.authAnimator) {
        window.authAnimator.setMode(mode);
    }
}

// Handle browser back/forward buttons
window.addEventListener('popstate', function(e) {
    if (e.state && e.state.mode) {
        updateFormMode(e.state.mode);
    }
});

// Push state for better UX
function pushAuthState(mode) {
    const title = mode === 'register' ? 'Sign Up' : 'Sign In';
    const url = mode === 'register' ? '/register' : '/login';
    
    if (history.pushState) {
        history.pushState({ mode: mode }, title, url);
    }
}

// Touch event handling for mobile
if ('ontouchstart' in window) {
    document.addEventListener('touchstart', function(e) {
        // Improve touch responsiveness
        if (e.target.matches('.btn-submit, .auth-links a')) {
            e.target.style.opacity = '0.8';
        }
    });
    
    document.addEventListener('touchend', function(e) {
        if (e.target.matches('.btn-submit, .auth-links a')) {
            setTimeout(() => {
                e.target.style.opacity = '1';
            }, 150);
        }
    });
}

// Prevent zoom on double tap for iOS
document.addEventListener('touchend', function(event) {
    var now = (new Date()).getTime();
    if (now - lastTouchEnd <= 300) {
        event.preventDefault();
    }
    lastTouchEnd = now;
}, false);

var lastTouchEnd = 0;