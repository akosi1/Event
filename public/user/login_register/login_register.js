// Login/Register Animation System with Fixed Navigation
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
        } else {
            this.isRegisterMode = false;
            // Ensure login mode
            setTimeout(() => {
                this.authContainer?.classList.remove('register-mode');
                this.diagonalSection?.classList.remove('register-mode');
                this.welcomeSection?.classList.remove('register-mode');
                this.formSection?.classList.remove('register-mode');
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
        // Handle clicks on auth links for navigation (not animation)
        document.addEventListener('click', (e) => {
            // Check for register links
            if (e.target.closest('a[href*="register"]') || 
                (e.target.textContent && e.target.textContent.toLowerCase().includes('sign up here'))) {
                // Let the default navigation happen - don't prevent it
                return;
            }
            
            // Check for login links  
            if (e.target.closest('a[href*="login"]') || 
                (e.target.textContent && e.target.textContent.toLowerCase().includes('sign in here'))) {
                // Let the default navigation happen - don't prevent it
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

// Expose to global scope for any external usage
window.authAnimator = authAnimator;