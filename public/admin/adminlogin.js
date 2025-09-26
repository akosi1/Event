// Form submission handling with loading state
document.getElementById('loginForm').addEventListener('submit', function() {
    const button = document.getElementById('loginButton');
    button.classList.add('btn-loading');
    button.disabled = true;
    button.innerHTML = '';
});

// Password toggle functionality
document.getElementById('passwordIcon').addEventListener('click', function() {
    const passwordField = document.getElementById('password');
    const icon = this;
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        icon.classList.remove('fa-lock');
        icon.classList.add('fa-unlock');
    } else {
        passwordField.type = 'password';
        icon.classList.remove('fa-unlock');
        icon.classList.add('fa-lock');
    }
});

// Enhanced interactive effects
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.form-control');
    
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'scale(1.02)';
            this.parentElement.style.transition = 'transform 0.2s ease';
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.style.transform = 'scale(1)';
        });
        
        // Add ripple effect on focus
        input.addEventListener('focus', function() {
            this.style.boxShadow = '0 4px 15px rgba(107, 44, 145, 0.2)';
        });
        
        input.addEventListener('blur', function() {
            this.style.boxShadow = '0 4px 15px rgba(0, 0, 0, 0.1)';
        });
    });
    
    // reCAPTCHA integration
    initializeRecaptcha();
});

// reCAPTCHA initialization function
function initializeRecaptcha() {
    // Check if reCAPTCHA is configured
    if (typeof grecaptcha === 'undefined') {
        console.warn('Google reCAPTCHA is not loaded.');
        return;
    }
    
    // Get the site key from a data attribute or global variable
    const siteKey = window.recaptchaSiteKey;
    
    if (!siteKey) {
        console.warn('Google reCAPTCHA site key is not configured.');
        return;
    }
    
    // Initialize reCAPTCHA v3
    grecaptcha.ready(function() {
        // Add event listener for form submission with reCAPTCHA
        const loginForm = document.getElementById('loginForm');
        
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            grecaptcha.execute(siteKey, {action: 'admin_login'}).then(function(token) {
                // Add the token to the form
                let recaptchaInput = document.getElementById('g-recaptcha-response');
                if (!recaptchaInput) {
                    recaptchaInput = document.createElement('input');
                    recaptchaInput.type = 'hidden';
                    recaptchaInput.name = 'g-recaptcha-response';
                    recaptchaInput.id = 'g-recaptcha-response';
                    loginForm.appendChild(recaptchaInput);
                }
                recaptchaInput.value = token;
                
                // Submit the form
                loginForm.submit();
            });
        });
    });
}

// Error handling and display
function showError(message) {
    const errorAlert = document.getElementById('errorAlert');
    const errorMessages = document.getElementById('errorMessages');
    
    if (errorAlert && errorMessages) {
        errorMessages.innerHTML = message;
        errorAlert.style.display = 'block';
        
        // Auto-hide after 5 seconds
        setTimeout(function() {
            errorAlert.style.display = 'none';
        }, 5000);
    }
}

// Form validation
function validateForm() {
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value.trim();
    
    if (!email || !password) {
        showError('Please fill in all required fields.');
        return false;
    }
    
    // Basic email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        showError('Please enter a valid email address.');
        return false;
    }
    
    return true;
}

// Add form validation before submission
document.getElementById('loginForm').addEventListener('submit', function(e) {
    if (!validateForm()) {
        e.preventDefault();
        
        // Reset button state if validation fails
        const button = document.getElementById('loginButton');
        button.classList.remove('btn-loading');
        button.disabled = false;
        button.innerHTML = 'Sign In';
    }
});