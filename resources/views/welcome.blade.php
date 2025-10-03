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
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            overflow: hidden;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: url("{{ asset('images/mcc background.jpg') }}") center/cover no-repeat fixed;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(74, 26, 92, 0.85) 0%, rgba(107, 44, 145, 0.85) 50%, rgba(61, 26, 120, 0.85) 100%);
            z-index: 1;
        }

        .welcome-wrapper {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            z-index: 2;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .welcome-container {
            text-align: center;
            max-width: 900px;
            width: 100%;
            animation: fadeIn 0.8s ease-out;
            padding: 20px 0;
        }

        @keyframes fadeIn {
            from { 
                opacity: 0; 
                transform: translateY(30px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }

        .logo-section {
            margin-bottom: 50px;
            animation: fadeIn 1s ease-out 0.2s backwards;
        }

        .logo-section img {
            width: 150px;
            height: auto;
            margin-bottom: 20px;
            filter: drop-shadow(0 4px 30px rgba(0, 0, 0, 0.4));
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { 
                transform: scale(1); 
                opacity: 1;
            }
            50% { 
                transform: scale(1.08); 
                opacity: 0.9;
            }
        }

        .welcome-header {
            margin-bottom: 70px;
            animation: fadeIn 1s ease-out 0.4s backwards;
        }

        .welcome-header h1 {
            font-size: clamp(28px, 6vw, 64px);
            font-weight: 700;
            margin-bottom: 20px;
            letter-spacing: 2px;
            font-family: 'Oswald', sans-serif;
            text-transform: uppercase;
            text-shadow: 4px 4px 8px rgba(0, 0, 0, 0.6);
            line-height: 1.2;
        }

        .welcome-header h1 .white-text {
            color: #ffffff;
        }

        .welcome-header h1 .red-text {
            color: #e85d5d;
            display: inline;
        }

        .welcome-header p {
            font-size: clamp(14px, 3vw, 20px);
            color: #e0e0e0;
            font-weight: 400;
            font-family: 'Roboto Condensed', sans-serif;
            text-transform: uppercase;
            letter-spacing: clamp(1.5px, 0.5vw, 3px);
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            margin-top: 20px;
        }

        .button-group {
            display: flex;
            gap: 25px;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeIn 1s ease-out 0.6s backwards;
        }

        .btn {
            padding: 18px 50px;
            border-radius: 0;
            font-size: 17px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 14px;
            transition: all 0.4s ease;
            font-family: 'Oswald', sans-serif;
            text-transform: uppercase;
            letter-spacing: 2px;
            cursor: pointer;
            border: none;
            min-width: 220px;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #e53e3e 0%, #d53f41 100%);
            transition: left 0.4s ease;
            z-index: -1;
        }

        .btn-dashboard {
            background: linear-gradient(135deg, #e53e3e 0%, #d53f41 100%);
            color: white;
            box-shadow: 0 4px 20px rgba(229, 62, 62, 0.4);
        }

        .btn-dashboard:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(229, 62, 62, 0.6);
        }

        .btn-admin {
            background: transparent;
            color: #ffffff;
            border: 2px solid rgba(255, 255, 255, 0.7);
            box-shadow: 0 4px 20px rgba(255, 255, 255, 0.1);
            z-index: 1;
        }

        .btn-admin::before {
            z-index: -1;
        }

        .btn-admin:hover {
            color: #ffffff;
            border-color: #e53e3e;
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(229, 62, 62, 0.4);
        }

        .btn-admin:hover::before {
            left: 0;
        }

        .btn-user {
            background: transparent;
            color: #ffffff;
            border: 2px solid rgba(255, 255, 255, 0.7);
            box-shadow: 0 4px 20px rgba(255, 255, 255, 0.1);
            z-index: 1;
        }

        .btn-user::before {
            z-index: -1;
        }

        .btn-user:hover {
            color: #ffffff;
            border-color: #e53e3e;
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(229, 62, 62, 0.4);
        }

        .btn-user:hover::before {
            left: 0;
        }

        .btn:active {
            transform: translateY(-1px);
        }

        .btn i {
            font-size: 20px;
        }

        /* Floating Background Elements */
        .bg-decoration {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            animation: float 6s ease-in-out infinite;
            pointer-events: none;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .bg-decoration.circle-1 {
            width: 350px;
            height: 350px;
            top: 10%;
            left: 5%;
            animation-delay: 0s;
        }

        .bg-decoration.circle-2 {
            width: 250px;
            height: 250px;
            bottom: 15%;
            right: 10%;
            animation-delay: -2s;
        }

        .bg-decoration.circle-3 {
            width: 180px;
            height: 180px;
            top: 60%;
            left: 15%;
            animation-delay: -4s;
        }

        /* Tablet Responsive */
        @media (max-width: 768px) {
            .welcome-wrapper {
                padding: 15px;
            }

            .welcome-container {
                max-width: 95%;
                padding: 15px 0;
            }

            .logo-section {
                margin-bottom: 35px;
            }

            .logo-section img {
                width: 110px;
            }

            .welcome-header {
                margin-bottom: 45px;
            }

            .welcome-header h1 {
                margin-bottom: 15px;
            }

            .welcome-header p {
                margin-top: 15px;
            }

            .button-group {
                gap: 18px;
            }

            .btn {
                padding: 16px 40px;
                font-size: 16px;
                min-width: 200px;
                letter-spacing: 1.5px;
            }

            .btn i {
                font-size: 18px;
            }

            .bg-decoration.circle-1 {
                width: 250px;
                height: 250px;
            }

            .bg-decoration.circle-2 {
                width: 180px;
                height: 180px;
            }

            .bg-decoration.circle-3 {
                width: 130px;
                height: 130px;
            }
        }

        /* Mobile Responsive */
        @media (max-width: 480px) {
            body {
                overflow-y: auto;
            }

            .welcome-wrapper {
                padding: 20px 15px;
                min-height: 100vh;
                height: auto;
            }

            .welcome-container {
                max-width: 100%;
                padding: 30px 0;
            }

            .logo-section {
                margin-bottom: 30px;
            }

            .logo-section img {
                width: 90px;
            }

            .welcome-header {
                margin-bottom: 35px;
            }

            .welcome-header h1 {
                margin-bottom: 12px;
            }

            .welcome-header h1 br {
                display: block;
            }

            .welcome-header p {
                margin-top: 12px;
            }

            .button-group {
                flex-direction: column;
                gap: 15px;
                width: 100%;
                align-items: center;
            }

            .btn {
                width: 100%;
                max-width: 320px;
                padding: 16px 35px;
                font-size: 15px;
                letter-spacing: 1.5px;
                min-width: unset;
            }

            .btn:active {
                transform: translateY(-3px);
            }

            .btn i {
                font-size: 17px;
            }

            .bg-decoration.circle-1 {
                width: 200px;
                height: 200px;
                top: 5%;
                left: -70px;
            }

            .bg-decoration.circle-2 {
                width: 150px;
                height: 150px;
                bottom: 10%;
                right: -60px;
            }

            .bg-decoration.circle-3 {
                width: 110px;
                height: 110px;
                top: 50%;
                left: -40px;
            }
        }

        /* Extra Small Mobile */
        @media (max-width: 360px) {
            .welcome-wrapper {
                padding: 15px 10px;
            }

            .welcome-container {
                padding: 20px 0;
            }

            .logo-section {
                margin-bottom: 25px;
            }

            .logo-section img {
                width: 75px;
            }

            .welcome-header {
                margin-bottom: 30px;
            }

            .btn {
                padding: 14px 30px;
                font-size: 14px;
                max-width: 280px;
                gap: 10px;
            }

            .btn i {
                font-size: 16px;
            }

            .bg-decoration.circle-1 {
                width: 160px;
                height: 160px;
            }

            .bg-decoration.circle-2 {
                width: 120px;
                height: 120px;
            }

            .bg-decoration.circle-3 {
                width: 90px;
                height: 90px;
            }
        }

        /* Landscape Mobile */
        @media (max-height: 600px) and (orientation: landscape) {
            .welcome-wrapper {
                padding: 15px;
                overflow-y: auto;
            }

            .logo-section {
                margin-bottom: 20px;
            }

            .logo-section img {
                width: 70px;
            }

            .welcome-header {
                margin-bottom: 25px;
            }

            .welcome-header h1 {
                font-size: clamp(24px, 5vw, 48px);
            }

            .button-group {
                gap: 12px;
            }

            .btn {
                padding: 12px 35px;
                font-size: 14px;
            }
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