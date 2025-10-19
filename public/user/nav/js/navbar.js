// Enhanced Navigation JavaScript - Maintaining Full Functionality

document.addEventListener('DOMContentLoaded', function() {
    initializeNavigation();
});

function initializeNavigation() {
    setupScrollEffect();
    setupMobileToggle();
    setupDropdowns();
    setupSmoothScroll();
    setupOverlay();
    setupKeyboardNavigation();
}

// Navbar scroll effect
function setupScrollEffect() {
    const navbar = document.getElementById('navbar');
    if (!navbar) return;

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
}

// Mobile menu toggle
function setupMobileToggle() {
    const mobileToggle = document.getElementById('mobileToggle');
    const navMenu = document.getElementById('navMenu');
    
    if (!mobileToggle || !navMenu) return;

    mobileToggle.addEventListener('click', (e) => {
        e.stopPropagation();
        toggleMobileMenu();
    });

    // Close mobile menu when clicking a non-dropdown link
    const menuLinks = navMenu.querySelectorAll('li > a:not(.dropdown-toggle)');
    menuLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                closeMobileMenu();
            }
        });
    });
}

function toggleMobileMenu() {
    const navMenu = document.getElementById('navMenu');
    const mobileToggle = document.getElementById('mobileToggle');
    const overlay = document.querySelector('.mobile-overlay');
    const icon = mobileToggle?.querySelector('i');
    
    if (!navMenu) return;

    const isActive = navMenu.classList.contains('active');
    
    if (isActive) {
        closeMobileMenu();
    } else {
        openMobileMenu();
    }
}

function openMobileMenu() {
    const navMenu = document.getElementById('navMenu');
    const mobileToggle = document.getElementById('mobileToggle');
    const overlay = document.querySelector('.mobile-overlay');
    const icon = mobileToggle?.querySelector('i');
    
    navMenu?.classList.add('active');
    overlay?.classList.add('show');
    document.body.style.overflow = 'hidden';
    
    if (icon) {
        icon.classList.remove('fa-bars');
        icon.classList.add('fa-times');
    }
}

function closeMobileMenu() {
    const navMenu = document.getElementById('navMenu');
    const mobileToggle = document.getElementById('mobileToggle');
    const overlay = document.querySelector('.mobile-overlay');
    const icon = mobileToggle?.querySelector('i');
    
    navMenu?.classList.remove('active');
    overlay?.classList.remove('show');
    document.body.style.overflow = '';
    
    if (icon) {
        icon.classList.remove('fa-times');
        icon.classList.add('fa-bars');
    }
    
    // Close all dropdowns
    closeAllDropdowns();
}

// Dropdown functionality
function setupDropdowns() {
    const dropdowns = document.querySelectorAll('.dropdown');
    
    dropdowns.forEach(dropdown => {
        const toggle = dropdown.querySelector('.dropdown-toggle');
        const menu = dropdown.querySelector('.dropdown-menu');
        
        if (!toggle || !menu) return;

        // Click handler for dropdown toggle
        toggle.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            const isMobile = window.innerWidth <= 768;
            const isOpen = dropdown.classList.contains('show');
            
            // Close other dropdowns
            closeAllDropdowns(dropdown);
            
            // Toggle current dropdown
            if (!isOpen) {
                dropdown.classList.add('show');
                menu.classList.add('show');
            } else {
                dropdown.classList.remove('show');
                menu.classList.remove('show');
            }
        });

        // Desktop hover behavior
        if (window.innerWidth > 768) {
            dropdown.addEventListener('mouseenter', () => {
                if (window.innerWidth > 768) {
                    dropdown.classList.add('show');
                    menu.classList.add('show');
                }
            });

            dropdown.addEventListener('mouseleave', () => {
                if (window.innerWidth > 768) {
                    dropdown.classList.remove('show');
                    menu.classList.remove('show');
                }
            });
        }

        // Handle dropdown item clicks
        const dropdownItems = menu.querySelectorAll('.dropdown-item');
        dropdownItems.forEach(item => {
            item.addEventListener('click', (e) => {
                // Allow default behavior for links and form submissions
                if (window.innerWidth <= 768) {
                    setTimeout(() => {
                        closeMobileMenu();
                    }, 300);
                } else {
                    dropdown.classList.remove('show');
                    menu.classList.remove('show');
                }
            });
        });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.dropdown')) {
            closeAllDropdowns();
        }
    });
}

