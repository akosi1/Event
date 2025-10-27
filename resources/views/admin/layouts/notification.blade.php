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

        /* Footer */
        .sidebar-footer {
            padding: 1rem; border-top: 1px solid rgba(255,255,255,0.15);
            background: rgba(0,0,0,0.1);
        }
        .logout-btn {
            width: 100%; background: var(--danger); border: none; color: white;
            padding: 0.75rem; border-radius: 8px; font-weight: 500; cursor: pointer;
            transition: var(--transition); display: flex; align-items: center; justify-content: center;
        }
        .logout-btn:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }
        .logout-btn i { margin-right: 0.5rem; }

        /* Toggle Button */
        .sidebar-toggle {
            position: fixed; top: 1rem; left: 1rem; z-index: 1001; width: 45px; height: 45px;
            background: var(--primary); border: none; color: white; border-radius: 8px;
            box-shadow: var(--shadow-md); cursor: pointer; opacity: 0; transform: translateX(-60px);
            pointer-events: none; display: flex; align-items: center; justify-content: center;
            transition: var(--transition);
        }
        .sidebar-toggle:hover { transform: translateX(-60px) translateY(-3px); box-shadow: var(--shadow-lg); }
        .sidebar-toggle.show { opacity: 1; transform: translateX(0); pointer-events: all; }

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
            .navbar { border-radius: 0; }
            .sidebar-toggle { opacity: 1; transform: translateX(0); pointer-events: all; }
        }

        @media (max-width: 576px) {
            :root { --sidebar-width: 100vw; }
            .sidebar-toggle { top: 0.75rem; left: 0.75rem; }
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

        <div class="sidebar-footer">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </div>

    <div class="main-content" id="content">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid">
                <h5 class="mb-0 fw-semibold">@yield('page-title', 'Dashboard')</h5>
                <div class="navbar-nav ms-auto d-flex flex-row align-items-center">
                    <!-- Include Notification Component -->
                    @include('admin.layouts.notification')

                    <span class="navbar-text">
                        <i class="fas fa-clock me-1"></i>
                        <span id="time"></span>
                    </span>
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
                this.updateTime();
                setInterval(() => this.updateTime(), 1000);
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

            updateTime() {
                const timeEl = document.getElementById('time');
                if (timeEl) timeEl.textContent = new Date().toLocaleTimeString();
            }
        }

        // Initialize sidebar when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            new Sidebar();
        });
    </script>

    @stack('scripts')
</body>
</html>