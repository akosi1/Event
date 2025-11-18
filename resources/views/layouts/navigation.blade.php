<!-- Navigation -->
<nav class="navbar" id="navbar">
    <div class="nav-container">
        <a href="{{ route('dashboard') }}" class="nav-logo">
            <img src="{{ asset('images/logo.png') }}" alt="MCC Logo">
            <span>MCC E&PO</span>
        </a>

        <button class="mobile-toggle" id="mobileToggle" aria-label="Toggle Navigation">
            <i class="fas fa-bars"></i>
        </button>

        <div class="nav-content" id="navContent">
            
            <a href="{{ route('dashboard') }}" class="nav-btn" data-nav-link>
                <i class="fas fa-home"></i>
                Home
            </a>

            <div class="dropdown" id="deptDropdown">
                <button class="dropdown-btn" type="button" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-graduation-cap"></i>
                    <span id="deptLabel">
                        @if(request('department'))
                            {{ request('department') }}
                        @else
                            Departments
                        @endif
                    </span>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </button>
                <div class="dropdown-menu" role="menu">
                    <div class="dropdown-header">Select Department</div>
                    
                    @php
                        $departments = [
                            'BSIT' => 'Information Technology',
                            'BSBA' => 'Business Administration',
                            'BSED' => 'Science in Education',
                            'BEED' => 'Elementary Education',
                            'BSHM' => 'Hospitality Management'
                        ];
                    @endphp

                    @foreach($departments as $code => $name)
                        <a href="{{ route('dashboard', array_merge(request()->query(), ['department' => $code])) }}"
                           class="dropdown-item {{ request('department') === $code ? 'active' : '' }}"
                           role="menuitem">
                            <i class="fas fa-graduation-cap"></i>
                            <div class="dept-info">
                                <div class="dept-code">{{ $code }}</div>
                                <div class="dept-name">{{ $name }}</div>
                            </div>
                        </a>
                    @endforeach

                    <div class="dropdown-divider"></div>
                    
                    <a href="{{ route('dashboard', request()->except('department')) }}"
                       class="dropdown-item"
                       role="menuitem">
                        <i class="fas fa-times"></i>
                        Clear Filter
                    </a>
                </div>
            </div>

            <a href="{{ route('certificates') }}" class="nav-btn" data-nav-link>
                <i class="fas fa-certificate"></i>
                Certificates
            </a>
            
            <div class="dropdown" id="userDropdown">
                <button class="dropdown-btn user-btn" type="button" aria-haspopup="true" aria-expanded="false">
                    @if(auth()->user()->profile_picture)
                        <img src="{{ auth()->user()->profile_picture }}" 
                             alt="{{ auth()->user()->first_name }}" 
                             class="user-profile-pic">
                    @else
                        <div class="user-profile-initials">
                            {{ substr(auth()->user()->first_name, 0, 1) }}{{ substr(auth()->user()->last_name, 0, 1) }}
                        </div>
                    @endif
                    <span class="user-name">{{ auth()->user()->first_name }}</span>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right" role="menu">
                    <a href="{{ route('profile.edit') }}" class="dropdown-item" role="menuitem">
                        <i class="fas fa-user"></i>
                        Profile
                    </a>
                    <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                        @csrf
                        <button type="submit" class="dropdown-item logout" role="menuitem">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
            
        </div>
    </div>
</nav>

<div class="mobile-overlay" id="mobileOverlay"></div>

<style>
/* ========================================
   ROOT VARIABLES
   ======================================== */
:root {
    --nav-bg: rgba(10, 5, 15, 0.85);
    --nav-bg-scrolled: rgba(10, 5, 15, 0.95);
    --accent-red: #e53e3e;
    --accent-red-hover: #c53030;
    --text-primary: #ffffff;
    --text-secondary: #cbd5e1;
    --border-color: rgba(229, 62, 62, 0.2);
    --dropdown-bg: rgba(10, 5, 15, 0.98);
    --hover-bg: rgba(229, 62, 62, 0.1);
    --active-bg: rgba(229, 62, 62, 0.15);
}

/* ========================================
   NAVBAR STRUCTURE
   ======================================== */
.navbar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    background: var(--nav-bg);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid var(--border-color);
    padding: 8px 20px;
    transition: all 0.3s ease;
}

.navbar.scrolled {
    background: var(--nav-bg-scrolled);
    box-shadow: 0 2px 20px rgba(229, 62, 62, 0.1);
}

