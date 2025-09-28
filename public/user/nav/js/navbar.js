// Enhanced Navigation JavaScript with Mobile Toggle

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
        mobileToggle.addEventListener('click', toggleMobileMenu);
    }
    
    // Desktop dropdown functionality
    const dropdownBtns = document.querySelectorAll('.dropdown-btn');
    dropdownBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const dropdown = btn.closest('.dropdown');
            toggleDropdown(dropdown);
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', closeAllDropdowns);
    
    // Mobile overlay click
    const overlay = document.querySelector('.mobile-overlay');
    if (overlay) {
        overlay.addEventListener('click', closeMobileMenu);
    }
    
    // Handle window resize
    window.addEventListener('resize', handleResize);
    
    // Handle escape key
    document.addEventListener('keydown', handleEscapeKey);
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
    
    // Close any open dropdowns first
    closeAllDropdowns();
    
    // Add animation delay for better UX
    setTimeout(() => {
        navContent?.style.setProperty('--nav-delay', '0.1s');
    }, 100);
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
    
    // Close all dropdowns when closing mobile menu
    closeAllDropdowns();
}

// Enhanced dropdown toggle functionality
function toggleDropdown(dropdown) {
    if (!dropdown) return;
    
    const menu = dropdown.querySelector('.dropdown-menu');
    const isOpen = menu?.classList.contains('show');
    const isMobile = window.innerWidth <= 768;
    
    // Close all other dropdowns first
    document.querySelectorAll('.dropdown').forEach(d => {
        if (d !== dropdown) {
            const m = d.querySelector('.dropdown-menu');
            m?.classList.remove('show');
            d.classList.remove('show');
        }
    });
    
    // Toggle current dropdown
    if (!isOpen && menu) {
        menu.classList.add('show');
        dropdown.classList.add('show');
        
        // Add entrance animation
        if (!isMobile) {
            menu.style.animation = 'dropdownEnter 0.3s ease';
        }
        
        // Focus management for accessibility
        const firstItem = menu.querySelector('.dropdown-item');
        if (firstItem && !isMobile) {
            setTimeout(() => firstItem.focus(), 100);
        }
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
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.style.maxHeight = '';
        });
    } else {
        // Close desktop dropdowns on mobile
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
    // Enhanced hover effects for navigation elements
    const navBtns = document.querySelectorAll('.nav-btn');
    navBtns.forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            if (window.innerWidth > 768) {
                this.style.transform = 'translateY(-2px) scale(1.02)';
            }
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.transform = '';
        });
    });
    
    // Enhanced hover effects for dropdown buttons
    const dropdownBtns = document.querySelectorAll('.dropdown-btn');
    dropdownBtns.forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            if (window.innerWidth > 768) {
                this.style.transform = 'translateY(-2px) scale(1.02)';
            }
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.transform = '';
        });
    });
    
    // Add ripple effect to buttons
    addRippleEffect();
    
    // Add loading state to nav items
    addLoadingStates();
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
                background: rgba(255, 255, 255, 0.5);
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

// Add loading states for navigation items
function addLoadingStates() {
    const navLinks = document.querySelectorAll('.nav-btn, .dropdown-item[href]');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Skip if it's a dropdown toggle or external link
            if (this.classList.contains('dropdown-btn') || 
                this.getAttribute('href')?.startsWith('http') ||
                this.getAttribute('href')?.startsWith('mailto:')) {
                return;
            }
            
            // Add loading state
            const originalContent = this.innerHTML;
            const loadingSpinner = '<i class="fas fa-spinner fa-spin"></i>';
            
            // Only add loading state if it's not a form submission
            if (!this.closest('form')) {
                this.innerHTML = loadingSpinner + ' Loading...';
                this.style.pointerEvents = 'none';
                
                // Reset after a delay (in case navigation is cancelled)
                setTimeout(() => {
                    this.innerHTML = originalContent;
                    this.style.pointerEvents = '';
                }, 3000);
            }
        });
    });
}

// Handle responsive behavior changes
function handleResponsiveBehavior() {
    let resizeTimer;
    
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            const isMobile = window.innerWidth <= 768;
            const navContent = document.querySelector('.nav-content');
            
            // Smooth transition between desktop and mobile layouts
            if (navContent) {
                navContent.style.transition = 'all 0.3s ease';
            }
            
            // Update mobile toggle visibility
            const mobileToggle = document.querySelector('.mobile-toggle');
            if (mobileToggle) {
                mobileToggle.style.display = isMobile ? 'block' : 'none';
            }
            
        }, 250);
    });
}

// Accessibility improvements
document.addEventListener('DOMContentLoaded', function() {
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
});

// Export functions for external use if needed
window.navbarUtils = {
    toggleDropdown,
    closeMobileMenu,
    openMobileMenu,
    closeAllDropdowns
};