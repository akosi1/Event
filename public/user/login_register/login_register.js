// Login/Register Animation System
class AuthAnimator {
    constructor() {
        this.isRegisterMode = false;
        this.isAnimating = false;
        this.init();
    }

    init() {
        this.initElements();
        this.createParticles();
        this.startParticleSystem();
        this.bindEvents();
        this.setupFormAnimations();
    }

    initElements() {
        this.authContainer = document.querySelector('.auth-container');
        this.diagonalSection = document.querySelector('.diagonal-section');
        this.welcomeSection = document.querySelector('.welcome-section');
        this.welcomeTitle = document.getElementById('welcomeTitle') || this.welcomeSection.querySelector('h1');
        this.welcomeText = document.getElementById('welcomeText') || this.welcomeSection.querySelector('p');
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
        
        // Create multiple particles
        for (let i = 0; i < 15; i++) {
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
        }, 3000);
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

        // Handle form submissions if needed
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (form.matches('form')) {
                this.handleFormSubmit(e);
            }
        });
    }

    switchToRegister() {
        if (this.isAnimating || this.isRegisterMode) return;
        this.isAnimating = true;
        this.isRegisterMode = true;

        // Update welcome section text with fade
        this.updateWelcomeText('WELCOME!', 'Create your account');
        
        // Add register mode classes with stagger
        setTimeout(() => {
            this.authContainer.classList.add('register-mode');
        }, 100);
        
        setTimeout(() => {
            this.diagonalSection.classList.add('register-mode');
        }, 200);
        
        setTimeout(() => {
            this.welcomeSection.classList.add('register-mode');
        }, 300);
        
        setTimeout(() => {
            this.formSection.classList.add('register-mode');
        }, 400);

        // Handle form transition
        this.transitionToRegisterForm();

        setTimeout(() => {
            this.isAnimating = false;
        }, 800);
    }

    switchToLogin() {
        if (this.isAnimating || !this.isRegisterMode) return;
        this.isAnimating = true;
        this.isRegisterMode = false;

        // Update welcome section text
        this.updateWelcomeText('WELCOME BACK!', 'Please sign in to continue');

        // Remove register mode classes with stagger
        setTimeout(() => {
            this.formSection.classList.remove('register-mode');
        }, 100);
        
        setTimeout(() => {
            this.welcomeSection.classList.remove('register-mode');
        }, 200);
        
        setTimeout(() => {
            this.diagonalSection.classList.remove('register-mode');
        }, 300);
        
        setTimeout(() => {
            this.authContainer.classList.remove('register-mode');
        }, 400);

        // Handle form transition
        this.transitionToLoginForm();

        setTimeout(() => {
            this.isAnimating = false;
        }, 800);
    }

    updateWelcomeText(title, text) {
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
        const currentContent = this.formSection.querySelector('.form-content') || this.formSection;
        
        if (this.singleFormMode) {
            // Create register form content
            this.createRegisterFormContent();
        } else if (this.loginForm && this.registerForm) {
            // Handle multiple forms
            this.loginForm.classList.add('sliding-out');
            setTimeout(() => {
                this.loginForm.style.display = 'none';
                this.registerForm.style.display = 'block';
                this.registerForm.classList.add('sliding-in');
            }, 250);
        }
    }

    transitionToLoginForm() {
        const currentContent = this.formSection.querySelector('.form-content') || this.formSection;
        
        if (this.singleFormMode) {
            // Create login form content
            this.createLoginFormContent();
        } else if (this.loginForm && this.registerForm) {
            // Handle multiple forms
            this.registerForm.classList.add('sliding-out');
            setTimeout(() => {
                this.registerForm.style.display = 'none';
                this.loginForm.style.display = 'block';
                this.loginForm.classList.add('sliding-in');
            }, 250);
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
        const formContainer = form.parentElement;
        const currentHTML = formContainer.innerHTML;
        
        // Create wrapper for transition
        const wrapper = document.createElement('div');
        wrapper.className = 'form-content sliding-out';
        wrapper.innerHTML = currentHTML.replace(form.outerHTML, '');
        
        formContainer.innerHTML = '';
        formContainer.appendChild(wrapper);
        
        setTimeout(() => {
            const newWrapper = document.createElement('div');
            newWrapper.className = 'form-content sliding-in';
            newWrapper.innerHTML = `
                <div class="form-title">Sign Up</div>
                <form method="POST" action="/register">
                    <input type="hidden" name="_token" value="${this.getCSRFToken()}">
                    ${registerHTML}
                </form>
            `;
            
            formContainer.innerHTML = '';
            formContainer.appendChild(newWrapper);
            
            // Re-bind form events
            this.setupFormAnimations();
        }, 250);
    }

    createLoginFormContent() {
        const form = this.formSection.querySelector('form');
        
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
        const formContainer = form.parentElement;
        const currentHTML = formContainer.innerHTML;
        
        // Create wrapper for transition
        const wrapper = document.createElement('div');
        wrapper.className = 'form-content sliding-out';
        wrapper.innerHTML = currentHTML.replace(form.outerHTML, '');
        
        formContainer.innerHTML = '';
        formContainer.appendChild(wrapper);
        
        setTimeout(() => {
            const newWrapper = document.createElement('div');
            newWrapper.className = 'form-content sliding-in';
            newWrapper.innerHTML = `
                <div class="form-title">Sign In</div>
                <form method="POST" action="/login">
                    <input type="hidden" name="_token" value="${this.getCSRFToken()}">
                    ${loginHTML}
                </form>
            `;
            
            formContainer.innerHTML = '';
            formContainer.appendChild(newWrapper);
            
            // Re-bind form events
            this.setupFormAnimations();
        }, 250);
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
            input.addEventListener('focus', this.inputFocusHandler);
            input.addEventListener('blur', this.inputBlurHandler);
        });
    }

    inputFocusHandler(e) {
        const wrapper = e.target.parentElement;
        if (wrapper.classList.contains('input-wrapper')) {
            wrapper.style.transform = 'scale(1.02)';
            wrapper.style.zIndex = '10';
        }
    }

    inputBlurHandler(e) {
        const wrapper = e.target.parentElement;
        if (wrapper.classList.contains('input-wrapper')) {
            wrapper.style.transform = 'scale(1)';
            wrapper.style.zIndex = 'auto';
        }
    }

    handleFormSubmit(e) {
        const submitBtn = e.target.querySelector('.btn-submit');
        if (submitBtn) {
            submitBtn.style.transform = 'translateY(-1px)';
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        }
        
        // Allow form to submit normally
        // Add any additional form validation or AJAX handling here if needed
    }

    // Public method to manually trigger mode switch
    setMode(mode) {
        if (mode === 'register' && !this.isRegisterMode) {
            this.switchToRegister();
        } else if (mode === 'login' && this.isRegisterMode) {
            this.switchToLogin();
        }
    }

    // Cleanup method
    destroy() {
        if (this.particleInterval) {
            clearInterval(this.particleInterval);
        }
        
        // Remove event listeners
        document.removeEventListener('click', this.clickHandler);
        document.removeEventListener('submit', this.submitHandler);
    }
}

// Initialize the auth animator when DOM is ready
let authAnimator;

document.addEventListener('DOMContentLoaded', function() {
    authAnimator = new AuthAnimator();
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
    
    history.pushState({ mode: mode }, title, url);
}