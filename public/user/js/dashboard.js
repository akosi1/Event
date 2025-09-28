// Get CSRF token from meta tag
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Toast notification system
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    
    const container = document.getElementById('toastContainer');
    container.appendChild(toast);
    
    setTimeout(() => toast.classList.add('show'), 100);
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => container.removeChild(toast), 300);
    }, 3000);
}

// Toggle event join/leave functionality
async function toggleEventJoin(button) {
    const eventId = button.getAttribute('data-event-id');
    const isJoined = button.getAttribute('data-joined') === 'true';
    const btnIcon = button.querySelector('.btn-icon i');
    const btnText = button.querySelector('.btn-text');
    
    button.disabled = true;
    btnIcon.className = 'spinner';
    btnText.textContent = isJoined ? 'Leaving...' : 'Joining...';
    
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
            const newJoinedState = !isJoined;
            button.setAttribute('data-joined', newJoinedState ? 'true' : 'false');
            button.className = `join-event-btn ${newJoinedState ? 'joined' : ''}`;
            
            btnIcon.className = newJoinedState ? 'fas fa-minus' : 'fas fa-plus';
            btnText.textContent = newJoinedState ? 'Leave Event' : 'Join Event';
            
            showToast(data.message, 'success');
        } else {
            showToast(data.message, 'error');
            btnIcon.className = isJoined ? 'fas fa-minus' : 'fas fa-plus';
            btnText.textContent = isJoined ? 'Leave Event' : 'Join Event';
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('An error occurred. Please try again.', 'error');
        btnIcon.className = isJoined ? 'fas fa-minus' : 'fas fa-plus';
        btnText.textContent = isJoined ? 'Leave Event' : 'Join Event';
    } finally {
        button.disabled = false;
    }
}

// Keyboard shortcut for search
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        document.querySelector('.search-input').focus();
    }
});

// Smooth scroll for pagination clicks
document.querySelectorAll('.pagination-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        if (!this.classList.contains('disabled') && !this.classList.contains('active')) {
            // Add a subtle loading state
            this.style.opacity = '0.7';
            setTimeout(() => {
                this.style.opacity = '';
            }, 200);
        }
    });
});

// Add hover effects for better UX
document.querySelectorAll('.pagination-btn:not(.disabled)').forEach(btn => {
    btn.addEventListener('mouseenter', function() {
        if (!this.classList.contains('active')) {
            this.style.transform = 'translateY(-2px)';
        }
    });
    
    btn.addEventListener('mouseleave', function() {
        if (!this.classList.contains('active')) {
            this.style.transform = '';
        }
    });
});

// Add smooth animations for event cards
document.addEventListener('DOMContentLoaded', function() {
    const eventCards = document.querySelectorAll('.event-card');
    
    // Add staggered animation for event cards
    eventCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.style.animation = 'fadeInUp 0.6s ease forwards';
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
    });
    
    // Add CSS animation keyframes dynamically
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    `;
    document.head.appendChild(style);
});