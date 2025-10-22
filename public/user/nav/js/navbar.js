// Enhanced Navigation JavaScript with Mobile Toggle - Fixed Version

// Initialize navbar functionality
document.addEventListener('DOMContentLoaded', function() {
    initializeNavbar();
    addSmoothAnimations();
    handleResponsiveBehavior();
});

// Main navbar initialization
function initializeNavbar() {
    createMobileToggle();
    createMobileOverlay();
    setupEventListeners();
}

// Create mobile toggle button
function createMobileToggle() {
    const navContainer = document.querySelector('.nav-container');
    const existingToggle = document.querySelector('.mobile-toggle');
    
    if (!existingToggle && navContainer) {
        const toggleBtn = document.createElement('button');
        toggleBtn.className = 'mobile-toggle';
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
    
    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(handleResize, 250);
    });
    
    // Handle escape key
    document.addEventListener('keydown', handleEscapeKey);
    
    // Prevent dropdown menu clicks from closing on mobile
    const dropdownMenus = document.querySelectorAll('.dropdown-menu');
    dropdownMenus.forEach(menu => {
        menu.addEventListener('click', function(e) {
            const isMobile = window.innerWidth <= 768;
            // Allow clicks on links to go through
            if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON') {
                if (isMobile) {
                    // Close mobile menu after clicking a link
                    setTimeout(() => {
                        closeMobileMenu();
                    }, 300);
                }
            }
        });
    });
}

// Toggle mobile menu
function toggleMobileMenu() {
    const navContent = document.querySelector('.nav-content');
    const overlay = document.querySelector('.mobile-overlay');
    const toggleBtn = document.querySelector('.mobile-toggle');
    const body = document.body;
    
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
    body.style.overflow = 'hidden';
    
    // Update toggle icon
    const icon = toggleBtn?.querySelector('i');
    if (icon) {
        icon.className = 'fas fa-times';
    }
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
    body.style.overflow = '';
    
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
                this.style.transform = 'translateY(-2px)';
            }
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.transform = '';
        });
    });
    
    // Add ripple effect
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
                background: rgba(255, 255, 255, 0.3);
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
    
    // Add ripple animation to CSS
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
            
            @keyframes dropdownEnter {
                from {
                    opacity: 0;
                    transform: translateY(-10px) scale(0.95);
                }
                to {
                    opacity: 1;
                    transform: translateY(0) scale(1);
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
    });
}

// Export functions for external use
window.navbarUtils = {
    toggleMobileMenu,
    closeMobileMenu,
    openMobileMenu,
    closeAllDropdowns
};