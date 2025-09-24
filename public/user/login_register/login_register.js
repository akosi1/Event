// Simple Authentication Page Manager
class AuthPageManager {
    constructor() {
        this.init();
    }

    init() {
        this.createParticles();
        this.startParticleSystem();
        this.setupFormAnimations();
        this.setupSelectLabels();
        this.setupFormSubmission();
    }

    createParticles() {
        const particlesContainer = document.getElementById('particles');
        if (!particlesContainer) return;
        
        // Create multiple particles
        for (let i = 0; i < 15; i++) {
            setTimeout(() => {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 8 + 's';
                particlesContainer.appendChild(particle);
                
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
        // Enhanced form input animations
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('focus', (e) => {
                const wrapper = e.target.parentElement;
                if (wrapper.classList.contains('input-wrapper')) {
                    wrapper.style.transform = 'scale(1.02)';
                    wrapper.style.zIndex = '10';
                }
            });

            input.addEventListener('blur', (e) => {
                const wrapper = e.target.parentElement;
                if (wrapper.classList.contains('input-wrapper')) {
                    wrapper.style.transform = 'scale(1)';
                    wrapper.style.zIndex = 'auto';
                }
            });
        });
    }

    setupSelectLabels() {
        // Handle select labels properly
        document.querySelectorAll('.form-select').forEach(select => {
            const label = select.parentElement.querySelector('.select-label');
            
            // Function to update label position
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

            // Update on change
            select.addEventListener('change', updateLabel);
            select.addEventListener('focus', updateLabel);
            
            // Initial update
            updateLabel();
        });
    }

    setupFormSubmission() {
        // Handle form submission UI feedback
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', (e) => {
                const submitBtn = form.querySelector('.btn-submit');
                if (submitBtn) {
                    submitBtn.style.transform = 'translateY(-1px)';
                    
                    // Check if it's login or register form
                    const isLogin = form.action.includes('login');
                    const isRegister = form.action.includes('register');
                    
                    if (isLogin) {
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing In...';
                    } else if (isRegister) {
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account...';
                    } else {
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                    }
                }
            });
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
    const authManager = new AuthPageManager();
    
    // Expose to global scope if needed
    window.authManager = authManager;
});

// Utility function to handle any additional animations
function animateButton(button) {
    button.style.transform = 'scale(0.98)';
    setTimeout(() => {
        button.style.transform = 'scale(1)';
    }, 150);
}

// Handle smooth transitions for navigation
document.addEventListener('click', function(e) {
    if (e.target.matches('a[href*="login"], a[href*="register"]')) {
        const link = e.target;
        animateButton(link);
    }
});

// Smooth page transitions
window.addEventListener('beforeunload', function() {
    document.body.style.opacity = '0.8';
});