// Fixed Login/Register Animation System - Production Ready
class AuthAnimator {
    constructor() {
        this.init();
    }

    init() {
        this.initElements();
        this.createParticles();
        this.startParticleSystem();
        this.setupFormAnimations();
        this.bindFormEvents();
    }

    initElements() {
        this.particlesContainer = document.getElementById('particles');
    }

    createParticles() {
        if (!this.particlesContainer) return;
        
        // Create floating particles
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

    setupFormAnimations() {
        // Enhanced form animations
        document.querySelectorAll('input, select').forEach(input => {
            input.addEventListener('focus', this.inputFocusHandler.bind(this));
            input.addEventListener('blur', this.inputBlurHandler.bind(this));
            
            // Handle label animations for inputs with values
            if (input.value) {
                this.activateLabel(input);
            }
        });

        // Setup select labels
        document.querySelectorAll('.form-select').forEach(select => {
            const label = select.parentElement.querySelector('.select-label');
            if (!label) return;
            
            const updateLabel = () => {
                if (select.value && select.value !== '') {
                    this.activateLabel(select);
                } else {
                    this.deactivateLabel(select);
                }
            };

            select.addEventListener('change', updateLabel);
            select.addEventListener('focus', updateLabel);
            updateLabel(); // Initial state
        });

        // Setup input labels
        document.querySelectorAll('.form-control').forEach(input => {
            const label = input.parentElement.querySelector('.input-label');
            if (!label) return;

            const updateLabel = () => {
                if (input.value && input.value !== '') {
                    this.activateLabel(input);
                } else {
                    this.deactivateLabel(input);
                }
            };

            input.addEventListener('input', updateLabel);
            input.addEventListener('change', updateLabel);
            updateLabel(); // Initial state
        });
    }

    activateLabel(element) {
        const label = element.parentElement.querySelector('.input-label, .select-label');
        if (label) {
            label.style.top = '-8px';
            label.style.left = '8px';
            label.style.fontSize = '11px';
            label.style.color = '#dc2626';
            label.style.background = 'rgba(45, 21, 21, 0.9)';
            label.style.padding = '0 4px';
            label.style.borderRadius = '4px';
            label.style.transform = 'translateY(0)';
        }
    }

    deactivateLabel(element) {
        const label = element.parentElement.querySelector('.input-label, .select-label');
        if (label) {
            label.style.top = '12px';
            label.style.left = '12px';
            label.style.fontSize = '13px';
            label.style.color = 'rgba(255, 255, 255, 0.6)';
            label.style.background = 'transparent';
            label.style.padding = '0';
            label.style.transform = 'translateY(0)';
        }
    }

    inputFocusHandler(e) {
        const wrapper = e.target.parentElement;
        if (wrapper.classList.contains('input-wrapper')) {
            wrapper.style.transform = 'scale(1.02)';
            wrapper.style.zIndex = '10';
        }
        this.activateLabel(e.target);
    }

    inputBlurHandler(e) {
        const wrapper = e.target.parentElement;
        if (wrapper.classList.contains('input-wrapper')) {
            wrapper.style.transform = 'scale(1)';
            wrapper.style.zIndex = 'auto';
        }
        
        if (!e.target.value) {
            this.deactivateLabel(e.target);
        }
    }

    bindFormEvents() {
        // Handle form submissions with loading states
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (!form.matches('form')) return;

            const submitBtn = form.querySelector('.btn-submit');
            if (!submitBtn) return;

            // Prevent double submission
            if (submitBtn.disabled) {
                e.preventDefault();
                return;
            }

            submitBtn.disabled = true;
            submitBtn.style.transform = 'translateY(-1px)';
            
            // Update button text based on form type
            const originalText = submitBtn.innerHTML;
            const isLogin = form.action.includes('login') || form.id === 'login-form';
            const isRegister = form.action.includes('register') || form.id === 'register-form';
            const isOtp = form.action.includes('otp') || form.id === 'otp-form';
            
            if (isLogin) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing In...';
            } else if (isRegister) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account...';
            } else if (isOtp) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
            } else {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            }

            // Re-enable button after 30 seconds as failsafe
            setTimeout(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                submitBtn.style.transform = 'translateY(0)';
            }, 30000);
        });

        // Handle input validation feedback
        document.addEventListener('input', (e) => {
            const input = e.target;
            if (!input.matches('input, select')) return;

            const wrapper = input.closest('.input-wrapper');
            if (!wrapper) return;

            // Remove error styling on input
            wrapper.classList.remove('error');
            const errorMsg = wrapper.parentElement.querySelector('.error-msg');
            if (errorMsg) {
                errorMsg.style.opacity = '0';
            }
        });
    }

    // Cleanup method
    destroy() {
        if (this.particleInterval) {
            clearInterval(this.particleInterval);
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    window.authAnimator = new AuthAnimator();
    
    // Focus first input
    const firstInput = document.querySelector('input:not([type="hidden"])');
    if (firstInput) {
        setTimeout(() => firstInput.focus(), 100);
    }
});

// Handle page unload
window.addEventListener('beforeunload', function() {
    if (window.authAnimator) {
        window.authAnimator.destroy();
    }
});