// Auth Animator Class for handling login/register transitions and mobile interactions
class AuthAnimator {
    constructor() {
        this.isRegisterMode = false;
        this.isMobile = window.innerWidth <= 768;
        this.init();
    }

    init() {
        this.createParticles();
        this.setupEventListeners();
        this.setupDepartmentModal();
        this.handleResize();
        
        // Check if we're in register mode based on current page
        const container = document.querySelector('.auth-container');
        if (container && container.classList.contains('register-mode')) {
            this.isRegisterMode = true;
        }
    }

    createParticles() {
        const particleContainer = document.getElementById('particles');
        if (!particleContainer) return;

        particleContainer.innerHTML = '';
        const particleCount = this.isMobile ? 15 : 25;

        for (let i = 0; i < particleCount; i++) {
            const particle = document.createElement('div');
            particle.classList.add('particle');
            
            const size = Math.random() * 6 + 4;
            const left = Math.random() * 100;
            const animationDelay = Math.random() * 6;
            const animationDuration = Math.random() * 4 + 4;
            
            particle.style.cssText = `
                width: ${size}px;
                height: ${size}px;
                left: ${left}%;
                animation-delay: ${animationDelay}s;
                animation-duration: ${animationDuration}s;
            `;
            
            particleContainer.appendChild(particle);
        }
    }

    setupEventListeners() {
        // Handle window resize
        window.addEventListener('resize', () => this.handleResize());
        
        // Handle form input animations
        this.setupInputAnimations();
        
        // Handle form submissions
        this.setupFormValidation();
    }

    setupInputAnimations() {
        const inputs = document.querySelectorAll('.form-control, .form-select');
        
        inputs.forEach(input => {
            input.addEventListener('focus', this.handleInputFocus.bind(this));
            input.addEventListener('blur', this.handleInputBlur.bind(this));
            input.addEventListener('input', this.handleInputChange.bind(this));
        });
    }

    handleInputFocus(e) {
        const wrapper = e.target.closest('.input-wrapper');
        if (wrapper) {
            wrapper.style.transform = 'translateY(-2px)';
        }
    }

    handleInputBlur(e) {
        const wrapper = e.target.closest('.input-wrapper');
        if (wrapper) {
            wrapper.style.transform = 'translateY(0)';
        }
    }

    handleInputChange(e) {
        const input = e.target;
        if (input.value.trim()) {
            input.style.borderColor = '#28a745';
        } else {
            input.style.borderColor = '#e0e6ed';
        }
    }

