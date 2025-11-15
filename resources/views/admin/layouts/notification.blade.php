{{-- Notification Styles --}}
<style>
    .notification-container {
        position: relative;
        margin-right: 1rem;
    }

    .notification-icon {
        position: relative;
        cursor: pointer;
        color: #6c757d;
        transition: var(--transition);
        font-size: 1.2rem;
        padding: 0.5rem;
        border-radius: 8px;
    }

    .notification-icon:hover {
        color: #495057;
        background: rgba(102, 126, 234, 0.1);
    }

    .notification-badge {
        position: absolute;
        top: -2px;
        right: -2px;
        background: #dc3545;
        color: white;
        font-size: 0.7rem;
        padding: 0.15rem 0.4rem;
        border-radius: 50px;
        min-width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        animation: pulse 2s infinite;
    }

    .notification-badge.hidden {
        display: none;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }

    /* Notification Dropdown */
    .notification-dropdown {
        position: absolute;
        top: calc(100% + 0.5rem);
        right: 0;
        background: white;
        border-radius: 12px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        min-width: 350px;
        max-width: 400px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px) scale(0.95);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        z-index: 1000;
        max-height: 500px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .notification-dropdown.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0) scale(1);
    }

    .notification-header {
        padding: 1rem 1.25rem 0.75rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8f9fa;
        border-radius: 12px 12px 0 0;
    }

    .notification-header h6 {
        margin: 0;
        font-weight: 600;
        color: #2d3748;
    }

    .mark-all-read {
        background: none;
        border: none;
        color: #667eea;
        font-size: 0.8rem;
        cursor: pointer;
        text-decoration: underline;
    }

    .mark-all-read:hover {
        color: #5a67d8;
    }

    .notification-list {
        flex: 1;
        overflow-y: auto;
        max-height: 400px;
    }

    .notification-item {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .notification-item:hover {
        background: #f8f9fa;
    }

    .notification-item:last-child {
        border-bottom: none;
    }

    .notification-item.unread {
        background: rgba(102, 126, 234, 0.05);
        border-left: 3px solid #667eea;
    }

    .notification-icon-small {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.9rem;
        flex-shrink: 0;
    }

    .notification-icon-small.feedback {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .notification-content {
        flex: 1;
        min-width: 0;
    }

    .notification-message {
        font-size: 0.9rem;
        color: #2d3748;
        margin-bottom: 0.25rem;
        line-height: 1.4;
    }

    .feedback-preview {
        font-size: 0.85rem;
        color: #4a5568;
        font-style: italic;
        margin-top: 0.5rem;
        padding: 0.5rem;
        background: rgba(102, 126, 234, 0.05);
        border-left: 2px solid #667eea;
        border-radius: 4px;
        line-height: 1.4;
    }

    .notification-time {
        font-size: 0.75rem;
        color: #718096;
    }

    .notification-actions {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .notification-action {
        background: none;
        border: none;
        color: #667eea;
        font-size: 0.75rem;
        cursor: pointer;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        transition: all 0.2s ease;
    }

    .notification-action:hover {
        background: rgba(102, 126, 234, 0.1);
    }

    .notification-action.delete {
        color: #e53e3e;
    }

    .notification-action.delete:hover {
        background: rgba(229, 62, 62, 0.1);
    }

    .no-notifications {
        padding: 2rem 1.25rem;
        text-align: center;
        color: #718096;
    }

    .loading-notifications {
        padding: 2rem 1.25rem;
        text-align: center;
        color: #718096;
    }

    .notification-list::-webkit-scrollbar { width: 4px; }
    .notification-list::-webkit-scrollbar-track { background: rgba(0,0,0,0.05); }
    .notification-list::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.2); border-radius: 4px; }

    @media (max-width: 768px) {
        .notification-dropdown {
            min-width: 300px;
            max-width: calc(100vw - 2rem);
        }
    }
</style>

{{-- Notification HTML --}}
<div class="notification-container">
    <div class="notification-icon" id="notificationBell">
        <i class="fas fa-bell"></i>
        <span class="notification-badge" id="notificationBadge">0</span>
    </div>

    {{-- Notification Dropdown --}}
    <div class="notification-dropdown" id="notificationDropdown">
        <div class="notification-header">
            <h6>Notifications</h6>
            <button class="mark-all-read" id="markAllRead">Mark all as read</button>
        </div>
        <div class="notification-list" id="notificationList">
            <div class="loading-notifications">
                <i class="fas fa-spinner fa-spin"></i> Loading notifications...
            </div>
        </div>
    </div>
</div>

{{-- Notification JavaScript --}}
<script>
    class NotificationSystem {
        constructor() {
            this.bell = document.getElementById('notificationBell');
            this.badge = document.getElementById('notificationBadge');
            this.dropdown = document.getElementById('notificationDropdown');
            this.list = document.getElementById('notificationList');
            this.markAllBtn = document.getElementById('markAllRead');
            this.isOpen = false;
            this.notifications = [];

            this.init();
        }

        init() {
            this.bindEvents();
            this.loadNotifications();
            this.startPolling();
        }

        bindEvents() {
            // Toggle dropdown
            this.bell.addEventListener('click', (e) => {
                e.stopPropagation();
                this.toggleDropdown();
            });

            // Mark all as read
            this.markAllBtn.addEventListener('click', () => {
                this.markAllAsRead();
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.notification-container')) {
                    this.closeDropdown();
                }
            });

            // Prevent dropdown from closing when clicking inside
            this.dropdown.addEventListener('click', (e) => {
                e.stopPropagation();
            });
        }

        async loadNotifications() {
            try {
                const response = await fetch('/admin/notifications', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (!response.ok) throw new Error('Failed to load notifications');

                const data = await response.json();
                this.notifications = data.data || [];
                this.updateBadge();
                this.renderNotifications();
            } catch (error) {
                console.error('Error loading notifications:', error);
                this.showError('Failed to load notifications');
            }
        }

        async updateNotificationCount() {
            try {
                const response = await fetch('/admin/notifications/count', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (!response.ok) throw new Error('Failed to get count');

                const data = await response.json();
                this.updateBadgeCount(data.count);
            } catch (error) {
                console.error('Error getting notification count:', error);
            }
        }

        updateBadge() {
            const unreadCount = this.notifications.filter(n => !n.is_read).length;
            this.updateBadgeCount(unreadCount);
        }

        updateBadgeCount(count) {
            if (count > 0) {
                this.badge.textContent = count > 99 ? '99+' : count;
                this.badge.classList.remove('hidden');
            } else {
                this.badge.classList.add('hidden');
            }
        }

        renderNotifications() {
            if (this.notifications.length === 0) {
                this.list.innerHTML = `
                    <div class="no-notifications">
                        <i class="fas fa-bell-slash" style="font-size: 2rem; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                        <p>No notifications yet</p>
                    </div>
                `;
                return;
            }

            this.list.innerHTML = this.notifications.map(notification => {
                // Parse notification data to check if it's feedback
                let notificationData = null;
                let isFeedback = false;
                let feedbackPreview = '';
                
                try {
                    notificationData = JSON.parse(notification.data || '{}');
                    isFeedback = notification.type === 'feedback';
                    feedbackPreview = notificationData.feedback_preview || '';
                } catch (e) {
                    console.error('Error parsing notification data:', e);
                }

                return `
                    <div class="notification-item ${!notification.is_read ? 'unread' : ''}"
                         data-id="${notification.id}">
                        <div class="notification-icon-small ${isFeedback ? 'feedback' : ''}">
                            <i class="fas ${isFeedback ? 'fa-comment-dots' : 'fa-user-plus'}"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-message">
                                ${this.escapeHtml(notification.message)}
                            </div>
                            ${isFeedback && feedbackPreview ? `
                                <div class="feedback-preview">
                                    ${this.escapeHtml(feedbackPreview)}
                                </div>
                            ` : ''}
                            <div class="notification-time">
                                ${this.formatTime(notification.created_at)}
                            </div>
                            <div class="notification-actions">
                                ${!notification.is_read ? `
                                    <button class="notification-action" onclick="notificationSystem.markAsRead(${notification.id})">
                                        Mark as read
                                    </button>
                                ` : ''}
                                <button class="notification-action delete" onclick="notificationSystem.deleteNotification(${notification.id})">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        toggleDropdown() {
            if (this.isOpen) {
                this.closeDropdown();
            } else {
                this.openDropdown();
            }
        }

        openDropdown() {
            this.dropdown.classList.add('show');
            this.isOpen = true;
            this.loadNotifications(); // Refresh when opening
        }

        closeDropdown() {
            this.dropdown.classList.remove('show');
            this.isOpen = false;
        }

        async markAsRead(id) {
            try {
                const response = await fetch(`/admin/notifications/${id}/read`, {
                    method: 'PATCH',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (!response.ok) throw new Error('Failed to mark as read');

                // Update local state
                const notification = this.notifications.find(n => n.id === id);
                if (notification) {
                    notification.is_read = true;
                }

                this.updateBadge();
                this.renderNotifications();
            } catch (error) {
                console.error('Error marking notification as read:', error);
            }
        }

        async markAllAsRead() {
            try {
                const response = await fetch('/admin/notifications/read-all', {
                    method: 'PATCH',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (!response.ok) throw new Error('Failed to mark all as read');

                // Update local state
                this.notifications.forEach(n => n.is_read = true);

                this.updateBadge();
                this.renderNotifications();
            } catch (error) {
                console.error('Error marking all notifications as read:', error);
            }
        }

        async deleteNotification(id) {
            try {
                const response = await fetch(`/admin/notifications/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (!response.ok) throw new Error('Failed to delete notification');

                // Remove from local state
                this.notifications = this.notifications.filter(n => n.id !== id);

                this.updateBadge();
                this.renderNotifications();
            } catch (error) {
                console.error('Error deleting notification:', error);
            }
        }

        startPolling() {
            // Poll for new notifications every 30 seconds
            setInterval(() => {
                if (!this.isOpen) {
                    this.updateNotificationCount();
                }
            }, 30000);
        }

        formatTime(timestamp) {
            const date = new Date(timestamp);
            const now = new Date();
            const diff = now - date;

            // Less than a minute
            if (diff < 60000) {
                return 'Just now';
            }

            // Less than an hour
            if (diff < 3600000) {
                const minutes = Math.floor(diff / 60000);
                return `${minutes} minute${minutes === 1 ? '' : 's'} ago`;
            }

            // Less than a day
            if (diff < 86400000) {
                const hours = Math.floor(diff / 3600000);
                return `${hours} hour${hours === 1 ? '' : 's'} ago`;
            }

            // Less than a week
            if (diff < 604800000) {
                const days = Math.floor(diff / 86400000);
                return `${days} day${days === 1 ? '' : 's'} ago`;
            }

            // Format as date
            return date.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: date.getFullYear() !== now.getFullYear() ? 'numeric' : undefined
            });
        }

        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        showError(message) {
            this.list.innerHTML = `
                <div class="no-notifications">
                    <i class="fas fa-exclamation-triangle" style="font-size: 2rem; margin-bottom: 0.5rem; color: #e53e3e;"></i>
                    <p>${message}</p>
                    <button class="notification-action" onclick="notificationSystem.loadNotifications()" style="margin-top: 0.5rem;">
                        Try again
                    </button>
                </div>
            `;
        }
    }

    // Initialize notification system when DOM is loaded
    document.addEventListener('DOMContentLoaded', () => {
        window.notificationSystem = new NotificationSystem();
    });
</script>