// Get CSRF token from meta tag
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Sanitize input to prevent XSS
function sanitizeInput(input) {
    const div = document.createElement('div');
    div.textContent = input;
    return div.innerHTML;
}

// Escape HTML entities
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

// Toast notification system
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
            <span>${escapeHtml(message)}</span>
        </div>
    `;
    
    const container = document.getElementById('toastContainer');
    container.appendChild(toast);
    
    // Trigger animation
    setTimeout(() => toast.classList.add('show'), 100);
    
    // Remove toast after 3 seconds
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            if (container.contains(toast)) {
                container.removeChild(toast);
            }
        }, 300);
    }, 3000);
}

// Toggle event join/leave functionality
async function toggleEventJoin(button) {
    const eventId = button.getAttribute('data-event-id');
    const isJoined = button.getAttribute('data-joined') === 'true';
    const btnText = button.querySelector('.btn-text');
    
    // Disable button and show loading state
    button.disabled = true;
    
    // Store original content
    const originalContent = btnText.innerHTML;
    
    // Show loading state with spinner
    btnText.innerHTML = `<span class="spinner"></span> ${isJoined ? 'Leaving...' : 'Registering...'}`;
    
    try {
        const url = `/events/${eventId}/${isJoined ? 'leave' : 'join'}`;
        const method = isJoined ? 'DELETE' : 'POST';
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Update button state
            const newJoinedState = !isJoined;
            button.setAttribute('data-joined', newJoinedState ? 'true' : 'false');
            button.className = `register-btn ${newJoinedState ? 'joined' : ''}`;
            
            // Update button text
            btnText.innerHTML = newJoinedState ? 'Leave Event' : 'Register Now';
            
            // Show success toast
            showToast(data.message, 'success');
            
            // Add button animation
            button.style.transform = 'scale(0.95)';
            setTimeout(() => {
                button.style.transform = '';
            }, 150);
        } else {
            // Show error toast
            showToast(data.message, 'error');
            // Restore original content
            btnText.innerHTML = originalContent;
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('An error occurred. Please try again.', 'error');
        // Restore original content
        btnText.innerHTML = originalContent;
    } finally {
        // Re-enable button
        button.disabled = false;
    }
}

// Event info modal functionality with sanitized data and event image
function showEventInfo(eventId) {
    // Get event data from the info button's data attributes
    const infoBtn = document.querySelector(`button[onclick="showEventInfo(${eventId})"]`);
    
    if (!infoBtn) {
        console.error('Info button not found');
        return;
    }
    
    // Get and sanitize all data attributes
    const title = escapeHtml(infoBtn.getAttribute('data-event-title') || 'Event');
    const location = escapeHtml(infoBtn.getAttribute('data-event-location') || 'TBA');
    const date = escapeHtml(infoBtn.getAttribute('data-event-date') || 'TBA');
    const time = escapeHtml(infoBtn.getAttribute('data-event-time') || 'TBA');
    const department = escapeHtml(infoBtn.getAttribute('data-event-department') || 'All Departments');
    const description = escapeHtml(infoBtn.getAttribute('data-event-description') || 'No description available.');
    const imageUrl = infoBtn.getAttribute('data-event-image') || '';
    
    // Set modal title
    document.getElementById('modalTitle').textContent = infoBtn.getAttribute('data-event-title') || 'Event';
    
    // Create event image header
    let imageHTML = '';
    if (imageUrl) {
        imageHTML = `<img src="${imageUrl}" alt="${title}" class="modal-event-image">`;
    } else {
        imageHTML = `
            <div class="modal-event-image-placeholder">
                <i class="fas fa-calendar-alt"></i>
            </div>
        `;
    }
    
    // Create modal content with sanitized data
    const modalContent = `
        ${imageHTML}
        <div class="info-item">
            <div class="info-icon">
                <i class="fas fa-map-marker-alt"></i>
            </div>
            <div class="info-content">
                <div class="info-label">Location</div>
                <div class="info-text">${location}</div>
            </div>
        </div>
        
        <div class="info-item">
            <div class="info-icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="info-content">
                <div class="info-label">Event Date</div>
                <div class="info-text">${date}</div>
            </div>
        </div>
        
        <div class="info-item">
            <div class="info-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="info-content">
                <div class="info-label">Time</div>
                <div class="info-text">${time}</div>
            </div>
        </div>
        
        <div class="info-item">
            <div class="info-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="info-content">
                <div class="info-label">Department</div>
                <div class="info-text">${department}</div>
            </div>
        </div>
        
        <div class="description-box">
            <div class="info-label">Description</div>
            <div class="info-text">${description}</div>
        </div>
    `;
    
    document.getElementById('eventInfoContent').innerHTML = modalContent;
    const modal = document.getElementById('eventInfoModal');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeEventInfo() {
    const modal = document.getElementById('eventInfoModal');
    modal.style.display = 'none';
    document.body.style.overflow = '';
}

// Close modal on outside click
document.getElementById('eventInfoModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeEventInfo();
    }
});

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('eventInfoModal');
        if (modal && modal.style.display === 'flex') {
            closeEventInfo();
        }
    }
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + K to focus search
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        const searchInput = document.querySelector('.search-input');
        if (searchInput) {
            searchInput.focus();
            searchInput.select();
        }
    }
    
    // Escape to blur search
    if (e.key === 'Escape') {
        const searchInput = document.querySelector('.search-input');
        if (searchInput === document.activeElement) {
            searchInput.blur();
        }
    }
});

// Enhanced pagination interactions
document.querySelectorAll('.pagination-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        if (!this.classList.contains('disabled') && !this.classList.contains('active')) {
            // Add loading state
            this.style.opacity = '0.7';
            this.style.pointerEvents = 'none';
            
            // Smooth scroll to top
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
            
            setTimeout(() => {
                this.style.opacity = '';
                this.style.pointerEvents = '';
            }, 300);
        }
    });
});

// Smooth animations for event cards on load
document.addEventListener('DOMContentLoaded', function() {
    const eventCards = document.querySelectorAll('.event-card');
    
    // Intersection Observer for scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                // Add staggered delay
                setTimeout(() => {
                    entry.target.classList.add('animate');
                }, index * 100);
                
                // Unobserve after animation
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    // Observe all event cards
    eventCards.forEach(card => {
        observer.observe(card);
    });
    
    // Add hover effect for images
    eventCards.forEach(card => {
        const image = card.querySelector('.event-image');
        if (image) {
            card.addEventListener('mouseenter', () => {
                image.style.transform = 'scale(1.1)';
            });
            
            card.addEventListener('mouseleave', () => {
                image.style.transform = '';
            });
        }
    });
});

// Search input sanitization
const searchInput = document.querySelector('.search-input');

if (searchInput) {
    // Show search hint on focus
    searchInput.addEventListener('focus', function() {
        this.placeholder = 'Type to search events...';
    });
    
    searchInput.addEventListener('blur', function() {
        this.placeholder = 'Search events...';
    });
    
    // Sanitize input on form submit
    const searchForm = searchInput.closest('form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            const input = searchInput.value.trim();
            // Remove potentially dangerous characters
            const sanitized = input.replace(/[<>'"]/g, '');
            searchInput.value = sanitized;
        });
    }
    
    // Optional: Real-time input sanitization
    searchInput.addEventListener('input', function(e) {
        const cursorPos = this.selectionStart;
        const originalLength = this.value.length;
        
        // Remove dangerous characters in real-time
        this.value = this.value.replace(/[<>'"]/g, '');
        
        // Adjust cursor position if characters were removed
        const newLength = this.value.length;
        const diff = originalLength - newLength;
        if (diff > 0) {
            this.setSelectionRange(cursorPos - diff, cursorPos - diff);
        }
    });
    
    // Optional: Debounced auto-search (uncomment if needed)
    /*
    let searchTimeout;
    searchInput.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        
        searchTimeout = setTimeout(() => {
            if (e.target.value.length >= 3 || e.target.value.length === 0) {
                this.form.submit();
            }
        }, 500);
    });
    */
}

// Add ripple effect to buttons
function createRipple(event) {
    const button = event.currentTarget;
    const ripple = document.createElement('span');
    const rect = button.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = event.clientX - rect.left - size / 2;
    const y = event.clientY - rect.top - size / 2;
    
    ripple.style.width = ripple.style.height = size + 'px';
    ripple.style.left = x + 'px';
    ripple.style.top = y + 'px';
    ripple.classList.add('ripple');
    
    button.appendChild(ripple);
    
    setTimeout(() => {
        ripple.remove();
    }, 600);
}

// Apply ripple to all register buttons
document.querySelectorAll('.register-btn').forEach(button => {
    button.addEventListener('click', createRipple);
});

// Smooth scroll behavior for the entire page
if ('scrollBehavior' in document.documentElement.style) {
    document.documentElement.style.scrollBehavior = 'smooth';
}

// Add loading state for page navigation
window.addEventListener('beforeunload', function() {
    document.body.style.opacity = '0.7';
    document.body.style.pointerEvents = 'none';
});

// Performance optimization: Lazy load images
if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                }
                imageObserver.unobserve(img);
            }
        });
    });
    
    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });
}

// Hover effect on info button
document.querySelectorAll('.info-btn').forEach(btn => {
    btn.addEventListener('mouseenter', function() {
        this.style.background = 'rgba(30, 41, 59, 1)';
        this.style.transform = 'scale(1.1)';
    });
    
    btn.addEventListener('mouseleave', function() {
        this.style.background = 'rgba(30, 41, 59, 0.9)';
        this.style.transform = '';
    });
});

// Console log for developers
console.log('%c🎉 EventAP Dashboard Loaded Successfully!', 'color: #dc2626; font-size: 16px; font-weight: bold;');
console.log('%cKeyboard Shortcuts:', 'color: #b91c1c; font-size: 14px; font-weight: bold;');
console.log('%c• Ctrl/Cmd + K: Focus search', 'color: #4a5568; font-size: 12px;');
console.log('%c• Escape: Close search/modal', 'color: #4a5568; font-size: 12px;');
console.log('%c• Click info icon: View event details', 'color: #4a5568; font-size: 12px;');