    setupFormValidation() {
        const forms = document.querySelectorAll('form');
        
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                    this.showFormErrors(form);
                }
            });
        });
    }

    validateForm(form) {
        const requiredInputs = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredInputs.forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
                input.style.borderColor = '#dc3545';
                input.style.boxShadow = '0 0 0 2px rgba(220, 53, 69, 0.1)';
            }
        });
        
        return isValid;
    }

    showFormErrors(form) {
        const submitBtn = form.querySelector('.btn-submit');
        submitBtn.style.animation = 'shake 0.5s ease-in-out';
        
        setTimeout(() => {
            submitBtn.style.animation = '';
        }, 500);
    }

    setupDepartmentModal() {
        if (this.isMobile) {
            this.createDepartmentModal();
        }
    }

    createDepartmentModal() {
        const departmentSelect = document.getElementById('department');
        if (!departmentSelect) return;

        // Create modal HTML
        const modal = document.createElement('div');
        modal.className = 'department-modal';
        modal.innerHTML = `
            <div class="department-content">
                <button class="department-close" onclick="authAnimator.closeDepartmentModal()">&times;</button>
                <h3 style="margin-bottom: 20px; text-align: center;">Select Department</h3>
                <div class="department-options">
                    <div class="department-option" data-value="BSIT">
                        <input type="radio" name="dept" value="BSIT" id="dept-bsit">
                        <label for="dept-bsit">BSIT</label>
                    </div>
                    <div class="department-option" data-value="BSBA">
                        <input type="radio" name="dept" value="BSBA" id="dept-bsba">
                        <label for="dept-bsba">BSBA</label>
                    </div>
                    <div class="department-option" data-value="BSED">
                        <input type="radio" name="dept" value="BSED" id="dept-bsed">
                        <label for="dept-bsed">BSED</label>
                    </div>
                    <div class="department-option" data-value="BEED">
                        <input type="radio" name="dept" value="BEED" id="dept-beed">
                        <label for="dept-beed">BEED</label>
                    </div>
                    <div class="department-option" data-value="BSHM">
                        <input type="radio" name="dept" value="BSHM" id="dept-bshm">
                        <label for="dept-bshm">BSHM</label>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        // Hide original select on mobile
        departmentSelect.style.display = 'none';

        // Create custom select button
        const customSelect = document.createElement('div');
        customSelect.className = 'input-wrapper';
        customSelect.innerHTML = `
            <button type="button" class="form-control department-trigger" id="department-trigger">
                <span class="department-text">Select Your Department</span>
            </button>
            <label class="input-label">Select Your Department</label>
            <i class="fas fa-graduation-cap input-icon"></i>
        `;

        departmentSelect.parentNode.insertBefore(customSelect, departmentSelect);

        // Setup modal interactions
        this.setupModalInteractions(modal, departmentSelect);
    }

    setupModalInteractions(modal, originalSelect) {
        const trigger = document.getElementById('department-trigger');
        const options = modal.querySelectorAll('.department-option');
        
        trigger.addEventListener('click', () => {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        });

        options.forEach(option => {
            option.addEventListener('click', () => {
                const value = option.dataset.value;
                const radio = option.querySelector('input[type="radio"]');
                
                // Clear previous selections
                options.forEach(opt => opt.classList.remove('selected'));
                
                // Select current option
                option.classList.add('selected');
                radio.checked = true;
                
                // Update original select and trigger
                originalSelect.value = value;
                const triggerText = trigger.querySelector('.department-text');
                triggerText.textContent = value;
                trigger.classList.add('selected');
                
                // Close modal
                this.closeDepartmentModal();
            });
        });

        // Close modal on background click
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                this.closeDepartmentModal();
            }
        });
    }

    closeDepartmentModal() {
        const modal = document.querySelector('.department-modal');
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    handleResize() {
        const wasMobile = this.isMobile;
        this.isMobile = window.innerWidth <= 768;
        
        if (wasMP !== this.isMobile) {
            this.createParticles();
            
            if (this.isMobile && !document.querySelector('.department-modal')) {
                this.setupDepartmentModal();
            }
        }
    }

    switchToRegister() {
        if (this.isMobile) {
            // For mobile, just redirect to register page
            window.location.href = '/register';
            return;
        }

        this.isRegisterMode = true;
        const container = document.querySelector('.auth-container');
        const welcomeTitle = document.getElementById('welcomeTitle');
        const welcomeText = document.getElementById('welcomeText');
        const formTitle = document.querySelector('.form-title');
        
        // Add register mode class
        container.classList.add('register-mode');
        container.querySelector('.diagonal-section').classList.add('register-mode');
        container.querySelector('.welcome-section').classList.add('register-mode');
        
        // Update welcome text
        if (welcomeTitle) welcomeTitle.textContent = 'WELCOME!';
        if (welcomeText) welcomeText.textContent = 'Create your account';
        if (formTitle) formTitle.textContent = 'Sign Up';
        
        // Animate form change
        this.animateFormTransition(() => {
            // Load register form content here
            window.location.href = '/register';
        });
    }

    switchToLogin() {
        if (this.isMobile) {
            // For mobile, just redirect to login page
            window.location.href = '/login';
            return;
        }

        this.isRegisterMode = false;
        const container = document.querySelector('.auth-container');
        const welcomeTitle = document.getElementById('welcomeTitle');
        const welcomeText = document.getElementById('welcomeText');
        const formTitle = document.querySelector('.form-title');
        
        // Remove register mode class
        container.classList.remove('register-mode');
        container.querySelector('.diagonal-section').classList.remove('register-mode');
        container.querySelector('.welcome-section').classList.remove('register-mode');
        
        // Update welcome text
        if (welcomeTitle) welcomeTitle.textContent = 'WELCOME BACK!';
        if (welcomeText) welcomeText.textContent = 'Please sign in to continue';
        if (formTitle) formTitle.textContent = 'Sign In';
        
        // Animate form change
        this.animateFormTransition(() => {
            // Load login form content here
            window.location.href = '/login';
        });
    }

    animateFormTransition(callback) {
        const formSection = document.querySelector('.form-section');
        
        formSection.style.transform = 'translateX(-100px)';
        formSection.style.opacity = '0';
        
        setTimeout(() => {
            callback();
            
            formSection.style.transform = 'translateX(0)';
            formSection.style.opacity = '1';
        }, 300);
    }

    // Utility method to show loading state
    showLoading(button) {
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
        button.disabled = true;
        
        return () => {
            button.innerHTML = originalText;
            button.disabled = false;
        };
    }

    // Method to show success/error messages
    showMessage(message, type = 'success') {
        const messageDiv = document.createElement('div');
        messageDiv.className = `auth-message ${type}`;
        messageDiv.textContent = message;
        messageDiv.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            z-index: 9999;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            background: ${type === 'success' ? '#28a745' : '#dc3545'};
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        `;
        
        document.body.appendChild(messageDiv);
        
        setTimeout(() => {
            messageDiv.style.transform = 'translateX(0)';
        }, 100);
        
        setTimeout(() => {
            messageDiv.style.transform = 'translateX(100%)';
            setTimeout(() => {
                document.body.removeChild(messageDiv);
            }, 300);
        }, 3000);
    }
}

