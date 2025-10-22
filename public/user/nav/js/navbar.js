// Enhanced Navigation JavaScript for MCC E&PO - Complete Version

// Initialize navbar functionality
document.addEventListener('DOMContentLoaded', function() {
    initializeNavbar();
    initializeScrollBehavior();
    addSmoothAnimations();
    handleResponsiveBehavior();
});

// Main navbar initialization
function initializeNavbar() {
    createMobileToggle();
    createMobileOverlay();
    setupEventListeners();
}

// Navbar show/hide on scroll
function initializeScrollBehavior() {
    const navbar = document.getElementById('navbar');
    let lastScroll = 0;

    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;
        
        if (currentScroll > 200) {
            navbar?.classList.add('scrolled');
        } else {
            navbar?.classList.remove('scrolled');
        }
        
        lastScroll = currentScroll;
    });
}

// Create mobile toggle button
function createMobileToggle() {
    const navContainer = document.querySelector('.nav-container');
    const existingToggle = document.querySelector('.mobile-toggle');
    
    if (!existingToggle && navContainer) {
        const toggleBtn = document.createElement('button');
        toggleBtn.className = 'mobile-toggle';
        toggleBtn.id = 'mobileToggle';
        toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
        toggleBtn.setAttribute('aria-label', 'Toggle navigation menu');
        
        // Insert after nav-brand
        const navBrand = document.querySelector('.nav-brand');
        if (navBrand) {
            navBrand.insertAdjacentElement('afterend', toggleBtn);
        }
    }
}

// Create mobile overlay
function createMobileOverlay() {
    const existingOverlay = document.querySelector('.mobile-overlay');
    
    if (!existingOverlay) {
        const overlay = document.createElement('div');
        overlay.className = 'mobile-overlay';
        document.body.appendChild(overlay);
    }
}

// Setup all event listeners
function setupEventListeners() {
    // Mobile toggle functionality
    const mobileToggle = document.querySelector('.mobile-toggle');
    if (mobileToggle) {
        mobileToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleMobileMenu();
        });
    }
    
    // Dropdown functionality - works for both mobile and desktop
    const dropdownBtns = document.querySelectorAll('.dropdown-btn');
    dropdownBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            const dropdown = btn.closest('.dropdown');
            const isMobile = window.innerWidth <= 768;
            
            if (isMobile) {
                toggleMobileDropdown(dropdown);
            } else {
                toggleDesktopDropdown(dropdown);
            }
        });
    });
    
    // Close dropdowns when clicking outside (desktop only)
    document.addEventListener('click', function(e) {
        const isMobile = window.innerWidth <= 768;
        if (!isMobile) {
            const clickedInsideDropdown = e.target.closest('.dropdown');
            if (!clickedInsideDropdown) {
                closeAllDropdowns();
            }
        }
    });
    
    // Mobile overlay click
    const overlay = document.querySelector('.mobile-overlay');
    if (overlay) {
        overlay.addEventListener('click', closeMobileMenu);
    }
    
    // Close mobile menu when clicking a nav link
    document.querySelectorAll('.nav-btn').forEach(link => {
        link.addEventListener('click', () => {
            const isMobile = window.innerWidth <= 768;
            if (isMobile) {
                setTimeout(() => {
                    closeMobileMenu();
                }, 300);
            }
        });
    });
    
    // Close mobile menu when clicking dropdown items
    document.querySelectorAll('.dropdown-item').forEach(item => {
        item.addEventListener('click', () => {
            const isMobile = window.innerWidth <= 768;
            if (isMobile && (item.tagName === 'A' || item.tagName === 'BUTTON')) {
                setTimeout(() => {
                    closeMobileMenu();
                }, 300);
            }
        });
    });
    
    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(handleResize, 250);
    });
    
    // Handle escape key
    document.addEventListener('keydown', handleEscapeKey);
    
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
}

// Toggle mobile menu
function toggleMobileMenu() {
    const navContent = document.querySelector('.nav-content');
    const overlay = document.querySelector('.mobile-overlay');
    const toggleBtn = document.querySelector('.mobile-toggle');
    
    if (!navContent) return;
    
    const isOpen = navContent.classList.contains('show');
    
    if (isOpen) {
        closeMobileMenu();
    } else {
        openMobileMenu();
    }
}

