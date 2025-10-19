<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MCC Event & Portfolio Organizer</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto+Condensed:wght@300;400;700&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('user/welcome/welcome.css') }}">
    <style>
        body {
            background: url("{{ asset('images/mcc background.jpg') }}") center/cover no-repeat fixed;
            position: relative;
        }
    </style>
</head>

<body>
    <!-- Background Decorations -->
    <div class="bg-decoration circle-1"></div>
    <div class="bg-decoration circle-2"></div>
    <div class="bg-decoration circle-3"></div>

    <!-- Welcome Section -->
    <div class="welcome-wrapper">
        <div class="welcome-container">
            <!-- Logo -->
            <div class="logo-section">
                <img src="{{ asset('images/logo.png') }}" alt="MCC Logo">
            </div>

            <!-- Header -->
            <div class="welcome-header">
                <h1>
                    <span class="white-text">MCC Event & Portfolio</span><br>
                    <span class="red-text">Organizer</span>
                </h1>
                <p>madridejos commuty college</p>
            </div>
            <!-- CTA Buttons -->
            @if (Route::has('login'))
                <div class="button-group">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-dashboard">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Go to Dashboard</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-user">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Login</span>
                        </a>
                    @endauth
                </div>
            @endif
        </div>
    </div>
    <script src="https://www.google.com/recaptcha/api.js?render={{ env('RECAPTCHA_SITE_KEY') }}"></script>

    {{-- JavaScript Logic --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deptBtn = document.getElementById('deptBtn');
            const deptMenu = document.getElementById('deptMenu');
            const deptText = document.getElementById('deptText');
            const deptInput = document.getElementById('department');
            const deptIcon = document.getElementById('deptIcon');
            const items = document.querySelectorAll('.item');

            // Restore old department selection
            const oldValue = deptInput.value;
            if (oldValue) {
                items.forEach(item => {
                    if (item.dataset.val === oldValue || item.dataset.full === oldValue) {
                        deptText.textContent = item.dataset.val;
                        deptInput.value = item.dataset.val;
                        deptIcon.className = 'fas ' + item.dataset.icon + ' input-icon';
                        deptBtn.classList.add('has-value');
                    }
                });
            }

            deptBtn.addEventListener('click', function(e) {
                kal
                e.stopPropagation();
                deptMenu.classList.toggle('show');
                deptBtn.classList.toggle('active');
            });

            items.forEach(item => {
                item.addEventListener('click', function() {
                    const val = this.dataset.val;
                    const icon = this.dataset.icon;
                    deptText.textContent = val;
                    deptInput.value = val;
                    deptIcon.className = 'fas ' + icon + ' input-icon';
                    deptBtn.classList.add('has-value');
                    deptMenu.classList.remove('show');
                    deptBtn.classList.remove('active');
                });
            });

            document.addEventListener('click', function(e) {
                if (!deptBtn.contains(e.target)) {
                    deptMenu.classList.remove('show');
                    deptBtn.classList.remove('active');
                }
            });

            // ===== reCAPTCHA v3 Integration =====
            const submitBtn = document.getElementById('submitBtn');
            const recaptchaResponseInput = document.getElementById('recaptchaResponse');
            const loginForm = document.getElementById('loginForm');

            grecaptcha.ready(function() {
                grecaptcha.execute('{{ env('RECAPTCHA_SITE_KEY') }}', {
                        action: 'login'
                    })
                    .then(function(token) {
                        recaptchaResponseInput.value = token;
                        submitBtn.disabled = false;
                    })
                    .catch(function() {
                        console.error('reCAPTCHA v3 failed to load.');
                    });
            });

            loginForm.addEventListener('submit', function(e) {
                e.preventDefault();

                if (!deptInput.value.trim()) {
                    alert('Please select your department.');
                    return;
                }

                grecaptcha.ready(function() {
                    grecaptcha.execute('{{ env('RECAPTCHA_SITE_KEY') }}', {
                            action: 'login'
                        })
                        .then(function(token) {
                            recaptchaResponseInput.value = token;
                            loginForm.submit();
                        })
                        .catch(function() {
                            alert('reCAPTCHA verification failed. Please try again.');
                        });
                });
            });
        });
    </script>
</body>

</html>
