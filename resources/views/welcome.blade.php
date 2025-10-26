<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MCC Event & Portfolio Organizer</title>
    <link href="{{ asset('public/user/footer/footer.css') }}" rel="stylesheet">    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&family=Roboto+Condensed:wght@300;400;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
            overflow-x: hidden;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: #f8fafc;
            overflow-x: hidden;
            position: relative;
            background: #000;
        }

        /* Animated Background */
        .animated-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        .bg-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0;
            transition: opacity 2s ease-in-out;
        }

        .bg-image.active {
            opacity: 1;
        }

        .bg-image.bg1 {
            background-image: url('/images/mcc background.jpg');
        }

        .bg-image.bg2 {
            background-image: url('/images/mcc backgrounbackground1.jpg')
        }

        .bg-image.bg3 {
           background-image: url('/images/mcc background2.jpg');
        }

        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 80px 20px 20px;
            position: relative;
            overflow: hidden;
        }

        .hero::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(74, 26, 92, 0.65) 0%, rgba(107, 44, 145, 0.68) 50%, rgba(61, 26, 120, 0.65) 100%);
            z-index: 1;
        }

        .hero-container {
            text-align: center;
            max-width: 1000px;
            width: 100%;
            animation: fadeIn 0.8s ease-out;
            position: relative;
            z-index: 2;
            padding: 0 15px;
            transform: scale(0.8);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px) scale(0.8); }
            to { opacity: 1; transform: translateY(0) scale(0.8); }
        }

        .logo-section {
            margin-bottom: 30px;
            animation: fadeInLogo 1s ease-out 0.2s backwards;
        }

        @keyframes fadeInLogo {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .logo-section img {
            width: clamp(100px, 20vw, 150px);
            height: auto;
            margin-bottom: 15px;
            filter: drop-shadow(0 4px 30px rgba(0, 0, 0, 0.4));
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .hero-title {
            font-size: clamp(24px, 6vw, 64px);
            font-weight: 700;
            margin-bottom: 15px;
            letter-spacing: 1px;
            font-family: 'Oswald', sans-serif;
            text-transform: uppercase;
            text-shadow: 4px 4px 8px rgba(0, 0, 0, 0.6);
            line-height: 1.2;
            animation: fadeInTitle 1s ease-out 0.4s backwards;
        }

        @keyframes fadeInTitle {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .hero-title .white-text {
            color: #ffffff;
        }

        .hero-title .red-text {
            color: #e85d5d;
        }

        .hero-subtitle {
            font-size: clamp(12px, 3vw, 20px);
            color: #e0e0e0;
            font-weight: 400;
            font-family: 'Roboto Condensed', sans-serif;
            text-transform: uppercase;
            letter-spacing: clamp(1px, 0.5vw, 3px);
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            margin-top: 15px;
            margin-bottom: 40px;
            animation: fadeInSubtitle 1s ease-out 0.4s backwards;
        }

        @keyframes fadeInSubtitle {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .cta-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInButtons 1s ease-out 0.6s backwards;
        }

        @keyframes fadeInButtons {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .btn {
            padding: clamp(14px, 3vw, 18px) clamp(30px, 8vw, 50px);
            border-radius: 0;
            font-size: clamp(14px, 2.5vw, 17px);
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.4s ease;
            font-family: 'Oswald', sans-serif;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            cursor: pointer;
            border: none;
            min-width: clamp(160px, 40vw, 220px);
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

        .btn-primary {
            background: linear-gradient(135deg, #e53e3e 0%, #d53f41 100%);
            color: white;
            box-shadow: 0 4px 20px rgba(229, 62, 62, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(229, 62, 62, 0.6);
        }

        .btn-secondary {
            background: transparent;
            color: #ffffff;
            border: 2px solid rgba(255, 255, 255, 0.7);
            box-shadow: 0 4px 20px rgba(255, 255, 255, 0.1);
            z-index: 1;
        }

        .btn-secondary::before {
            z-index: -1;
        }

        .btn-secondary:hover {
            color: #ffffff;
            border-color: #e53e3e;
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(229, 62, 62, 0.4);
        }

        .btn-secondary:hover::before {
            left: 0;
        }

        .btn i {
            font-size: clamp(16px, 3vw, 20px);
        }

        .scroll-indicator {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            animation: bounce 2s infinite;
            z-index: 2;
            color: #cbd5e1;
        }

        @keyframes bounce {
            0%, 100% { transform: translateX(-50%) translateY(0); }
            50% { transform: translateX(-50%) translateY(10px); }
        }

        .scroll-indicator span {
            font-size: clamp(10px, 2vw, 12px);
            text-transform: uppercase;
            letter-spacing: 2px;
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
            width: clamp(150px, 30vw, 350px);
            height: clamp(150px, 30vw, 350px);
            top: 10%;
            left: 5%;
            animation-delay: 0s;
        }

        .bg-decoration.circle-2 {
            width: clamp(100px, 25vw, 250px);
            height: clamp(100px, 25vw, 250px);
            bottom: 15%;
            right: 10%;
            animation-delay: -2s;
        }

        .bg-decoration.circle-3 {
            width: clamp(80px, 20vw, 180px);
            height: clamp(80px, 20vw, 180px);
            top: 60%;
            left: 15%;
            animation-delay: -4s;
        }

        /* Content Sections */
        .section {
            padding: clamp(60px, 15vw, 120px) 20px;
            position: relative;
            background: linear-gradient(180deg, rgba(20, 5, 10, 0.95) 0%, rgba(40, 10, 15, 0.97) 50%, rgba(25, 5, 10, 0.95) 100%);
        }

        .section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 50%, rgba(229, 62, 62, 0.05) 0%, transparent 50%),
                        radial-gradient(circle at 80% 50%, rgba(139, 0, 0, 0.04) 0%, transparent 50%);
            pointer-events: none;
        }

        .section-header {
            text-align: center;
            max-width: 800px;
            margin: 0 auto clamp(40px, 10vw, 80px);
            padding: 0 15px;
        }

        .section-label {
            display: inline-block;
            padding: 6px 16px;
            background: rgba(229, 62, 62, 0.1);
            border: 1px solid rgba(229, 62, 62, 0.3);
            border-radius: 50px;
            color: #f87171;
            font-size: clamp(12px, 2.5vw, 14px);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 20px;
        }

        .section-title {
            font-family: 'Oswald', sans-serif;
            font-size: clamp(28px, 6vw, 56px);
            font-weight: 700;
            margin-bottom: 15px;
            line-height: 1.2;
            color: #ffffff;
        }

        .section-description {
            font-size: clamp(14px, 3vw, 18px);
            color: #cbd5e1;
            line-height: 1.6;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 280px), 1fr));
            gap: clamp(20px, 4vw, 30px);
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .feature-card {
            background: linear-gradient(135deg, rgba(229, 62, 62, 0.08) 0%, rgba(139, 0, 0, 0.05) 100%);
            border: 1px solid rgba(229, 62, 62, 0.15);
            border-radius: clamp(16px, 4vw, 24px);
            padding: clamp(25px, 6vw, 40px);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #e53e3e, #8b0000);
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            background: linear-gradient(135deg, rgba(229, 62, 62, 0.15) 0%, rgba(139, 0, 0, 0.10) 100%);
            border-color: rgba(229, 62, 62, 0.4);
            box-shadow: 0 20px 60px rgba(229, 62, 62, 0.2);
        }

        .feature-card:hover::before {
            transform: scaleX(1);
        }

        .feature-icon {
            width: clamp(50px, 12vw, 70px);
            height: clamp(50px, 12vw, 70px);
            background: linear-gradient(135deg, #e53e3e, #ec4899);
            border-radius: clamp(12px, 3vw, 16px);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(24px, 6vw, 32px);
            margin-bottom: clamp(16px, 4vw, 24px);
            color: white;
        }

        .feature-title {
            font-size: clamp(18px, 4vw, 24px);
            font-weight: 700;
            margin-bottom: 10px;
            color: white;
        }

        .feature-description {
            font-size: clamp(14px, 3vw, 16px);
            color: #94a3b8;
            line-height: 1.6;
        }

        /* Map Section */
        .map-section {
            background: linear-gradient(180deg, rgba(10, 2, 5, 0.97) 0%, rgba(25, 5, 10, 0.98) 100%);
            border-top: 1px solid rgba(229, 62, 62, 0.2);
            border-bottom: 1px solid rgba(229, 62, 62, 0.2);
            padding: clamp(60px, 15vw, 120px) 20px;
            position: relative;
        }

        .map-container {
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .map-wrapper {
            background: rgba(229, 62, 62, 0.05);
            border: 2px solid rgba(229, 62, 62, 0.2);
            border-radius: clamp(16px, 4vw, 24px);
            padding: clamp(15px, 4vw, 30px);
            backdrop-filter: blur(10px);
        }

        #map {
            height: clamp(300px, 60vw, 500px);
            border-radius: clamp(12px, 3vw, 16px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
        }

        .map-info {
            margin-top: clamp(20px, 5vw, 30px);
            text-align: center;
        }

        .map-info h3 {
            font-size: clamp(18px, 4vw, 24px);
            font-weight: 700;
            margin-bottom: 10px;
            color: #ffffff;
        }

        .map-info p {
            font-size: clamp(14px, 3vw, 16px);
            color: #cbd5e1;
            line-height: 1.6;
        }

        .location-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: rgba(229, 62, 62, 0.15);
            border: 1px solid rgba(229, 62, 62, 0.3);
            border-radius: 50px;
            margin-top: 12px;
            color: #f87171;
            font-weight: 600;
            font-size: clamp(12px, 2.5vw, 14px);
        }

        /* Testimonials Section */
        .testimonials-section {
            padding: clamp(60px, 15vw, 120px) 20px;
            background: linear-gradient(180deg, rgba(15, 5, 10, 0.95) 0%, rgba(25, 8, 12, 0.97) 100%);
            position: relative;
        }

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 300px), 1fr));
            gap: clamp(20px, 4vw, 30px);
            max-width: 1400px;
            margin: 0 auto;
        }

        .testimonial-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(229, 62, 62, 0.25);
            border-radius: clamp(16px, 4vw, 20px);
            padding: clamp(25px, 5vw, 35px);
            transition: all 0.4s ease;
            position: relative;
            backdrop-filter: blur(15px);
        }

        .testimonial-card::before {
            content: '"';
            position: absolute;
            top: 15px;
            left: 20px;
            font-size: clamp(50px, 12vw, 80px);
            color: rgba(229, 62, 62, 0.2);
            font-family: Georgia, serif;
            line-height: 1;
        }

        .testimonial-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(229, 62, 62, 0.4);
            box-shadow: 0 15px 40px rgba(229, 62, 62, 0.15);
        }

        .testimonial-content {
            position: relative;
            z-index: 1;
            margin-bottom: 20px;
        }

        .testimonial-text {
            font-size: clamp(14px, 3vw, 16px);
            line-height: 1.7;
            color: #cbd5e1;
            font-style: italic;
            margin-bottom: 15px;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-top: 15px;
            border-top: 1px solid rgba(229, 62, 62, 0.15);
        }

        .author-avatar {
            width: clamp(40px, 10vw, 50px);
            height: clamp(40px, 10vw, 50px);
            border-radius: 50%;
            background: linear-gradient(135deg, #e53e3e, #ec4899);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(16px, 4vw, 20px);
            font-weight: 700;
            color: white;
            flex-shrink: 0;
        }

        .author-info {
            text-align: left;
        }

        .author-name {
            font-size: clamp(14px, 3vw, 16px);
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 4px;
        }

        .author-role {
            font-size: clamp(11px, 2.5vw, 13px);
            color: #94a3b8;
        }

        .rating {
            display: flex;
            gap: 4px;
            margin-bottom: 12px;
        }

        .rating i {
            color: #fbbf24;
            font-size: clamp(12px, 2.5vw, 14px);
        }

        /* Contact Section */
        .cta-section {
            text-align: center;
            padding: clamp(60px, 15vw, 120px) 20px;
            background: linear-gradient(135deg, rgba(40, 10, 15, 0.95) 0%, rgba(20, 5, 10, 0.97) 100%);
            position: relative;
            overflow: hidden;
        }

        .cta-content {
            max-width: 800px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
            padding: 0 15px;
        }

        .cta-title {
            font-family: 'Oswald', sans-serif;
            font-size: clamp(28px, 6vw, 56px);
            font-weight: 700;
            margin-bottom: 20px;
            color: #ffffff;
        }

        .cta-description {
            font-size: clamp(16px, 3.5vw, 20px);
            color: #cbd5e1;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        /* About Us Section */
        .about-section {
            padding: clamp(60px, 15vw, 120px) 20px;
            background: linear-gradient(180deg, rgba(20, 5, 10, 0.97) 0%, rgba(30, 8, 15, 0.98) 100%);
            position: relative;
            border-top: 1px solid rgba(229, 62, 62, 0.2);
        }

        .about-container {
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .about-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 280px), 1fr));
            gap: clamp(25px, 5vw, 40px);
            margin-top: clamp(40px, 10vw, 60px);
        }

        .about-card {
            background: linear-gradient(135deg, rgba(229, 62, 62, 0.08) 0%, rgba(139, 0, 0, 0.05) 100%);
            border: 1px solid rgba(229, 62, 62, 0.2);
            border-radius: clamp(16px, 4vw, 20px);
            padding: clamp(25px, 6vw, 40px);
            transition: all 0.4s ease;
            backdrop-filter: blur(10px);
        }

        .about-card:hover {
            transform: translateY(-8px);
            background: linear-gradient(135deg, rgba(229, 62, 62, 0.12) 0%, rgba(139, 0, 0, 0.08) 100%);
            border-color: rgba(229, 62, 62, 0.4);
            box-shadow: 0 20px 60px rgba(229, 62, 62, 0.2);
        }

        .about-icon {
            width: clamp(50px, 12vw, 70px);
            height: clamp(50px, 12vw, 70px);
            background: linear-gradient(135deg, #e53e3e, #ec4899);
            border-radius: clamp(12px, 3vw, 16px);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(24px, 6vw, 32px);
            margin-bottom: clamp(16px, 4vw, 24px);
            color: white;
        }

        .about-card h3 {
            font-size: clamp(18px, 4vw, 24px);
            font-weight: 700;
            margin-bottom: 12px;
            color: #ffffff;
        }

        .about-card p {
            font-size: clamp(14px, 3vw, 16px);
            color: #cbd5e1;
            line-height: 1.7;
        }

        .about-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 150px), 1fr));
            gap: clamp(15px, 4vw, 30px);
            margin-top: clamp(40px, 10vw, 60px);
        }

        .stat-item {
            text-align: center;
            padding: clamp(20px, 5vw, 30px);
            background: rgba(229, 62, 62, 0.05);
            border: 1px solid rgba(229, 62, 62, 0.2);
            border-radius: clamp(12px, 3vw, 16px);
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            transform: translateY(-5px);
            background: rgba(229, 62, 62, 0.1);
            border-color: rgba(229, 62, 62, 0.3);
        }

        .stat-number {
            font-size: clamp(32px, 8vw, 48px);
            font-weight: 700;
            color: #e53e3e;
            margin-bottom: 8px;
            font-family: 'Oswald', sans-serif;
        }

        .stat-label {
            font-size: clamp(12px, 3vw, 16px);
            color: #cbd5e1;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Leaflet custom styles */
        .leaflet-popup-content-wrapper {
            background: rgba(15, 23, 42, 0.95);
            color: white;
            border-radius: 12px;
            padding: 8px;
        }

        .leaflet-popup-content {
            margin: 16px;
            font-family: 'Poppins', sans-serif;
        }

        .leaflet-popup-content h3 {
            margin-bottom: 8px;
            color: #e53e3e;
            font-size: 16px;
        }

        .leaflet-popup-tip {
            background: rgba(15, 23, 42, 0.95);
        }

        /* Mobile Optimizations */
        @media (max-width: 768px) {
            .hero {
                min-height: 100vh;
                padding: 80px 15px 40px;
            }

            .scroll-indicator {
                bottom: 20px;
            }

            .cta-buttons {
                flex-direction: column;
                width: 100%;
                max-width: 300px;
                margin: 0 auto;
            }

            .btn {
                width: 100%;
            }

            .bg-decoration.circle-1,
            .bg-decoration.circle-2,
            .bg-decoration.circle-3 {
                opacity: 0.3;
            }

            .section {
                padding: 60px 15px;
            }

            .features-grid,
            .testimonials-grid,
            .about-grid {
                gap: 20px;
            }

            .map-section {
                padding: 60px 15px;
            }

            #map {
                height: 300px;
            }
        }

        @media (max-width: 480px) {
            .hero-title {
                font-size: 24px;
            }

            .hero-subtitle {
                font-size: 12px;
                margin-bottom: 30px;
            }

            .feature-card,
            .testimonial-card,
            .about-card {
                padding: 20px;
            }

            .section-title {
                font-size: 28px;
            }

            .section-description {
                font-size: 14px;
            }
        }

        /* Smooth reveal animations */
        .reveal {
            opacity: 0;
            transform: translateY(50px);
            transition: all 0.8s ease;
        }

        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        @media (prefers-reduced-motion: reduce) {
            * {
                animation: none !important;
                transition: none !important;
            }
        }
    </style>
