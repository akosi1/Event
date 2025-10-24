document.addEventListener('DOMContentLoaded', function() {
    // ========================================
    // ELEMENT REFERENCES
    // ========================================
    const navbar = document.getElementById('navbar');
    const mobileToggle = document.getElementById('mobileToggle');
    const navContent = document.getElementById('navContent');
    const mobileOverlay = document.getElementById('mobileOverlay');
    const navLinks = document.querySelectorAll('[data-nav-link]');
    const dropdowns = document.querySelectorAll('.dropdown');

    // ========================================
    // SCROLL EFFECT
    // ========================================
    window.addEventListener('scroll', () => {
        navbar.classList.toggle('scrolled', window.pageYOffset > 200);
    });

    // ========================================
    // MOBILE MENU TOGGLE
    // ========================================
    mobileToggle.addEventListener('click', (e) => {
        e.stopPropagation();
        toggleMobileMenu();
    });

    // ========================================
    // OVERLAY CLICK HANDLER
    // ========================================
    mobileOverlay.addEventListener('click', () => {
        closeMobileMenu();
    });

    // ========================================
    // NAVIGATION LINK HANDLERS
    // ========================================
    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            if (window.innerWidth <= 768) {
                setTimeout(() => {
                    closeMobileMenu();
                }, 100);
            }
        });
    });

    // ========================================
    // DROPDOWN HANDLERS
    // ========================================
    dropdowns.forEach(dropdown => {
        const btn = dropdown.querySelector('.dropdown-btn');
        const items = dropdown.querySelectorAll('.dropdown-item');

        // Dropdown button click
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleDropdown(dropdown);
        });

        // Dropdown item clicks
        items.forEach(item => {
            item.addEventListener('click', (e) => {
                if (window.innerWidth <= 768) {
                    if (item.tagName === 'A') {
                        setTimeout(() => {
                            closeMobileMenu();
                        }, 100);
                    }
                }
            });
        });
    });

    // ========================================
    // CLICK OUTSIDE HANDLER
    // ========================================
    document.addEventListener('click', (e) => {
        const clickedInsideNav = e.target.closest('.nav-content');
        const clickedToggle = e.target.closest('.mobile-toggle');
        
        if (!clickedInsideNav && !clickedToggle) {
            if (window.innerWidth > 768) {
                closeAllDropdowns();
            }
        }
    });

    // ========================================
    // KEYBOARD HANDLERS
    // ========================================
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (navContent.classList.contains('active')) {
                closeMobileMenu();
            } else {
                closeAllDropdowns();
            }
        }
    });

    // ========================================
    // RESIZE HANDLER
    // ========================================
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            if (window.innerWidth > 768) {
                closeMobileMenu();
                closeAllDropdowns();
            }
        }, 250);
    });

    // ========================================
    // HELPER FUNCTIONS
    // ========================================
    
    function toggleMobileMenu() {
        const isOpen = navContent.classList.contains('active');
        
        if (isOpen) {
            closeMobileMenu();
        } else {
            openMobileMenu();
        }
    }

    function openMobileMenu() {
        navContent.classList.add('active');
        mobileOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
        
        const icon = mobileToggle.querySelector('i');
        icon.className = 'fas fa-times';
        
        mobileToggle.setAttribute('aria-expanded', 'true');
    }

    function closeMobileMenu() {
        navContent.classList.remove('active');
        mobileOverlay.classList.remove('active');
        document.body.style.overflow = '';
        
        const icon = mobileToggle.querySelector('i');
        icon.className = 'fas fa-bars';
        
        mobileToggle.setAttribute('aria-expanded', 'false');
        
        closeAllDropdowns();
    }

    function toggleDropdown(dropdown) {
        const isActive = dropdown.classList.contains('active');
        
        // Close other dropdowns
        dropdowns.forEach(d => {
            if (d !== dropdown) {
                d.classList.remove('active');
                const btn = d.querySelector('.dropdown-btn');
                if (btn) btn.setAttribute('aria-expanded', 'false');
            }
        });
        
        // Toggle current dropdown
        if (isActive) {
            dropdown.classList.remove('active');
            const btn = dropdown.querySelector('.dropdown-btn');
            if (btn) btn.setAttribute('aria-expanded', 'false');
        } else {
            dropdown.classList.add('active');
            const btn = dropdown.querySelector('.dropdown-btn');
            if (btn) btn.setAttribute('aria-expanded', 'true');
        }
    }

    function closeAllDropdowns() {
        dropdowns.forEach(dropdown => {
            dropdown.classList.remove('active');
            const btn = dropdown.querySelector('.dropdown-btn');
            if (btn) btn.setAttribute('aria-expanded', 'false');
        });
    }

    // ========================================
    // SMOOTH SCROLL FOR ANCHOR LINKS
    // ========================================
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            const target = document.querySelector(targetId);
            
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // ========================================
    // ADD RIPPLE EFFECT
    // ========================================
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
        `;
        document.head.appendChild(style);
    }

    // ========================================
    // KEYBOARD NAVIGATION SUPPORT
    // ========================================
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

    // Export functions for external use
    window.navbarUtils = {
        toggleMobileMenu,
        closeMobileMenu,
        openMobileMenu,
        closeAllDropdowns
    };
});