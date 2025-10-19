<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MCC Event & Portfolio Organizer</title>
    <link href="{{ asset('user/footer/footer.css') }}" rel="stylesheet">  
    <link href="{{ asset('user/welcome/welcome.css') }}" rel="stylesheet">   
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