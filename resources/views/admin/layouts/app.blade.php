<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - EventAP</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    @stack('styles')

    <style>
        :root {
            --sidebar-width: 260px;
            --primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --danger: linear-gradient(135deg, #ff6b7a 0%, #ee5a52 100%);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --shadow-sm: 0 2px 10px rgba(0,0,0,0.08);
            --shadow-md: 0 4px 15px rgba(0,0,0,0.1);
            --shadow-lg: 0 8px 25px rgba(0,0,0,0.15);
        }

        * { box-sizing: border-box; }
        body { background: #f8f9fa; overflow-x: hidden; font-family: 'Segoe UI', sans-serif; margin: 0; }

        /* Sidebar */
        .sidebar {
            position: fixed; top: 0; left: 0; height: 100vh; width: var(--sidebar-width);
            background: var(--primary); box-shadow: var(--shadow-md); z-index: 1000;
            display: flex; flex-direction: column; transition: var(--transition);
        }
        .sidebar.hidden { transform: translateX(-100%); }

        .sidebar-header {
            padding: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.15);
            text-align: center; position: relative;
        }
        .sidebar-header img { width: 50px; height: 50px; filter: brightness(1.2); }
        .sidebar-header h4 { color: white; font-size: 1rem; font-weight: 600; margin: 0.75rem 0 0.25rem; }
        .sidebar-header small { color: rgba(255,255,255,0.7); font-size: 0.8rem; }

        .sidebar-close {
            position: absolute; top: 1rem; right: 1rem; width: 32px; height: 32px;
            background: rgba(255,255,255,0.1); border: none; color: white; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; cursor: pointer;
            transition: var(--transition);
        }
        .sidebar-close:hover { background: rgba(255,255,255,0.2); transform: rotate(90deg); }

        /* Navigation */
        .sidebar-nav { flex: 1; padding: 1rem 0; overflow-y: auto; }
        .nav-link {
            color: rgba(255,255,255,0.85); padding: 0.75rem 1.5rem; border-radius: 8px;
            margin: 0.25rem 1rem; transition: var(--transition); text-decoration: none;
            display: flex; align-items: center; font-weight: 500; position: relative;
        }
        .nav-link i { width: 18px; margin-right: 0.75rem; }
        .nav-link:hover, .nav-link.active {
            color: white; background: rgba(255,255,255,0.15); transform: translateX(5px);
            text-decoration: none;
        }
        .nav-link.active::before {
            content: ''; position: absolute; left: 0; top: 50%; transform: translateY(-50%);
            width: 3px; height: 20px; background: white; border-radius: 0 2px 2px 0;
        }

        /* Toggle Button */
        .sidebar-toggle {
            position: fixed; top: 1rem; left: 1rem; z-index: 1001; width: 45px; height: 45px;
            background: var(--primary); border: none; color: white; border-radius: 10px;
            box-shadow: var(--shadow-md); cursor: pointer; opacity: 0; transform: translateX(-60px);
            pointer-events: none; display: flex; align-items: center; justify-content: center;
            transition: var(--transition); font-size: 1.2rem;
        }
        .sidebar-toggle:hover { transform: translateX(-60px) scale(1.05); box-shadow: var(--shadow-lg); }
        .sidebar-toggle.show { opacity: 1; transform: translateX(0); pointer-events: all; }
        
        .sidebar-toggle i.fa-bars { display: inline; }
        .sidebar-toggle i.fa-ellipsis-v { display: none; }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width); min-height: 100vh; background: white;
            border-radius: 15px 0 0 0; box-shadow: var(--shadow-sm); transition: var(--transition);
        }
        .main-content.expanded { margin-left: 0; border-radius: 0; }

        .navbar {
            background: white; box-shadow: var(--shadow-sm); border-radius: 15px 15px 0 0;
            position: sticky; top: 0; z-index: 999; padding: 1rem 1.5rem;
        }
        .main-content.expanded .navbar { border-radius: 0; }

        /* Navbar Title Center */
        .navbar-title {
            flex: 1;
            text-align: center;
            font-size: 1.25rem;
            font-weight: 600;
            color: #2d3748;
            margin: 0;
        }

        /* Profile Section */
        .profile-section {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 10px;
            transition: var(--transition);
            position: relative;
        }

        .profile-section:hover {
            background: rgba(102, 126, 234, 0.05);
        }

        .profile-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #667eea;
        }

        .profile-initials {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
            border: 2px solid #667eea;
        }

        .profile-info {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .profile-name {
            font-weight: 600;
            font-size: 0.9rem;
            color: #2d3748;
            line-height: 1.2;
        }

        .profile-role {
            font-size: 0.75rem;
            color: #718096;
        }

        .profile-dropdown-icon {
            color: #718096;
            transition: var(--transition);
            margin-left: 0.25rem;
        }

        .profile-section:hover .profile-dropdown-icon {
            color: #667eea;
        }

        /* Profile Dropdown */
        .profile-dropdown {
            position: absolute;
            top: calc(100% + 0.5rem);
            right: 0;
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            min-width: 220px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px) scale(0.95);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(0, 0, 0, 0.05);
            z-index: 1000;
            overflow: hidden;
        }

        .profile-dropdown.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
        }

        .profile-dropdown-header {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
        }

        .profile-dropdown-name {
            font-weight: 600;
            color: #2d3748;
            font-size: 0.95rem;
            margin-bottom: 0.25rem;
        }

        .profile-dropdown-email {
            font-size: 0.8rem;
            color: #718096;
        }

        .profile-dropdown-menu {
            padding: 0.5rem;
        }

        .profile-dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: #4a5568;
            text-decoration: none;
            border-radius: 8px;
            transition: var(--transition);
            font-size: 0.9rem;
        }

        .profile-dropdown-item:hover {
            background: rgba(102, 126, 234, 0.05);
            color: #667eea;
        }

        .profile-dropdown-item.backup-item:hover {
            background: rgba(16, 185, 129, 0.05);
            color: #10b981;
        }

        .profile-dropdown-item.logout-item:hover {
            background: rgba(239, 68, 68, 0.05);
            color: #ef4444;
        }

        .profile-dropdown-item i {
            width: 18px;
            text-align: center;
        }

        .profile-dropdown-item.loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .profile-dropdown-item .spinner {
            display: none;
        }

        .profile-dropdown-item.loading .spinner {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .profile-dropdown-divider {
            height: 1px;
            background: #e2e8f0;
            margin: 0.5rem 0;
        }

        /* Overlay */
        .sidebar-overlay {
            position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999;
            opacity: 0; visibility: hidden; transition: var(--transition);
        }
        .sidebar-overlay.show { opacity: 1; visibility: visible; }

        /* Scrollbar */
        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-track { background: rgba(255,255,255,0.1); }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.3); border-radius: 4px; }

        /* Mobile */
        @media (max-width: 768px) {
            :root { --sidebar-width: 280px; }
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; border-radius: 0; }
            .navbar { border-radius: 0; padding: 1rem; }
            .sidebar-toggle { opacity: 1; transform: translateX(0); pointer-events: all; }

            .navbar-title {
                font-size: 1.1rem;
            }

            .profile-info {
                display: none;
            }

            .profile-section {
                padding: 0.25rem;
            }

            .profile-dropdown {
                right: -1rem;
            }
            
            /* Change bars icon to 3 dots vertically on mobile */
            .sidebar-toggle i.fa-bars { display: none; }
            .sidebar-toggle i.fa-ellipsis-v { display: inline; }
        }

        @media (max-width: 576px) {
            :root { --sidebar-width: 100vw; }
            .sidebar-toggle { top: 0.75rem; left: 0.75rem; width: 40px; height: 40px; }
            
            .navbar-title {
                font-size: 1rem;
            }
        }

        /* Components */
        .card { border: none; border-radius: 12px; box-shadow: var(--shadow-sm); margin-bottom: 1.5rem; }
        .btn-primary { background: var(--primary); border: none; border-radius: 8px; transition: var(--transition); }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }
        .fade-in { animation: fadeIn 0.3s ease-out; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <button class="sidebar-toggle" id="toggle" aria-label="Toggle Sidebar">
        <i class="fas fa-bars"></i>
        <i class="fas fa-ellipsis-v"></i>
    </button>

    <div class="sidebar-overlay" id="overlay"></div>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="{{ asset('images/logo.png') }}" alt="EventAP Logo">
            <h4>Event & Portfolio Organizer</h4>
            <small>Admin Panel</small>
            <button class="sidebar-close" id="close" aria-label="Close Sidebar">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
               href="{{ route('admin.dashboard') }}">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a class="nav-link {{ request()->routeIs('admin.events.*') ? 'active' : '' }}"
               href="{{ route('admin.events.index') }}">
                <i class="fas fa-calendar-alt"></i> Events
            </a>
            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
               href="{{ route('admin.users.index') }}">
                <i class="fas fa-users"></i> Users
            </a>
            <a class="nav-link {{ request()->routeIs('admin.certificates') ? 'active' : '' }}"
               href="{{ route('admin.certificates') }}">
                <i class="fas fa-certificate"></i> Certificates
            </a>
            <a class="nav-link {{ request()->routeIs('admin.event-joins.index') ? 'active' : '' }}"
               href="{{ route('admin.event-joins.index') }}">
                <i class="fas fa-users-cog"></i> Participants
            </a>
                <a class="nav-link {{ request()->routeIs('admin.login-logs.*') ? 'active' : '' }}"
                href="{{ route('admin.login-logs.index') }}">
                    <i class="fas fa-shield-alt"></i> Login Activity
                </a>
        </nav>
        
    </div>

    <div class="main-content" id="content">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid d-flex align-items-center">
                <h5 class="navbar-title mb-0">@yield('page-title', 'Dashboard')</h5>
                <div class="navbar-nav d-flex flex-row align-items-center">
                    {{-- Include Notification Component --}}
                    @include('admin.layouts.notification')

                    <!-- Profile Section -->
                    <div class="profile-section" id="profileSection">
                        @if(Auth::user()->profile_picture)
                            <img src="{{ Auth::user()->profile_picture_url }}" alt="{{ Auth::user()->full_name }}" class="profile-avatar">
                        @else
                            <div class="profile-initials">{{ Auth::user()->initials }}</div>
                        @endif
                        <div class="profile-info">
                            <div class="profile-name">{{ Auth::user()->full_name }}</div>
                            <div class="profile-role">{{ ucfirst(Auth::user()->role) }}</div>
                        </div>
                        <i class="fas fa-chevron-down profile-dropdown-icon"></i>

                        <!-- Profile Dropdown -->
                        <div class="profile-dropdown" id="profileDropdown">
                            <div class="profile-dropdown-header">
                                <div class="profile-dropdown-name">{{ Auth::user()->full_name }}</div>
                                <div class="profile-dropdown-email">{{ Auth::user()->email }}</div>
                            </div>
                            <div class="profile-dropdown-menu">
                                <!-- Database Backup Button -->
                                <button type="button" id="backupButton" class="profile-dropdown-item backup-item w-100 border-0 bg-transparent text-start">
                                    <i class="fas fa-database"></i>
                                    <span class="spinner"></span>
                                    <span class="backup-text">Download DB Backup</span>
                                </button>

                                <div class="profile-dropdown-divider"></div>

                                <form method="POST" action="{{ route('admin.logout') }}" class="d-block">
                                    @csrf
                                    <button type="submit" class="profile-dropdown-item logout-item w-100 border-0 bg-transparent text-start">
                                        <i class="fas fa-sign-out-alt"></i>
                                        <span>Logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <div class="p-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        class Sidebar {
            constructor() {
                this.el = {
                    sidebar: document.getElementById('sidebar'),
                    toggle: document.getElementById('toggle'),
                    close: document.getElementById('close'),
                    overlay: document.getElementById('overlay'),
                    content: document.getElementById('content')
                };
                this.init();
            }

            init() {
                this.updateLayout();
                this.bindEvents();
                window.addEventListener('resize', () => this.updateLayout());
            }

            bindEvents() {
                const { toggle, close, overlay, sidebar } = this.el;

                toggle?.addEventListener('click', () => this.toggle());
                close?.addEventListener('click', () => this.close());
                overlay?.addEventListener('click', () => this.close());

                sidebar.querySelectorAll('.nav-link').forEach(link => {
                    link.addEventListener('click', () => {
                        if (this.isMobile()) this.close();
                    });
                });

                document.addEventListener('keydown', (e) => {
                    if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
                        e.preventDefault();
                        this.toggle();
                    }
                    if (e.key === 'Escape' && this.isOpen() && this.isMobile()) {
                        this.close();
                    }
                });
            }

            isMobile() { return window.innerWidth <= 768; }

            isOpen() {
                return this.isMobile() ?
                    this.el.sidebar.classList.contains('show') :
                    !this.el.sidebar.classList.contains('hidden');
            }

            toggle() { this.isOpen() ? this.close() : this.open(); }

            open() {
                const { sidebar, overlay, toggle, content } = this.el;

                if (this.isMobile()) {
                    sidebar.classList.add('show');
                    overlay.classList.add('show');
                    toggle.classList.remove('show');
                } else {
                    sidebar.classList.remove('hidden');
                    content.classList.remove('expanded');
                    toggle.classList.remove('show');
                }
            }

            close() {
                const { sidebar, overlay, toggle, content } = this.el;

                if (this.isMobile()) {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                    toggle.classList.add('show');
                } else {
                    sidebar.classList.add('hidden');
                    content.classList.add('expanded');
                    toggle.classList.add('show');
                }
            }

            updateLayout() {
                const { sidebar, overlay, content, toggle } = this.el;

                if (this.isMobile()) {
                    sidebar.classList.remove('hidden');
                    content.classList.add('expanded');
                    toggle.classList.add('show');

                    if (!sidebar.classList.contains('show')) {
                        overlay.classList.remove('show');
                    }
                } else {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                    content.classList.remove('expanded');
                    toggle.classList.remove('show');
                }
            }
        }

        class ProfileDropdown {
            constructor() {
                this.profileSection = document.getElementById('profileSection');
                this.dropdown = document.getElementById('profileDropdown');
                this.isOpen = false;

                this.init();
            }

            init() {
                this.bindEvents();
            }

            bindEvents() {
                // Toggle dropdown when clicking profile section
                this.profileSection.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.toggle();
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', (e) => {
                    if (!e.target.closest('.profile-section')) {
                        this.close();
                    }
                });

                // Prevent dropdown from closing when clicking inside
                this.dropdown.addEventListener('click', (e) => {
                    e.stopPropagation();
                });
            }

            toggle() {
                if (this.isOpen) {
                    this.close();
                } else {
                    this.open();
                }
            }

            open() {
                this.dropdown.classList.add('show');
                this.isOpen = true;
            }

            close() {
                this.dropdown.classList.remove('show');
                this.isOpen = false;
            }
        }

        class DatabaseBackup {
            constructor() {
                this.backupButton = document.getElementById('backupButton');
                this.init();
            }

            init() {
                this.backupButton.addEventListener('click', () => this.downloadBackup());
            }

            async downloadBackup() {
                const button = this.backupButton;
                const icon = button.querySelector('.fas');
                const text = button.querySelector('.backup-text');

                // Disable button and show loading state
                button.classList.add('loading');
                button.disabled = true;
                text.textContent = 'Downloading...';

                try {
                    const response = await fetch('{{ route("admin.backup.download") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Backup failed');
                    }

                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    
                    // Get filename from response headers or create default
                    const contentDisposition = response.headers.get('Content-Disposition');
                    let filename = 'u802714156_events_backup.sql';
                    
                    if (contentDisposition) {
                        const filenameMatch = contentDisposition.match(/filename="(.+)"/);
                        if (filenameMatch) {
                            filename = filenameMatch[1];
                        }
                    }
                    
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);

                    // Show success message
                    this.showAlert('Database backup downloaded successfully!', 'success');
                } catch (error) {
                    console.error('Backup error:', error);
                    this.showAlert('Failed to download database backup. Please try again.', 'error');
                } finally {
                    // Reset button state
                    button.classList.remove('loading');
                    button.disabled = false;
                    text.textContent = 'Download DB Backup';
                }
            }

            showAlert(message, type) {
                const alertDiv = document.createElement('div');
                alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
                alertDiv.innerHTML = `
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;

                const container = document.querySelector('.p-4');
                container.insertBefore(alertDiv, container.firstChild);

                // Auto dismiss after 5 seconds
                setTimeout(() => {
                    alertDiv.remove();
                }, 5000);
            }
        }

        // Initialize systems when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            new Sidebar();
            new ProfileDropdown();
            new DatabaseBackup();
            // NotificationSystem is initialized in notification.blade.php
        });
    </script>

    @stack('scripts')
</body>
</html>