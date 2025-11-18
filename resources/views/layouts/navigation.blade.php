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

<!-- ✨ LOADING ANIMATION OVERLAY -->
<div class="page-loader" id="pageLoader">
    <div class="loader-content">
        <div class="loader-logo">
            <img src="{{ asset('images/logo.png') }}" alt="MCC Logo">
        </div>
        <div class="loader-spinner">
            <div class="spinner-ring"></div>
            <div class="spinner-ring"></div>
            <div class="spinner-ring"></div>
        </div>
        <div class="loader-text">Loading...</div>
    </div>
</div>

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
   PREVENT ALL TRANSITION GLITCHES ON LOAD
   ======================================== */
* {
    -webkit-transition: none !important;
    -moz-transition: none !important;
    -o-transition: none !important;
    transition: none !important;
}

body.loaded * {
    -webkit-transition: all 0.3s ease !important;
    -moz-transition: all 0.3s ease !important;
    -o-transition: all 0.3s ease !important;
    transition: all 0.3s ease !important;
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
}

body.loaded .navbar {
    transition: all 0.3s ease;
}

body.loaded .navbar.scrolled {
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
   LOGO - AGGRESSIVE FIX FOR ALL GLITCHING
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
    z-index: 1002;
    /* CRITICAL FIXES - NO TRANSITIONS ON LOGO */
    transition: none !important;
    will-change: auto;
    transform: translate3d(0, 0, 0);
    backface-visibility: hidden;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

.nav-logo * {
    transition: none !important;
}

body.loaded .nav-logo:hover {
    transform: scale(1.05) translate3d(0, 0, 0);
}

.nav-logo img {
    width: 32px;
    height: 32px;
    border-radius: 4px;
    /* AGGRESSIVE IMAGE FIXES */
    display: block;
    image-rendering: -webkit-optimize-contrast;
    image-rendering: crisp-edges;
    transform: translate3d(0, 0, 0);
    backface-visibility: hidden;
    -webkit-backface-visibility: hidden;
    transition: none !important;
    /* PREVENT ANY LAYOUT SHIFT */
    min-width: 32px;
    min-height: 32px;
    max-width: 32px;
    max-height: 32px;
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
    padding: 8px;
    z-index: 1002;
    transition: none !important;
}

body.loaded .mobile-toggle:hover {
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
}

.nav-btn::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0;
    height: 2px;
    background: var(--accent-red);
}

body.loaded .nav-btn:hover,
body.loaded .dropdown-btn:hover {
    color: var(--accent-red);
}

body.loaded .nav-btn:hover i,
body.loaded .dropdown-btn:hover i {
    transform: scale(1.1);
}

body.loaded .nav-btn:hover::after {
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
    display: block;
    /* PREVENT IMAGE GLITCHING */
    transform: translate3d(0, 0, 0);
    backface-visibility: hidden;
    -webkit-backface-visibility: hidden;
    transition: none !important;
    min-width: 32px;
    min-height: 32px;
    max-width: 32px;
    max-height: 32px;
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
    transition: none !important;
}

.user-btn {
    padding: 4px 0;
}

body.loaded .user-btn:hover .user-profile-pic,
body.loaded .user-btn:hover .user-profile-initials {
    transform: scale(1.1) translate3d(0, 0, 0);
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
}

body.loaded .dropdown.active .dropdown-arrow {
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
    z-index: 1001;
    backdrop-filter: blur(10px);
}

.dropdown-menu-right {
    left: auto;
    right: 0;
    min-width: 200px;
}

@media (min-width: 769px) {
    body.loaded .dropdown:hover .dropdown-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
}

body.loaded .dropdown.active .dropdown-menu {
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
}

body.loaded .dropdown-item:hover {
    color: var(--accent-red);
    background: var(--hover-bg);
}

