<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MCC Event & Portfolio Organizer</title>
    <link href="{{ asset('user/footer/footer.css') }}" rel="stylesheet">    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&family=Roboto+Condensed:wght@300;400;700&display=swap" rel="stylesheet">
    <link href="{{ asset('user/dashboard/dashboard.css') }}" rel="stylesheet">    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />

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
                <img src="images/logo.png" alt="MCC Logo">
            </div>

            <!-- Header -->
            <h1 class="hero-title">
                <span class="white-text">MCC Event & Portfolio</span><br>
                <span class="red-text">Organizer</span>
            </h1>
            <p class="hero-subtitle">Madridejos Community College</p>

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
            .welcome-wrapper {
                padding: 20px 15px;
            }

            .welcome-container {
                max-width: 100%;
                padding: 20px 0;
            }

            .logo-section {
                margin-bottom: 25px;
            }

            .logo-section img {
                width: 90px;
            }

            .welcome-header {
                margin-bottom: 30px;
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