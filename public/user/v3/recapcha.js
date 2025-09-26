// reCAPTCHA v3 Implementation for Login/Register Forms
document.addEventListener('DOMContentLoaded', function() {
    const siteKey = document.querySelector('meta[name="recaptcha-key"]')?.getAttribute('content');
    
    if (!siteKey) {
        console.warn('reCAPTCHA site key not found in meta tag');
        return;
    }

    // Initialize reCAPTCHA when script is loaded
    if (typeof grecaptcha !== 'undefined') {
        initRecaptcha();
    } else {
        // Wait for reCAPTCHA script to load
        window.addEventListener('load', initRecaptcha);
    }

    function initRecaptcha() {
        if (typeof grecaptcha === 'undefined') {
            console.error('reCAPTCHA library not loaded');
            return;
        }

        grecaptcha.ready(function() {
            console.log('reCAPTCHA ready');
            
            // Handle login form
            const loginForm = document.getElementById('login-form');
            if (loginForm) {
                setupFormRecaptcha(loginForm, 'login');
            }

            // Handle register form
            const registerForm = document.getElementById('register-form');
            if (registerForm) {
                setupFormRecaptcha(registerForm, 'register');
            }
        });
    }

    function setupFormRecaptcha(form, action) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitButton = form.querySelector('.btn-submit');
            const originalText = submitButton.innerHTML;
            
            // Show loading state
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
            
            grecaptcha.execute(siteKey, { action: action })
                .then(function(token) {
                    // Set the token in the hidden input
                    const tokenInput = form.querySelector('input[name="g-recaptcha-response"]');
                    if (tokenInput) {
                        tokenInput.value = token;
                    }
                    
                    // Update button text for actual submission
                    if (action === 'login') {
                        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing In...';
                    } else if (action === 'register') {
                        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account...';
                    }
                    
                    // Submit the form
                    form.submit();
                })
                .catch(function(error) {
                    console.error('reCAPTCHA error:', error);
                    
                    // Reset button
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;
                    
                    // Show error message
                    showRecaptchaError('reCAPTCHA verification failed. Please try again.');
                });
        });
    }

    function showRecaptchaError(message) {
        // Remove existing error
        const existingError = document.querySelector('.recaptcha-error');
        if (existingError) {
            existingError.remove();
        }

        // Create error element
        const errorDiv = document.createElement('div');
        errorDiv.className = 'recaptcha-error error-msg';
        errorDiv.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${message}`;
        
        // Add to form
        const submitButton = document.querySelector('.btn-submit');
        if (submitButton) {
            submitButton.parentNode.insertBefore(errorDiv, submitButton);
        }

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (errorDiv.parentNode) {
                errorDiv.remove();
            }
        }, 5000);
    }

    // Handle network errors
    window.addEventListener('online', function() {
        const error = document.querySelector('.recaptcha-error');
        if (error) {
            error.remove();
        }
    });

    window.addEventListener('offline', function() {
        showRecaptchaError('Network connection lost. Please check your internet connection.');
    });
});