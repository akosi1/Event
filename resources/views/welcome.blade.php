<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MCC Event & Portfolio Organizer</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (required for live mode) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            /* Split background: deep maroon (not black) + red */
            background: linear-gradient(90deg, #2a0a0a 50%, #c00000 50%);
            font-family: 'Barlow', sans-serif;
            padding: 20px;
        }

        .welcome-container {
            background: rgba(15, 8, 8, 0.85);
            padding: 60px 40px;
            text-align: center;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .logo-container {
            max-width: 250px;
            margin: 0 auto 30px;
        }

        .logo {
            width: 100%;
            height: auto;
            max-height: 120px;
            object-fit: contain;
        }

        .main-title {
            color: white;
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: 1px;
            line-height: 1.2;
        }

        .highlight-text {
            color: #ff4d4d;
            display: block;
            margin-top: 8px;
        }

        .btn {
            color: white;
            background: #d00000;
            border: none;
            padding: 16px 45px;
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-decoration: none;
            border-radius: 0; /* Rectangular */
            position: relative;
            display: inline-block;
            font-family: 'Barlow', sans-serif;
            box-shadow: 0 4px 10px rgba(150, 0, 0, 0.4);
        }

        .btn:hover {
            background: #ff0000;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(180, 0, 0, 0.5);
        }

        /* Underline animation on hover */
        .btn::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: white;
            transition: width 0.3s ease;
        }

        .btn:hover::after {
            width: 100%;
        }

        @media (max-width: 600px) {
            .main-title {
                font-size: 2.2rem;
            }
            .btn {
                width: 100%;
                max-width: 250px;
                padding: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <!-- Logo -->
        <div class="logo-container">
            <img src="{{ asset('images/logo.png') }}" alt="MCC Logo" class="logo">
        </div>

        <!-- Title -->
        <h1 class="main-title">
            MCC Event & Portfolio
            <span class="highlight-text">Organizer</span>
        </h1>

        <!-- Single "Get Started" Button -->
        <a href="{{ route('dashboard') }}" class="btn">Get Started</a>
    </div>
</body>
</html>