// Open mobile menu
function openMobileMenu() {
    const navContent = document.querySelector('.nav-content');
    const overlay = document.querySelector('.mobile-overlay');
    const toggleBtn = document.querySelector('.mobile-toggle');
    const body = document.body;
    
    navContent?.classList.add('show');
    overlay?.classList.add('show');
    toggleBtn?.classList.add('active');
    
    // Prevent body scroll and save scroll position
    const scrollY = window.scrollY;
    body.style.position = 'fixed';
    body.style.top = `-${scrollY}px`;
    body.style.width = '100%';
    body.classList.add('menu-open');
    
    // Store scroll position
    body.setAttribute('data-scroll-position', scrollY.toString());
    
    // Update toggle icon
    const icon = toggleBtn?.querySelector('i');
    if (icon) {
        icon.className = 'fas fa-times';
    }
    
    // Add smooth fade-in effect
    requestAnimationFrame(() => {
        navContent?.style.setProperty('opacity', '1');
    });
}

// Close mobile menu
function closeMobileMenu() {
    const navContent = document.querySelector('.nav-content');
    const overlay = document.querySelector('.mobile-overlay');
    const toggleBtn = document.querySelector('.mobile-toggle');
    const body = document.body;
    
    navContent?.classList.remove('show');
    overlay?.classList.remove('show');
    toggleBtn?.classList.remove('active');
    
    // Restore body scroll
    const scrollY = body.getAttribute('data-scroll-position');
    body.style.position = '';
    body.style.top = '';
    body.style.width = '';
    body.classList.remove('menu-open');
    
    if (scrollY) {
        window.scrollTo(0, parseInt(scrollY));
    }
    
    // Update toggle icon back to bars
    const icon = toggleBtn?.querySelector('i');
    if (icon) {
        icon.className = 'fas fa-bars';
    }
    
    // Close all dropdowns when closing mobile menu
    closeAllDropdowns();
}

// Toggle mobile dropdown
function toggleMobileDropdown(dropdown) {
    if (!dropdown) return;
    
    const menu = dropdown.querySelector('.dropdown-menu');
    const isOpen = dropdown.classList.contains('show');
    
    // Close all other dropdowns first
    document.querySelectorAll('.dropdown').forEach(d => {
        if (d !== dropdown) {
            const m = d.querySelector('.dropdown-menu');
            m?.classList.remove('show');
            d.classList.remove('show');
        }
    });
    
    // Toggle current dropdown
    if (!isOpen) {
        menu?.classList.add('show');
        dropdown.classList.add('show');
    } else {
        menu?.classList.remove('show');
        dropdown.classList.remove('show');
    }
}

// Toggle desktop dropdown (hover-based with click support)
function toggleDesktopDropdown(dropdown) {
    if (!dropdown) return;
    
    const menu = dropdown.querySelector('.dropdown-menu');
    const isOpen = menu?.classList.contains('show');
    
    // Close all other dropdowns
    document.querySelectorAll('.dropdown').forEach(d => {
        if (d !== dropdown) {
            const m = d.querySelector('.dropdown-menu');
            m?.classList.remove('show');
            d.classList.remove('show');
        }
    });
    
    // Toggle current dropdown
    if (!isOpen) {
        menu?.classList.add('show');
        dropdown.classList.add('show');
    } else {
        menu?.classList.remove('show');
        dropdown.classList.remove('show');
    }
}

// Close all dropdowns
function closeAllDropdowns() {
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        menu.classList.remove('show');
    });
    document.querySelectorAll('.dropdown').forEach(dropdown => {
        dropdown.classList.remove('show');
    });
}

// Handle window resize
function handleResize() {
    const isMobile = window.innerWidth <= 768;
    
    if (!isMobile) {
        closeMobileMenu();
        // Reset mobile-specific dropdown states
        closeAllDropdowns();
    }
}

// Handle escape key
function handleEscapeKey(e) {
    if (e.key === 'Escape') {
        const navContent = document.querySelector('.nav-content');
        const isMenuOpen = navContent?.classList.contains('show');
        
        if (isMenuOpen) {
            closeMobileMenu();
        } else {
            closeAllDropdowns();
        }
    }
}

// Add smooth animations and hover effects
function addSmoothAnimations() {
    // Enhanced hover effects for navigation elements (desktop only)
    const navBtns = document.querySelectorAll('.nav-btn');
    navBtns.forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            if (window.innerWidth > 768) {
                this.style.transition = 'all 0.3s ease';
            }
        });
    });
    
    // Add ripple effect to interactive elements
    addRippleEffect();
}

