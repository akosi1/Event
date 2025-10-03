<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MCC Event & Portfolio Organizer</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&family=Roboto+Condensed:wght@300;400;700&display=swap" rel="stylesheet">
    
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
                <p>Streamline Your Academic Journey</p>
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
                        <a href="{{ url('/admin') }}" class="btn btn-admin">
                            <i class="fas fa-user-shield"></i>
                            <span>Admin Login</span>
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-user">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>User Login</span>
                        </a>
                    @endauth
                </div>
            @endif
        </div>
    </div>
    
</body>
</html>