.nav-container {
    max-width: 1400px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* ========================================
   LOGO
   ======================================== */
.nav-logo {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 18px;
    font-weight: 700;
    color: var(--text-primary);
    text-decoration: none;
    font-family: 'Oswald', sans-serif;
    transition: transform 0.3s ease;
    z-index: 1002;
}

.nav-logo:hover {
    transform: scale(1.05);
}

.nav-logo img {
    width: 32px;
    height: 32px;
    border-radius: 4px;
}

/* ========================================
   MOBILE TOGGLE
   ======================================== */
.mobile-toggle {
    display: none;
    background: none;
    border: none;
    color: var(--text-primary);
    font-size: 22px;
    cursor: pointer;
    transition: all 0.3s ease;
    padding: 8px;
    z-index: 1002;
}

.mobile-toggle:hover {
    color: var(--accent-red);
    transform: scale(1.1);
}

/* ========================================
   NAVIGATION CONTENT
   ======================================== */
.nav-content {
    display: flex;
    gap: 25px;
    align-items: center;
}

/* ========================================
   NAVIGATION BUTTONS
   ======================================== */
.nav-btn,
.dropdown-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--text-secondary);
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
    position: relative;
    background: none;
    border: none;
    cursor: pointer;
    font-family: 'Oswald', sans-serif;
    padding: 8px 0;
}

.nav-btn i,
.dropdown-btn i {
    font-size: 16px;
    transition: all 0.3s ease;
}

.nav-btn::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0;
    height: 2px;
    background: var(--accent-red);
    transition: width 0.3s ease;
}

.nav-btn:hover,
.dropdown-btn:hover {
    color: var(--accent-red);
}

.nav-btn:hover i,
.dropdown-btn:hover i {
    transform: scale(1.1);
}

.nav-btn:hover::after {
    width: 100%;
}

/* ========================================
   USER PROFILE PICTURE & INITIALS
   ======================================== */
.user-profile-pic {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--accent-red);
    transition: all 0.3s ease;
}

.user-profile-initials {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: var(--accent-red);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-primary);
    border: 2px solid var(--accent-red);
    transition: all 0.3s ease;
}

.user-btn {
    padding: 4px 0;
}

.user-btn:hover .user-profile-pic,
.user-btn:hover .user-profile-initials {
    transform: scale(1.1);
    box-shadow: 0 0 10px rgba(229, 62, 62, 0.5);
}

.user-name {
    font-size: 14px;
}

/* ========================================
   DROPDOWN STRUCTURE
   ======================================== */
.dropdown {
    position: relative;
}

.dropdown-arrow {
    font-size: 12px !important;
    transition: transform 0.2s ease;
}

.dropdown.active .dropdown-arrow {
    transform: rotate(180deg);
}

.dropdown-menu {
    position: absolute;
    top: calc(100% + 10px);
    left: 0;
    min-width: 280px;
    background: var(--dropdown-bg);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
    z-index: 1001;
    backdrop-filter: blur(10px);
}

.dropdown-menu-right {
    left: auto;
    right: 0;
    min-width: 200px;
}

@media (min-width: 769px) {
    .dropdown:hover .dropdown-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
}

.dropdown.active .dropdown-menu {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

/* ========================================
   DROPDOWN ITEMS
   ======================================== */
.dropdown-header {
    padding: 10px 15px;
    font-size: 12px;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.7);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.dropdown-divider {
    height: 1px;
    background: rgba(255, 255, 255, 0.1);
    margin: 0.5rem 0;
}

.dropdown-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 15px;
    color: var(--text-secondary);
    text-decoration: none;
    font-size: 14px;
    transition: all 0.3s ease;
    cursor: pointer;
    background: transparent;
    border: none;
    width: 100%;
    text-align: left;
    font-family: inherit;
}

.dropdown-item i {
    font-size: 16px;
    width: 20px;
    text-align: center;
    color: rgba(255, 255, 255, 0.8);
    transition: all 0.3s ease;
}

.dropdown-item:hover {
    color: var(--accent-red);
    background: var(--hover-bg);
}

.dropdown-item:hover i {
    color: var(--accent-red);
    transform: scale(1.1);
}

.dropdown-item.active {
    color: var(--accent-red);
    background: var(--active-bg);
}

.dropdown-item.active i {
    color: var(--accent-red);
}

/* ========================================
   DEPARTMENT INFO
   ======================================== */
.dept-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.dept-code {
    font-weight: 600;
    font-size: 14px;
    color: var(--text-primary);
}

.dept-name {
    font-size: 12px;
    color: rgba(255, 255, 255, 0.7);
}

/* ========================================
   LOGOUT BUTTON
   ======================================== */
.dropdown-item.logout {
    color: #fca5a5;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    margin-top: 8px;
    padding-top: 12px;
}

.dropdown-item.logout:hover {
    color: #ef4444;
    background: rgba(239, 68, 68, 0.1);
}

.dropdown-item.logout i {
    color: #fca5a5;
}

/* ========================================
   MOBILE OVERLAY
   ======================================== */
.mobile-overlay {
    display: none;
    position: fixed;
    top: 60px;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(4px);
    z-index: 998;
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
}

.mobile-overlay.active {
    display: block;
    opacity: 1;
    pointer-events: auto;
}

/* ========================================
   MOBILE RESPONSIVE
   ======================================== */