// CSS Animation for form shake effect
const shakeKeyframes = `
    @keyframes shake {
        0%, 20%, 40%, 60%, 80%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    }
`;

// Add shake animation to document
const style = document.createElement('style');
style.textContent = shakeKeyframes;
document.head.appendChild(style);

// Initialize AuthAnimator when DOM is loaded
let authAnimator;

document.addEventListener('DOMContentLoaded', function() {
    authAnimator = new AuthAnimator();
    
    // Handle department selection for non-mobile
    const departmentSelect = document.getElementById('department');
    if (departmentSelect && !authAnimator.isMobile) {
        departmentSelect.addEventListener('change', function() {
            if (this.value) {
                this.style.borderColor = '#28a745';
                const label = this.parentNode.querySelector('.select-label');
                if (label) {
                    label.style.color = '#28a745';
                }
            }
        });
    }
    
    // Handle form submission with loading states
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('.btn-submit');
            if (submitBtn && authAnimator.validateForm(form)) {
                const resetLoading = authAnimator.showLoading(submitBtn);
                
                // Reset loading state if form submission fails
                setTimeout(() => {
                    resetLoading();
                }, 5000);
            }
        });
    });
    
    // Add smooth scroll behavior
    document.documentElement.style.scrollBehavior = 'smooth';
    
    // Handle touch events for mobile
    if (authAnimator.isMobile) {
        document.addEventListener('touchstart', function() {}, { passive: true });
    }
    
    // Preload images and resources
    const preloadImages = [
        // Add any background images you want to preload
    ];
    
    preloadImages.forEach(src => {
        const img = new Image();
        img.src = src;
    });
});

// Handle page visibility changes
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        // Page is hidden, pause animations if needed
        const particles = document.querySelectorAll('.particle');
        particles.forEach(particle => {
            particle.style.animationPlayState = 'paused';
        });
    } else {
        // Page is visible, resume animations
        const particles = document.querySelectorAll('.particle');
        particles.forEach(particle => {
            particle.style.animationPlayState = 'running';
        });
    }
});

// Export for global access
window.authAnimator = authAnimator;