body.loaded .dropdown-item:hover i {
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

body.loaded .dropdown-item.logout:hover {
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
    pointer-events: none;
}

body.loaded .mobile-overlay.active {
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
        border-bottom: 1px solid var(--border-color);
        z-index: 999;
        overflow-y: auto;
        align-items: stretch;
        height: calc(100vh - 60px);
        justify-content: flex-start;
    }

    body.loaded .nav-content {
        transition: transform 0.3s ease;
    }

    body.loaded .nav-content.active {
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

    body.loaded .nav-btn:active {
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

    body.loaded .dropdown.active .dropdown-btn {
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
        margin: 0;
        min-width: 100%;
    }

    body.loaded .dropdown-menu {
        transition: max-height 0.3s ease;
    }

    body.loaded .dropdown.active .dropdown-menu {
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

/* ========================================
   LOADING ANIMATION - BEAUTIFUL LOADER
   ======================================== */
.page-loader {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #5e2a84 0%, #3d1a5f 50%, #2c0e44 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    opacity: 1;
    visibility: visible;
    transition: opacity 0.6s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.page-loader.hidden {
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
}

.loader-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2rem;
    animation: fadeInUp 0.6s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Loader Logo */
.loader-logo {
    position: relative;
    width: 120px;
    height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: pulse 2s ease-in-out infinite;
}

.loader-logo::before {
    content: '';
    position: absolute;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(229, 62, 62, 0.3) 0%, transparent 70%);
    border-radius: 50%;
    animation: ripple 2s ease-out infinite;
}

.loader-logo img {
    width: 100px;
    height: 100px;
    object-fit: contain;
    filter: drop-shadow(0 0 20px rgba(229, 62, 62, 0.5));
    animation: rotate3d 3s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
}

@keyframes ripple {
    0% {
        transform: scale(0.8);
        opacity: 1;
    }
    100% {
        transform: scale(1.5);
        opacity: 0;
    }
}

@keyframes rotate3d {
    0% {
        transform: rotateY(0deg);
    }
    50% {
        transform: rotateY(180deg);
    }
    100% {
        transform: rotateY(360deg);
    }
}

/* Loader Spinner */
.loader-spinner {
    position: relative;
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.spinner-ring {
    position: absolute;
    width: 100%;
    height: 100%;
    border: 3px solid transparent;
    border-top-color: #e53e3e;
    border-radius: 50%;
    animation: spin 1.5s cubic-bezier(0.68, -0.55, 0.265, 1.55) infinite;
}

.spinner-ring:nth-child(2) {
    width: 70%;
    height: 70%;
    border-top-color: #f56565;
    animation-delay: -0.3s;
}

.spinner-ring:nth-child(3) {
    width: 40%;
    height: 40%;
    border-top-color: #fc8181;
    animation-delay: -0.6s;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}

/* Loader Text */
.loader-text {
    font-size: 1.25rem;
    font-weight: 600;
    color: #ffffff;
    text-transform: uppercase;
    letter-spacing: 3px;
    animation: textPulse 1.5s ease-in-out infinite;
    font-family: 'Oswald', sans-serif;
    text-shadow: 0 0 20px rgba(229, 62, 62, 0.5);
}

@keyframes textPulse {
    0%, 100% {
        opacity: 0.6;
    }
    50% {
        opacity: 1;
    }
}

/* Loading dots animation */
.loader-text::after {
    content: '';
    animation: dots 1.5s steps(4, end) infinite;
}

@keyframes dots {
    0%, 20% {
        content: '';
    }
    40% {
        content: '.';
    }
    60% {
        content: '..';
    }
    80%, 100% {
        content: '...';
    }
}

/* Mobile Loader Adjustments */
@media (max-width: 768px) {
    .loader-logo {
        width: 100px;
        height: 100px;
    }
    
    .loader-logo img {
        width: 80px;
        height: 80px;
    }
    
    .loader-spinner {
        width: 60px;
        height: 60px;
    }
    
    .loader-text {
        font-size: 1rem;
        letter-spacing: 2px;
    }
}

@media (max-width: 480px) {
    .loader-logo {
        width: 80px;
        height: 80px;
    }
    
    .loader-logo img {
        width: 60px;
        height: 60px;
    }
    
    .loader-spinner {
        width: 50px;
        height: 50px;
    }
    
    .loader-text {
        font-size: 0.9rem;
        letter-spacing: 1.5px;
    }
    
    .loader-content {
        gap: 1.5rem;
    }
}
</style>

<script>
// Navigation JavaScript - WITH LOADING ON ALL NAVIGATION ACTIONS
(function() {
    'use strict';
    
    // Get loader element
    const pageLoader = document.getElementById('pageLoader');
    
    // Show loader immediately on page load
    if (pageLoader) {
        pageLoader.style.display = 'flex';
    }
    
    // Hide page content initially
    document.documentElement.style.visibility = 'hidden';
    
    function hideLoader() {
        if (pageLoader) {
            // Add hidden class for smooth fade out
            pageLoader.classList.add('hidden');
            
            // Remove from DOM after animation completes (smooth timing)
            setTimeout(() => {
                pageLoader.style.display = 'none';
            }, 600);
        }
        
        // Show page content with smooth transition
        setTimeout(() => {
            document.documentElement.style.visibility = 'visible';
            document.body.classList.add('loaded');
        }, 100);
    }
    
    function showLoader() {
        if (pageLoader) {
            pageLoader.classList.remove('hidden');
            pageLoader.style.display = 'flex';
            // Force reflow for smooth animation
            void pageLoader.offsetWidth;
        }
    }
    
    // Wait for complete page load including images
    function waitForPageLoad() {
        // Check if page is already loaded
        if (document.readyState === 'complete') {
            initNavigation();
            // Smooth hide after everything is ready
            setTimeout(() => {
                hideLoader();
            }, 600);
        } else {
            // Wait for full page load
            window.addEventListener('load', function() {
                initNavigation();
                setTimeout(() => {
                    hideLoader();
                }, 600);
            });
        }
    }
    
    // Initialize
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', waitForPageLoad);
    } else {
        waitForPageLoad();
    }
    
    function initNavigation() {
        const navbar = document.getElementById('navbar');
        const mobileToggle = document.getElementById('mobileToggle');
        const navContent = document.getElementById('navContent');
        const mobileOverlay = document.getElementById('mobileOverlay');
        const dropdowns = document.querySelectorAll('.dropdown');
        
        // Check if elements exist
        if (!navbar || !navContent) {
            console.warn('Navigation elements not found');
            return;
        }
        
        // ========================================
        // ✨ SHOW LOADER ON ALL LINK CLICKS
        // ========================================
        const allLinks = document.querySelectorAll('a[href]:not([target="_blank"]):not([href^="#"]):not([href^="javascript:"])');
        
        allLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                
                // Only show loader for internal navigation (not external or hash links)
                if (href && !href.startsWith('http') && !href.startsWith('//') && !href.startsWith('#')) {
                    showLoader();
                }
            });
        });
        
        // ========================================
        // ✨ SHOW LOADER ON FORM SUBMISSIONS
        // ========================================
        const allForms = document.querySelectorAll('form');
        
        allForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                // Show loader on form submit (like logout)
                showLoader();
            });
        });
        
        // Initialize scroll state on page load
        checkScrollPosition();
        
        // Reset mobile menu state on page load
        resetMobileMenu();
        
        // Scroll effect with debouncing
        let scrollTimeout;
        window.addEventListener('scroll', function() {
            if (scrollTimeout) {
                window.cancelAnimationFrame(scrollTimeout);
            }
            scrollTimeout = window.requestAnimationFrame(checkScrollPosition);
        }, { passive: true });
        
        function checkScrollPosition() {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        }
        
        // Mobile menu toggle
        if (mobileToggle) {
            mobileToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleMobileMenu();
            });
        }
        
        function toggleMobileMenu() {
            const isActive = navContent.classList.contains('active');
            
            if (isActive) {
                closeMobileMenu();
            } else {
                openMobileMenu();
            }
        }
        
        function openMobileMenu() {
            navContent.classList.add('active');
            if (mobileOverlay) {
                mobileOverlay.classList.add('active');
            }
            document.body.style.overflow = 'hidden';
        }
        
        function closeMobileMenu() {
            navContent.classList.remove('active');
            if (mobileOverlay) {
                mobileOverlay.classList.remove('active');
            }
            document.body.style.overflow = '';
            
            // Close all dropdowns when closing mobile menu
            dropdowns.forEach(dropdown => dropdown.classList.remove('active'));
        }
        
        function resetMobileMenu() {
            navContent.classList.remove('active');
            if (mobileOverlay) {
                mobileOverlay.classList.remove('active');
            }
            document.body.style.overflow = '';
            dropdowns.forEach(dropdown => dropdown.classList.remove('active'));
        }
        
        // Close mobile menu when clicking overlay
        if (mobileOverlay) {
            mobileOverlay.addEventListener('click', closeMobileMenu);
        }
        
        // Dropdown functionality
        dropdowns.forEach(dropdown => {
            const btn = dropdown.querySelector('.dropdown-btn');
            
            if (btn) {
                btn.addEventListener('click', function(e) {
                    if (window.innerWidth <= 768) {
                        e.preventDefault();
                        e.stopPropagation();
                        
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
            }
        });
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown') && window.innerWidth <= 768) {
                dropdowns.forEach(dropdown => dropdown.classList.remove('active'));
            }
        });
        
        // Handle window resize
        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(handleResize, 150);
        });
        
        function handleResize() {
            if (window.innerWidth > 768) {
                resetMobileMenu();
            }
        }
        
        // Handle page visibility change
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden && window.innerWidth > 768) {
                resetMobileMenu();
            }
        });
        
        // Handle back/forward navigation - SMOOTH TRANSITION
        window.addEventListener('pageshow', function(event) {
            // Always ensure loaded class is present
            document.body.classList.add('loaded');
            
            if (event.persisted) {
                // Page was loaded from cache
                setTimeout(() => {
                    hideLoader();
                    resetMobileMenu();
                    checkScrollPosition();
                }, 400);
            }
        });
        
        // Show loader before page unload - SMOOTH
        let isNavigating = false;
        window.addEventListener('beforeunload', function() {
            if (!isNavigating) {
                isNavigating = true;
                showLoader();
            }
        });
    }
})();
</script>