function closeAllDropdowns(except = null) {
    const dropdowns = document.querySelectorAll('.dropdown');
    dropdowns.forEach(dropdown => {
        if (dropdown !== except) {
            dropdown.classList.remove('show');
            const menu = dropdown.querySelector('.dropdown-menu');
            if (menu) {
                menu.classList.remove('show');
            }
        }
    });
}

// Smooth scroll for anchor links
function setupSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href === '#' || href === '') return;
            
            e.preventDefault();
            const target = document.querySelector(href);
            
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
                
                // Close mobile menu if open
                if (window.innerWidth <= 768) {
                    closeMobileMenu();
                }
            }
        });
    });
}

// Setup overlay for mobile menu
function setupOverlay() {
    let overlay = document.querySelector('.mobile-overlay');
    
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.className = 'mobile-overlay';
        document.body.appendChild(overlay);
    }
    
    overlay.addEventListener('click', () => {
        closeMobileMenu();
    });
}

// Keyboard navigation
function setupKeyboardNavigation() {
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const navMenu = document.getElementById('navMenu');
            if (navMenu?.classList.contains('active')) {
                closeMobileMenu();
            } else {
                closeAllDropdowns();
            }
        }
    });

    // Arrow key navigation in dropdowns
    const dropdownItems = document.querySelectorAll('.dropdown-item');
    dropdownItems.forEach((item, index) => {
        item.addEventListener('keydown', (e) => {
            const dropdown = item.closest('.dropdown-menu');
            const items = Array.from(dropdown.querySelectorAll('.dropdown-item'));
            const currentIndex = items.indexOf(item);
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                const nextIndex = (currentIndex + 1) % items.length;
                items[nextIndex].focus();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                const prevIndex = currentIndex === 0 ? items.length - 1 : currentIndex - 1;
                items[prevIndex].focus();
            }
        });
    });
}

// Handle window resize
let resizeTimer;
window.addEventListener('resize', () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => {
        const isMobile = window.innerWidth <= 768;
        
        if (!isMobile) {
            // Close mobile menu when resizing to desktop
            closeMobileMenu();
            closeAllDropdowns();
        } else {
            // Ensure proper mobile state
            const navMenu = document.getElementById('navMenu');
            if (navMenu && !navMenu.classList.contains('active')) {
                // Make sure all dropdowns are closed in mobile when menu is closed
                closeAllDropdowns();
            }
        }
    }, 250);
});

// Prevent body scroll when mobile menu is open
function preventBodyScroll() {
    const navMenu = document.getElementById('navMenu');
    if (navMenu?.classList.contains('active')) {
        document.body.style.position = 'fixed';
        document.body.style.top = `-${window.scrollY}px`;
        document.body.style.width = '100%';
    }
}

function restoreBodyScroll() {
    const scrollY = document.body.style.top;
    document.body.style.position = '';
    document.body.style.top = '';
    document.body.style.width = '';
    window.scrollTo(0, parseInt(scrollY || '0') * -1);
}

// Update openMobileMenu to use new scroll prevention
function openMobileMenu() {
    const navMenu = document.getElementById('navMenu');
    const mobileToggle = document.getElementById('mobileToggle');
    const overlay = document.querySelector('.mobile-overlay');
    const icon = mobileToggle?.querySelector('i');
    
    navMenu?.classList.add('active');
    overlay?.classList.add('show');
    
    // Better scroll prevention for mobile
    if (window.innerWidth <= 768) {
        preventBodyScroll();
    }
    
    if (icon) {
        icon.classList.remove('fa-bars');
        icon.classList.add('fa-times');
    }
}

// Update closeMobileMenu to restore scroll
function closeMobileMenu() {
    const navMenu = document.getElementById('navMenu');
    const mobileToggle = document.getElementById('mobileToggle');
    const overlay = document.querySelector('.mobile-overlay');
    const icon = mobileToggle?.querySelector('i');
    
    navMenu?.classList.remove('active');
    overlay?.classList.remove('show');
    
    // Restore body scroll
    if (window.innerWidth <= 768) {
        restoreBodyScroll();
    }
    
    if (icon) {
        icon.classList.remove('fa-times');
        icon.classList.add('fa-bars');
    }
    
    // Close all dropdowns
    closeAllDropdowns();
}

// Export functions for external use
window.navigationUtils = {
    toggleMobileMenu,
    closeMobileMenu,
    openMobileMenu,
    closeAllDropdowns
};