@media (max-width: 768px) {
    .navbar {
        padding: 8px 15px;
    }

    .nav-logo {
        font-size: 16px;
    }

    .nav-logo img {
        width: 28px;
        height: 28px;
    }

    .mobile-toggle {
        display: block;
    }

    .nav-content {
        position: fixed;
        top: 60px;
        left: 0;
        right: 0;
        bottom: 0;
        background: var(--dropdown-bg);
        flex-direction: column;
        padding: 0;
        gap: 0;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
        border-bottom: 1px solid var(--border-color);
        z-index: 999;
        overflow-y: auto;
        align-items: stretch;
        height: calc(100vh - 60px);
        justify-content: flex-start;
    }

    .nav-content.active {
        transform: translateX(0);
    }

    .nav-btn {
        width: 100%;
        justify-content: center;
        padding: 18px 20px;
        font-size: 16px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        background: transparent;
    }

    .nav-btn::after {
        display: none;
    }

    .nav-btn:active {
        background: var(--active-bg);
        color: var(--accent-red);
    }

    .dropdown {
        width: 100%;
    }

    .dropdown-btn {
        width: 100%;
        justify-content: space-between;
        padding: 18px 20px;
        font-size: 16px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .dropdown.active .dropdown-btn {
        background: var(--active-bg);
        color: var(--accent-red);
    }

    .dropdown-menu {
        position: static;
        opacity: 1;
        visibility: visible;
        transform: none;
        box-shadow: none;
        border: none;
        border-radius: 0;
        background: rgba(0, 0, 0, 0.3);
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
        margin: 0;
        min-width: 100%;
    }

    .dropdown.active .dropdown-menu {
        max-height: 500px;
    }

    .dropdown-header {
        background: rgba(0, 0, 0, 0.3);
    }

    .dropdown-item {
        padding: 12px 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .dropdown-item:last-child {
        border-bottom: none;
    }

    #userDropdown {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        margin-top: 0;
    }

    .user-btn {
        border-bottom: none;
    }

    .user-profile-pic,
    .user-profile-initials {
        width: 28px;
        height: 28px;
        font-size: 12px;
    }

    /* Mobile Scrollbar */
    .nav-content::-webkit-scrollbar {
        width: 4px;
    }

    .nav-content::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.2);
    }

    .nav-content::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 2px;
    }

    .nav-content::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.3);
    }
}

/* Extra small devices */
@media (max-width: 480px) {
    .navbar {
        padding: 8px 12px;
    }

    .nav-logo {
        font-size: 14px;
    }

    .nav-logo img {
        width: 24px;
        height: 24px;
    }

    .mobile-toggle {
        font-size: 20px;
        padding: 6px;
    }

    .nav-btn,
    .dropdown-btn {
        padding: 14px 16px;
        font-size: 14px;
    }

    .dropdown-item {
        padding: 12px 16px;
        font-size: 13px;
    }

    .user-profile-pic,
    .user-profile-initials {
        width: 24px;
        height: 24px;
        font-size: 11px;
    }
}

/* Landscape orientation fix */
@media (max-width: 768px) and (orientation: landscape) {
    .nav-content {
        top: 50px;
        height: calc(100vh - 50px);
    }

    .mobile-overlay {
        top: 50px;
    }

    .nav-btn,
    .dropdown-btn {
        padding: 12px 16px;
    }

    .dropdown-item {
        padding: 10px 16px;
    }
}

/* Print Styles */
@media print {
    .navbar {
        display: none;
    }
}
</style>

<script>
// Navigation JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.getElementById('navbar');
    const mobileToggle = document.getElementById('mobileToggle');
    const navContent = document.getElementById('navContent');
    const mobileOverlay = document.getElementById('mobileOverlay');
    const dropdowns = document.querySelectorAll('.dropdown');

    // Scroll effect
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    // Mobile menu toggle
    mobileToggle?.addEventListener('click', function() {
        navContent.classList.toggle('active');
        mobileOverlay.classList.toggle('active');
    });

    // Close mobile menu when clicking overlay
    mobileOverlay?.addEventListener('click', function() {
        navContent.classList.remove('active');
        mobileOverlay.classList.remove('active');
        dropdowns.forEach(dropdown => dropdown.classList.remove('active'));
    });

    // Dropdown functionality for mobile
    dropdowns.forEach(dropdown => {
        const btn = dropdown.querySelector('.dropdown-btn');
        
        btn?.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                e.preventDefault();
                
                // Close other dropdowns
                dropdowns.forEach(d => {
                    if (d !== dropdown) {
                        d.classList.remove('active');
                    }
                });
                
                // Toggle current dropdown
                dropdown.classList.toggle('active');
            }
        });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown') && window.innerWidth <= 768) {
            dropdowns.forEach(dropdown => dropdown.classList.remove('active'));
        }
    });

    // Close mobile menu on window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            navContent.classList.remove('active');
            mobileOverlay.classList.remove('active');
            dropdowns.forEach(dropdown => dropdown.classList.remove('active'));
        }
    });
});
</script>