// Add ripple effect to interactive elements
function addRippleEffect() {
    const rippleElements = document.querySelectorAll('.nav-btn, .dropdown-btn, .dropdown-item');
    
    rippleElements.forEach(element => {
        element.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) return; // Skip on mobile
            
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
                position: absolute;
                border-radius: 50%;
                background: rgba(229, 62, 62, 0.3);
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                animation: ripple 0.6s linear;
                pointer-events: none;
                z-index: 1;
            `;
            
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
    
    // Add ripple animation to CSS if not exists
    if (!document.querySelector('#ripple-styles')) {
        const style = document.createElement('style');
        style.id = 'ripple-styles';
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(2);
                    opacity: 0;
                }
            }
            
            @keyframes slideDown {
                from {
                    transform: translateY(-100%);
                    opacity: 0;
                }
                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }
        `;
        document.head.appendChild(style);
    }
}

// Handle responsive behavior changes
function handleResponsiveBehavior() {
    // Add ARIA labels and roles
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        navbar.setAttribute('role', 'navigation');
        navbar.setAttribute('aria-label', 'Main navigation');
    }
    
    // Improve touch interactions for mobile
    if ('ontouchstart' in window) {
        const navContent = document.querySelector('.nav-content');
        let touchStartY = 0;
        let touchEndY = 0;
        
        navContent?.addEventListener('touchstart', function(e) {
            touchStartY = e.changedTouches[0].screenY;
        }, { passive: true });
        
        navContent?.addEventListener('touchend', function(e) {
            touchEndY = e.changedTouches[0].screenY;
            handleSwipeGesture();
        }, { passive: true });
        
        function handleSwipeGesture() {
            // Close menu on swipe left
            if (touchStartY - touchEndY > 50) {
                // Swipe up - do nothing
            } else if (touchEndY - touchStartY > 50) {
                // Swipe down - do nothing
            } else if (touchStartX - touchEndX > 100) {
                // Swipe left - close menu
                if (window.innerWidth <= 768) {
                    closeMobileMenu();
                }
            }
        }
    }
    
    // Add keyboard navigation support
    document.addEventListener('keydown', function(e) {
        const activeElement = document.activeElement;
        
        // Handle arrow key navigation in dropdowns
        if (activeElement && activeElement.classList.contains('dropdown-item')) {
            const dropdown = activeElement.closest('.dropdown-menu');
            const items = dropdown.querySelectorAll('.dropdown-item');
            const currentIndex = Array.from(items).indexOf(activeElement);
            
            if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                e.preventDefault();
                let nextIndex;
                
                if (e.key === 'ArrowDown') {
                    nextIndex = currentIndex < items.length - 1 ? currentIndex + 1 : 0;
                } else {
                    nextIndex = currentIndex > 0 ? currentIndex - 1 : items.length - 1;
                }
                
                items[nextIndex].focus();
            }
        }
        
        // Handle Enter key on dropdown buttons
        if (e.key === 'Enter' && activeElement?.classList.contains('dropdown-btn')) {
            e.preventDefault();
            activeElement.click();
        }
    });
    
    // Add focus styles for keyboard navigation
    const style = document.createElement('style');
    style.textContent = `
        .nav-btn:focus,
        .dropdown-btn:focus,
        .dropdown-item:focus {
            outline: 2px solid #e53e3e;
            outline-offset: 2px;
        }
        
        .nav-btn:focus-visible,
        .dropdown-btn:focus-visible,
        .dropdown-item:focus-visible {
            outline: 2px solid #e53e3e;
            outline-offset: 2px;
        }
        
        /* Smooth scroll behavior */
        html {
            scroll-behavior: smooth;
        }
        
        /* Improve text rendering on mobile */
        @media (max-width: 768px) {
            .nav-btn,
            .dropdown-btn,
            .dropdown-item {
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
                text-rendering: optimizeLegibility;
            }
        }
        
        /* Add haptic feedback effect */
        @media (max-width: 768px) {
            .nav-btn:active,
            .dropdown-btn:active,
            .dropdown-item:active {
                transform: scale(0.98);
            }
        }
    `;
    document.head.appendChild(style);
    
    // Add viewport meta tag if not exists
    if (!document.querySelector('meta[name="viewport"]')) {
        const meta = document.createElement('meta');
        meta.name = 'viewport';
        meta.content = 'width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes, viewport-fit=cover';
        document.head.appendChild(meta);
    }
}

// Export functions for external use
window.navbarUtils = {
    toggleMobileMenu,
    closeMobileMenu,
    openMobileMenu,
    closeAllDropdowns,
    initializeNavbar
};

// Log initialization
console.log('MCC E&PO Navigation initialized successfully');