<!-- Navigation Bar -->
<nav class="navbar" id="navbar">
    <div class="nav-container">
        <a href="#home" class="nav-logo">
            <img src="{{ asset('images/logo.png') }}" alt="MCC Logo">
            <span>MCC E&PO</span>
        </a>
        <ul class="nav-menu" id="navMenu">
            <li><a href="#features">Features</a></li>
            <li><a href="#location">Location</a></li>
            <li><a href="#feedback">Feedback</a></li>
            <li><a href="#contact">Contact Us</a></li>
            <li><a href="#about">About Us</a></li>
        </ul>
        <button class="mobile-toggle" id="mobileToggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>
</nav>

<style>
    /* Navigation Bar */
    .navbar {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
        background: rgba(10, 5, 15, 0.85);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(229, 62, 62, 0.2);
        padding: 8px 20px;
        transition: all 0.3s ease;
    }

    .navbar.scrolled {
        background: rgba(10, 5, 15, 0.95);
        box-shadow: 0 2px 20px rgba(229, 62, 62, 0.1);
    }

    .nav-container {
        max-width: 1400px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .nav-logo {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 18px;
        font-weight: 700;
        color: #fff;
        text-decoration: none;
        font-family: 'Oswald', sans-serif;
    }

    .nav-logo img {
        width: 32px;
        height: 32px;
    }

    .nav-menu {
        display: flex;
        gap: 25px;
        list-style: none;
        align-items: center;
    }

    .nav-menu li a {
        color: #cbd5e1;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s ease;
        position: relative;
    }

    .nav-menu li a::after {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 0;
        width: 0;
        height: 2px;
        background: #e53e3e;
        transition: width 0.3s ease;
    }

    .nav-menu li a:hover {
        color: #e53e3e;
    }

    .nav-menu li a:hover::after {
        width: 100%;
    }

    .nav-login {
        background: linear-gradient(135deg, #e53e3e, #d53f41);
        color: white;
        padding: 8px 20px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .nav-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(229, 62, 62, 0.4);
    }

    .mobile-toggle {
        display: none;
        background: none;
        border: none;
        color: white;
        font-size: 22px;
        cursor: pointer;
    }

    /* Mobile Responsive Navigation */
    @media (max-width: 768px) {
        .nav-menu {
            position: fixed;
            top: 55px;
            left: 0;
            right: 0;
            background: rgba(10, 5, 15, 0.98);
            flex-direction: column;
            padding: 20px;
            gap: 15px;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            border-bottom: 1px solid rgba(229, 62, 62, 0.2);
        }

        .nav-menu.active {
            transform: translateX(0);
        }

        .mobile-toggle {
            display: block;
        }
    }
</style>

<script>
    // Navbar show/hide on scroll
    const navbar = document.getElementById('navbar');
    let lastScroll = 0;

    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;
        
        if (currentScroll > 200) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
        
        lastScroll = currentScroll;
    });

    // Mobile menu toggle
    const mobileToggle = document.getElementById('mobileToggle');
    const navMenu = document.getElementById('navMenu');

    mobileToggle.addEventListener('click', () => {
        navMenu.classList.toggle('active');
        const icon = mobileToggle.querySelector('i');
        icon.classList.toggle('fa-bars');
        icon.classList.toggle('fa-times');
    });

    // Close mobile menu when clicking a link
    document.querySelectorAll('.nav-menu a').forEach(link => {
        link.addEventListener('click', () => {
            navMenu.classList.remove('active');
            const icon = mobileToggle.querySelector('i');
            icon.classList.add('fa-bars');
            icon.classList.remove('fa-times');
        });
    });

    // Smooth scroll for all anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
</script>