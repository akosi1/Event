// Login/Register Animation System with Enhanced Mobile Support
class AuthAnimator {
    constructor() {
        this.isRegisterMode = false;
        this.isAnimating = false;
        this.customSelects = [];
        this.init();
    }

    init() {
        this.initElements();
        this.createParticles();
        this.startParticleSystem();
        this.bindEvents();
        this.setupFormAnimations();
        this.initCustomSelects();
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
            this.currentForm = this.formSection?.querySelector('form');
        }
    }

    // Initialize custom select dropdowns for consistent mobile/desktop UI
    initCustomSelects() {
        const selects = document.querySelectorAll('.form-select');
        selects.forEach(select => {
            this.createCustomSelect(select);
        });
    }

    createCustomSelect(originalSelect) {
        const wrapper = document.createElement('div');
        wrapper.className = 'custom-select';
        
        const trigger = document.createElement('div');
        trigger.className = 'custom-select-trigger';
        trigger.textContent = originalSelect.options[originalSelect.selectedIndex].text || 'Select an option';
        
        const optionsContainer = document.createElement('div');
        optionsContainer.className = 'custom-select-options';
        
        // Create custom options
        Array.from(originalSelect.options).forEach((option, index) => {
            if (option.value === '') return; // Skip placeholder option
            
            const customOption = document.createElement('div');
            customOption.className = 'custom-select-option';
            customOption.textContent = option.text;
            customOption.dataset.value = option.value;
            
            if (option.selected) {
                customOption.classList.add('selected');
                trigger.textContent = option.text;
                trigger.classList.add('has-value');
            }
            
            customOption.addEventListener('click', () => {
                this.selectCustomOption(originalSelect, customOption, trigger, optionsContainer);
            });
            
            optionsContainer.appendChild(customOption);
        });
        
        // Toggle dropdown
        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            this.toggleCustomSelect(trigger, optionsContainer);
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!wrapper.contains(e.target)) {
                this.closeCustomSelect(trigger, optionsContainer);
            }
        });
        
        // Keyboard navigation
        trigger.addEventListener('keydown', (e) => {
            this.handleCustomSelectKeydown(e, optionsContainer, originalSelect);
        });
        
        wrapper.appendChild(trigger);
        wrapper.appendChild(optionsContainer);
        
        // Hide original select and insert custom one
        originalSelect.style.display = 'none';
        originalSelect.parentNode.insertBefore(wrapper, originalSelect);
        
        // Store reference for cleanup
        this.customSelects.push({
            original: originalSelect,
            custom: wrapper,
            trigger: trigger,
            options: optionsContainer
        });
    }

    selectCustomOption(originalSelect, customOption, trigger, optionsContainer) {
        // Update original select
        const value = customOption.dataset.value;
        originalSelect.value = value;
        
        // Update custom select
        trigger.textContent = customOption.textContent;
        trigger.classList.add('has-value');
        
        // Update selected state
        optionsContainer.querySelectorAll('.custom-select-option').forEach(opt => {
            opt.classList.remove('selected');
        });
        customOption.classList.add('selected');
        
        // Close dropdown
        this.closeCustomSelect(trigger, optionsContainer);
        
        // Trigger change event
        originalSelect.dispatchEvent(new Event('change', { bubbles: true }));
        
        // Update label position
        const label = originalSelect.parentNode.querySelector('.select-label');
        if (label) {
            label.style.top = '-8px';
            label.style.left = '8px';
            label.style.fontSize = '11px';
            label.style.color = '#dc2626';
            label.style.background = 'rgba(45, 21, 21, 0.9)';
            label.style.padding = '0 4px';
            label.style.borderRadius = '4px';
        }
    }

    toggleCustomSelect(trigger, optionsContainer) {
        const isOpen = optionsContainer.classList.contains('open');
        
        // Close all other custom selects
        this.customSelects.forEach(select => {
            if (select.options !== optionsContainer) {
                this.closeCustomSelect(select.trigger, select.options);
            }
        });
        
        if (isOpen) {
            this.closeCustomSelect(trigger, optionsContainer);
        } else {
            this.openCustomSelect(trigger, optionsContainer);
        }
    }

    openCustomSelect(trigger, optionsContainer) {
        trigger.classList.add('open');
        optionsContainer.classList.add('open');
        
        // Focus first option
        const firstOption = optionsContainer.querySelector('.custom-select-option');
        if (firstOption) {
            firstOption.classList.add('highlighted');
        }
    }

    closeCustomSelect(trigger, optionsContainer) {
        trigger.classList.remove('open');
        optionsContainer.classList.remove('open');
        
        // Remove highlights
        optionsContainer.querySelectorAll('.custom-select-option').forEach(opt => {
            opt.classList.remove('highlighted');
        });
    }

    handleCustomSelectKeydown(e, optionsContainer, originalSelect) {
        const options = optionsContainer.querySelectorAll('.custom-select-option');
        const highlighted = optionsContainer.querySelector('.custom-select-option.highlighted');
        let currentIndex = Array.from(options).indexOf(highlighted);

        switch (e.key) {
            case 'Enter':
            case ' ':
                e.preventDefault();
                if (highlighted) {
                    highlighted.click();
                } else {
                    this.toggleCustomSelect(e.target, optionsContainer);
                }
                break;
            case 'ArrowDown':
                e.preventDefault();
                currentIndex = currentIndex < options.length - 1 ? currentIndex + 1 : 0;
                this.highlightOption(options, currentIndex);
                break;
            case 'ArrowUp':
                e.preventDefault();
                currentIndex = currentIndex > 0 ? currentIndex - 1 : options.length - 1;
                this.highlightOption(options, currentIndex);
                break;
            case 'Escape':
                this.closeCustomSelect(e.target, optionsContainer);
                break;
        }
    }

    highlightOption(options, index) {
        options.forEach((opt, i) => {
            opt.classList.toggle('highlighted', i === index);
        });
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

        // Handle form transition
        this.transitionToLoginForm();

        setTimeout(() => {
            this.isAnimating = false;
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

    transitionToRegisterForm() {
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
        const formTitle = this.formSection?.querySelector('.form-title');
        const form = this.formSection?.querySelector('form');
        
        if (!form) return;
        
        // Update title
        if (formTitle) {
            formTitle.textContent = 'Sign Up';
        }
        
        // Update form action and method
        form.action = '/register';
        form.method = 'POST';
        
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
            
            // Re-initialize animations and custom selects
            this.setupFormAnimations();
            this.initCustomSelects();
        }, 250);
    }

    createLoginFormContent() {
        const form = this.formSection?.querySelector('form');
        if (!form) return;
        
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
            
            // Re-initialize animations and custom selects
            this.setupFormAnimations();
            this.initCustomSelects();
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
            input.addEventListener('focus', this.inputFocusHandler.bind(this));
            input.addEventListener('blur', this.inputBlurHandler.bind(this));
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
        
        // Cleanup custom selects
        this.customSelects.forEach(select => {
            if (select.custom && select.custom.parentNode) {
                select.custom.parentNode.removeChild(select.custom);
                select.original.style.display = '';
            }
        });
        this.customSelects = [];
        
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