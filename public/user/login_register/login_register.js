// Login/Register Animation System with Enhanced Mobile Support
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
        this.detectCurrentMode();
    }

    initElements() {
        this.authContainer = document.querySelector('.auth-container');
        this.diagonalSection = document.querySelector('.diagonal-section');
        this.welcomeSection = document.querySelector('.welcome-section');
        this.welcomeTitle = document.getElementById('welcomeTitle') || this.welcomeSection?.querySelector('h1');
        this.welcomeText = document.getElementById('welcomeText') || this.welcomeSection?.querySelector('p');
        this.formSection = document.querySelector('.form-section');
        this.particlesContainer = document.getElementById('particles');
    }

    detectCurrentMode() {
        // Detect if we're on register page
        const currentPath = window.location.pathname;
        if (currentPath.includes('register')) {
            this.isRegisterMode = true;
            // Ensure register classes are applied
            setTimeout(() => {
                this.authContainer?.classList.add('register-mode');
                this.diagonalSection?.classList.add('register-mode');
                this.welcomeSection?.classList.add('register-mode');
                this.formSection?.classList.add('register-mode');
            }, 100);
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
        // Handle clicks on auth links for animation - Fixed targeting
        document.addEventListener('click', (e) => {
            // Check if clicked element is a link in auth-links
            const authLinksContainer = e.target.closest('.auth-links');
            if (!authLinksContainer) return;

            // More specific targeting for register links
            if (e.target.matches('a[href*="register"]') || 
                e.target.getAttribute('href') === '/register' ||
                (e.target.textContent && e.target.textContent.toLowerCase().includes('sign up here'))) {
                e.preventDefault();
                this.switchToRegister();
                return;
            }
            
            // More specific targeting for login links
            if (e.target.matches('a[href*="login"]') || 
                e.target.getAttribute('href') === '/login' ||
                (e.target.textContent && e.target.textContent.toLowerCase().includes('sign in here'))) {
                e.preventDefault();
                this.switchToLogin();
                return;
            }
        });

        // Handle form submissions
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
        this.updateWelcomeText('WELCOME!', 'Create your student account');
        
        // Add register mode classes with stagger
        setTimeout(() => {
            this.authContainer?.classList.add('register-mode');
        }, 100);
        
        setTimeout(() => {
            this.diagonalSection?.classList.add('register-mode');
        }, 200);
        
        setTimeout(() => {
            this.welcomeSection?.classList.add('register-mode');
        }, 300);
        
        setTimeout(() => {
            this.formSection?.classList.add('register-mode');
        }, 400);

        // Navigate to register page after animation
        setTimeout(() => {
            this.isAnimating = false;
            window.location.href = '/register';
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
            this.formSection?.classList.remove('register-mode');
        }, 100);
        
        setTimeout(() => {
            this.welcomeSection?.classList.remove('register-mode');
        }, 200);
        
        setTimeout(() => {
            this.diagonalSection?.classList.remove('register-mode');
        }, 300);
        
        setTimeout(() => {
            this.authContainer?.classList.remove('register-mode');
        }, 400);

        // Navigate to login page after animation
        setTimeout(() => {
            this.isAnimating = false;
            window.location.href = '/login';
        }, 800);
    }

    updateWelcomeText(title, text) {
        if (!this.welcomeTitle || !this.welcomeText) return;
        
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

    setupFormAnimations() {
        // Enhanced form animations
        document.querySelectorAll('input, select').forEach(input => {
            input.addEventListener('focus', this.inputFocusHandler.bind(this));
            input.addEventListener('blur', this.inputBlurHandler.bind(this));
        });

        // Setup select labels
        document.querySelectorAll('.form-select').forEach(select => {
            const label = select.parentElement.querySelector('.select-label');
            
            const updateLabel = () => {
                if (select.value && select.value !== '') {
                    label.style.top = '-8px';
                    label.style.left = '8px';
                    label.style.fontSize = '11px';
                    label.style.color = '#dc2626';
                    label.style.background = 'rgba(45, 21, 21, 0.9)';
                    label.style.padding = '0 4px';
                    label.style.borderRadius = '4px';
                } else {
                    label.style.top = '12px';
                    label.style.left = '12px';
                    label.style.fontSize = '13px';
                    label.style.color = 'rgba(255, 255, 255, 0.6)';
                    label.style.background = 'transparent';
                    label.style.padding = '0';
                }
            };

            select.addEventListener('change', updateLabel);
            select.addEventListener('focus', updateLabel);
            updateLabel();
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
            
            const isLogin = e.target.action.includes('login');
            const isRegister = e.target.action.includes('register');
            
            if (isLogin) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing In...';
            } else if (isRegister) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account...';
            } else {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            }
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

    // Cleanup method
    destroy() {
        if (this.particleInterval) {
            clearInterval(this.particleInterval);
        }
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