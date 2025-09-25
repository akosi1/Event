// Fixed recapcha.js - Corrects token name mismatch

document.addEventListener("DOMContentLoaded", function () {
    const siteKey = document.querySelector('meta[name="recaptcha-key"]')?.content;

    if (!siteKey) {
        console.error("reCAPTCHA site key not found in meta tag.");
        return;
    }

    console.log('reCAPTCHA site key found:', siteKey);

    // Function to generate and inject token
    function generateRecaptchaToken(action = 'homepage') {
        grecaptcha.ready(function () {
            grecaptcha.execute(siteKey, { action: action })
                .then(function (token) {
                    console.log('reCAPTCHA token generated for', action, ':', token);

                    // Inject token into the correct input field name
                    const input = document.querySelector('input[name="g-recaptcha-response"]');
                    if (input) {
                        input.value = token;
                        console.log('Token injected into g-recaptcha-response field');
                    } else {
                        console.warn('g-recaptcha-response input field not found');
                    }
                })
                .catch(function (error) {
                    console.error("reCAPTCHA error:", error);
                });
        });
    }

    // Generate initial token
    generateRecaptchaToken('homepage');

    // Re-generate token before form submission
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (form.querySelector('input[name="g-recaptcha-response"]')) {
            e.preventDefault(); // Prevent initial submission
            
            let action = 'homepage';
            if (form.id === 'login-form') {
                action = 'login';
            } else if (form.id === 'register-form') {
                action = 'register';
            }
            
            console.log('Generating fresh token for form submission:', action);
            
            grecaptcha.ready(function () {
                grecaptcha.execute(siteKey, { action: action })
                    .then(function (token) {
                        const input = form.querySelector('input[name="g-recaptcha-response"]');
                        if (input) {
                            input.value = token;
                            console.log('Fresh token generated and injected');
                            form.submit(); // Now submit the form
                        }
                    })
                    .catch(function (error) {
                        console.error("reCAPTCHA error during form submission:", error);
                        form.submit(); // Submit anyway if reCAPTCHA fails
                    });
            });
        }
    });

    // Refresh token every 2 minutes (tokens expire after 2 minutes)
    setInterval(function() {
        generateRecaptchaToken('refresh');
    }, 110000); // 1 minute 50 seconds
});