</head>
<body>
    <!-- Include Navigation -->
    @include('layouts.intronav')

    <!-- Animated Background -->
    <div class="animated-background">
        <div class="bg-image bg1 active"></div>
        <div class="bg-image bg2"></div>
        <div class="bg-image bg3"></div>
    </div>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <!-- Floating orbs -->
        <div class="bg-decoration circle-1"></div>
        <div class="bg-decoration circle-2"></div>
        <div class="bg-decoration circle-3"></div>

        <div class="hero-container">
            <!-- Logo -->
            <div class="logo-section">
                <img src="{{ asset('public/images/logo.png') }}" alt="MCC Logo">
            </div>

            <!-- Header -->
            <h1 class="hero-title">
                <span class="white-text">MCC Event & Portfolio</span><br>
                <span class="red-text">Organizer</span>
            </h1>
            <p class="hero-subtitle">Madridejos Community College</p>
            <input type="hidden" name="g-recaptcha-response" id="recaptchaResponse">
            <!-- CTA Button -->
            <div class="cta-buttons">
                <a href="{{ route('login') }}" class="btn btn-secondary">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Sign In</span>
                </a>
            </div>
        </div>

        <div class="scroll-indicator" onclick="document.querySelector('#features').scrollIntoView({behavior: 'smooth'})">
            <span>Scroll</span>
            <i class="fas fa-chevron-down"></i>
        </div>
    </section>

    <!-- Features Section -->
    <section class="section" id="features">
        <div class="section-header reveal">
            <span class="section-label">Powerful Features</span>
            <h2 class="section-title">Everything You Need</h2>
            <p class="section-description">
                Powerful tools designed to streamline event management and portfolio creation for the modern campus.
            </p>
        </div>
        
        <div class="features-grid">
            <div class="feature-card reveal">
                <div class="feature-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <h3 class="feature-title">Event Scheduling</h3>
                <p class="feature-description">
                    Plan, schedule, and track campus events with real-time updates and automated reminders.
                </p>
            </div>
            
            <div class="feature-card reveal">
                <div class="feature-icon">
                    <i class="fas fa-folder-open"></i>
                </div>
                <h3 class="feature-title">Portfolio Builder</h3>
                <p class="feature-description">
                    Create stunning digital portfolios to showcase projects, achievements, and skills effortlessly.
                </p>
            </div>
            
            <div class="feature-card reveal">
                <div class="feature-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="feature-title">Team Collaboration</h3>
                <p class="feature-description">
                    Invite team members, assign roles, and collaborate seamlessly across departments.
                </p>
            </div>
            
            <div class="feature-card reveal">
                <div class="feature-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="feature-title">Analytics Dashboard</h3>
                <p class="feature-description">
                    Track engagement, monitor attendance, and gain insights with comprehensive analytics.
                </p>
            </div>
            
            <div class="feature-card reveal">
                <div class="feature-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <h3 class="feature-title">Mobile Responsive</h3>
                <p class="feature-description">
                    Access everything on any device with a fully responsive design for perfect experience.
                </p>
            </div>
            
            <div class="feature-card reveal">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3 class="feature-title">Secure & Private</h3>
                <p class="feature-description">
                    Enterprise-grade security keeps your data safe with encryption and privacy controls.
                </p>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="map-section reveal" id="location">
        <div class="map-container">
            <div class="section-header">
                <span class="section-label">Our Location</span>
                <h2 class="section-title">Find Us Here</h2>
                <p class="section-description">
                    Located in Bunakan, Madridejos, Cebu - Easy to reach and accessible for all students and faculty.
                </p>
            </div>
            
            <div class="map-wrapper">
                <div id="map"></div>
                <div class="map-info">
                    <h3>Madridejos Community College</h3>
                    <p>Bunakan, Madridejos, Cebu Province, Philippines</p>
                    <div class="location-badge">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Bunakan, Madridejos, Cebu</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials-section" id="feedback">
        <div class="section-header reveal">
            <span class="section-label">Student Feedback</span>
            <h2 class="section-title">What Our Students Say</h2>
            <p class="section-description">
                Real experiences from students and faculty using our platform every day.
            </p>
        </div>

        <div class="testimonials-grid">
            <div class="testimonial-card reveal">
                <div class="testimonial-content">
                    <div class="rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">
                        "This system has completely transformed how I manage my academic portfolio. Everything is organized and easy to access. I can showcase my projects professionally!"
                    </p>
                </div>
                <div class="testimonial-author">
                    <div class="author-avatar">MA</div>
                    <div class="author-info">
                        <div class="author-name">Maria Santos</div>
                        <div class="author-role">4th Year BSIT Student</div>
                    </div>
                </div>
            </div>

            <div class="testimonial-card reveal">
                <div class="testimonial-content">
                    <div class="rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">
                        "Event scheduling has never been easier! I love how I get automatic reminders and can track all campus activities in one place. Highly recommended!"
                    </p>
                </div>
                <div class="testimonial-author">
                    <div class="author-avatar">JD</div>
                    <div class="author-info">
                        <div class="author-name">John Dela Cruz</div>
                        <div class="author-role">Student Council President</div>
                    </div>
                </div>
            </div>

            <div class="testimonial-card reveal">
                <div class="testimonial-content">
                    <div class="rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">
                        "As a faculty member, this platform makes collaboration with students seamless. The analytics dashboard gives me insights I never had before."
                    </p>
                </div>
                <div class="testimonial-author">
                    <div class="author-avatar">LR</div>
                    <div class="author-info">
                        <div class="author-name">Prof. Linda Reyes</div>
                        <div class="author-role">Computer Science Faculty</div>
                    </div>
                </div>
            </div>

            <div class="testimonial-card reveal">
                <div class="testimonial-content">
                    <div class="rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">
                        "The mobile responsiveness is amazing! I can manage everything from my phone. Perfect for busy students like me who are always on the go."
                    </p>
                </div>
                <div class="testimonial-author">
                    <div class="author-avatar">CB</div>
                    <div class="author-info">
                        <div class="author-name">Carlos Bautista</div>
                        <div class="author-role">3rd Year Business Student</div>
                    </div>
                </div>
            </div>

            <div class="testimonial-card reveal">
                <div class="testimonial-content">
                    <div class="rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">
                        "Building my digital portfolio was so easy! The interface is intuitive and the results look professional. It really helped me land my internship."
                    </p>
                </div>
                <div class="testimonial-author">
                    <div class="author-avatar">AR</div>
                    <div class="author-info">
                        <div class="author-name">Anna Rodriguez</div>
                        <div class="author-role">2nd Year Education Student</div>
                    </div>
                </div>
            </div>

            <div class="testimonial-card reveal">
                <div class="testimonial-content">
                    <div class="rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">
                        "The security features give me peace of mind. I know my academic work and personal information are safe. This is exactly what our college needed!"
                    </p>
                </div>
                <div class="testimonial-author">
                    <div class="author-avatar">RT</div>
                    <div class="author-info">
                        <div class="author-name">Roberto Torres</div>
                        <div class="author-role">IT Administrator</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="cta-section reveal" id="contact">
        <div class="cta-content">
            <h2 class="cta-title">Contact Us</h2>
            <p class="cta-description">
                Have questions or need assistance? Get in touch with us and we'll be happy to help you get started with MCC Event & Portfolio Organizer.
            </p>
            <div class="cta-buttons">
                <a href="#" class="btn btn-primary">
                    <i class="fas fa-envelope"></i>
                    <span>Get In Touch</span>
                </a>
            </div>
        </div>
    </section>

    <!-- About Us Section -->
    <section class="about-section" id="about">
        <div class="about-container">
            <div class="section-header reveal">
                <span class="section-label">About Us</span>
                <h2 class="section-title">Who We Are</h2>
                <p class="section-description">
                    MCC Event & Portfolio Organizer is dedicated to empowering students and faculty at Madridejos Community College with innovative tools for event management and professional portfolio development.
                </p>
            </div>

            <div class="about-grid">
                <div class="about-card reveal">
                    <div class="about-icon">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h3>Our Mission</h3>
                    <p>
                        To provide a comprehensive digital platform that simplifies event organization and portfolio creation, enabling students to showcase their talents and achievements while fostering a vibrant campus community.
                    </p>
                </div>

                <div class="about-card reveal">
                    <div class="about-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h3>Our Vision</h3>
                    <p>
                        To become the leading event and portfolio management platform for educational institutions in the region, setting the standard for digital innovation in academic excellence and student engagement.
                    </p>
                </div>

                <div class="about-card reveal">
                    <div class="about-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3>Our Values</h3>
                    <p>
                        Innovation, collaboration, excellence, and student empowerment drive everything we do. We believe in creating tools that are accessible, intuitive, and designed with the student experience at the forefront.
                    </p>
                </div>
            </div>

            <div class="about-stats reveal">
                <div class="stat-item">
                    <div class="stat-number">500+</div>
                    <div class="stat-label">Active Users</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">150+</div>
                    <div class="stat-label">Events Organized</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">50+</div>
                    <div class="stat-label">Certificates Created</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">98%</div>
                    <div class="stat-label">Satisfaction Rate</div>
                </div>
            </div>
        </div>
    </section>
    <script src="https://www.google.com/recaptcha/api.js?render={{ env('RECAPTCHA_SITE_KEY') }}"></script>
    @include('layouts.footer')
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <script>
        // Background image rotation
        let currentBg = 0;
        const bgImages = document.querySelectorAll('.bg-image');
        
        function rotateBackground() {
            bgImages[currentBg].classList.remove('active');
            currentBg = (currentBg + 1) % bgImages.length;
            bgImages[currentBg].classList.add('active');
        }
        
        setInterval(rotateBackground, 5000);

        // Initialize map
        const map = L.map('map', {
            scrollWheelZoom: false,
            doubleClickZoom: true,
            touchZoom: true,
            boxZoom: true,
            keyboard: true
        }).setView([11.2667, 123.7333], 13);

        map.on('wheel', function(e) {
            if (e.originalEvent.ctrlKey) {
                map.scrollWheelZoom.enable();
            } else {
                map.scrollWheelZoom.disable();
            }
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        const mccIcon = L.divIcon({
            className: 'custom-marker',
            html: '<div style="background: linear-gradient(135deg, #e53e3e, #ec4899); width: 40px; height: 40px; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); border: 3px solid white; box-shadow: 0 5px 20px rgba(229, 62, 62, 0.6);"><i class="fas fa-graduation-cap" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(45deg); color: white; font-size: 18px;"></i></div>',
            iconSize: [40, 40],
            iconAnchor: [20, 40],
            popupAnchor: [0, -40]
        });

        const marker = L.marker([11.2667, 123.7333], { icon: mccIcon }).addTo(map);
        marker.bindPopup('<h3>Madridejos Community College</h3><p>Bunakan, Madridejos, Cebu</p>');

        // Smooth reveal on scroll
        const reveals = document.querySelectorAll('.reveal');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                }
            });
        }, {
            threshold: 0.1
        });

        reveals.forEach(el => {
            observer.observe(el);
        });

        // Resize map on window resize
        window.addEventListener('resize', function() {
            setTimeout(function() {
                map.invalidateSize();
            }, 100);
        });
    </script>
</body>
</html>
