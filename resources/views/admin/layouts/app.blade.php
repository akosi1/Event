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

        .profile-dropdown-item.logout-item:hover {
            background: rgba(239, 68, 68, 0.05);
            color: #ef4444;
        }

        .profile-dropdown-item i {
            width: 18px;
            text-align: center;
        }

        .profile-dropdown-divider {
            height: 1px;
            background: #e2e8f0;
            margin: 0.5rem 0;
        }

        /* Notification Styles */
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

        .notification-list::-webkit-scrollbar { width: 4px; }
        .notification-list::-webkit-scrollbar-track { background: rgba(0,0,0,0.05); }
        .notification-list::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.2); border-radius: 4px; }

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

            .notification-dropdown {
                min-width: 300px;
                max-width: calc(100vw - 2rem);
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
        </nav>
        
    </div>
    

    <div class="main-content" id="content">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid d-flex align-items-center">
                <h5 class="navbar-title mb-0">@yield('page-title', 'Dashboard')</h5>
                <div class="navbar-nav d-flex flex-row align-items-center">
                    <!-- Notification Container -->
                    <div class="notification-container">
                        <div class="notification-icon" id="notificationBell">
                            <i class="fas fa-bell"></i>
                            <span class="notification-badge" id="notificationBadge">0</span>
                        </div>

                        <!-- Notification Dropdown -->
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

                this.list.innerHTML = this.notifications.map(notification => `
                    <div class="notification-item ${!notification.is_read ? 'unread' : ''}"
                         data-id="${notification.id}">
                        <div class="notification-icon-small">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-message">
                                ${this.escapeHtml(notification.message)}
                            </div>
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
                `).join('');
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

        // Initialize systems when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            new Sidebar();
            new ProfileDropdown();
            window.notificationSystem = new NotificationSystem();
        });
    </script>

    @stack('scripts')
</body>
</html>`