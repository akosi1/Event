<!-- Navigation -->
<nav class="navbar" role="navigation" aria-label="Main navigation">
    <div class="nav-container">
        <a href="{{ route('dashboard') }}" class="nav-brand">
            <i class="fas fa-calendar-alt"></i>
            EventAps
        </a>

        <!-- Mobile Toggle Button (Hidden on Desktop) -->
        <button class="mobile-toggle" aria-label="Toggle navigation menu">
            <i class="fas fa-bars"></i>
        </button>

        <div class="nav-content">
            <!-- Dashboard Button -->
            <a href="{{ route('dashboard') }}" class="nav-btn {{ request()->routeIs('dashboard') && !request('department') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i>
                Dashboard
            </a>

            <!-- Department Filter -->
            <div class="dropdown" id="deptDropdown">
                <button class="dropdown-btn {{ request('department') ? 'active' : '' }}" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-graduation-cap"></i>
                    <span id="deptLabel">
                        @if(request('department'))
                            {{ request('department') }}
                        @else
                            Departments
                        @endif
                    </span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="dropdown-menu" role="menu">
                    <div class="dropdown-header">Select Department</div>
                    @php
                        $departments = [
                            'BSIT' => 'Information Technology',
                            'BSBA' => 'Business Administration',
                            'BSED' => 'Science in Education',
                            'BEED' => 'Elementary Education',
                            'BSHM' => 'Hospitality Management'
                        ];
                    @endphp

                    @foreach($departments as $code => $name)
                        <a href="{{ route('dashboard', array_merge(request()->query(), ['department' => $code])) }}"
                           class="dropdown-item {{ request('department') === $code ? 'active' : '' }}"
                           role="menuitem">
                            <i class="fas fa-graduation-cap"></i>
                            <div class="dept-info">
                                <div class="dept-code">{{ $code }}</div>
                                <div class="dept-name">{{ $name }}</div>
                            </div>
                        </a>
                    @endforeach

                    <div class="dropdown-divider"></div>
                    <a href="{{ route('dashboard', request()->except('department')) }}"
                       class="dropdown-item logout"
                       role="menuitem">
                        <i class="fas fa-times"></i>
                        Clear Filter
                    </a>
                </div>
            </div>

            <!-- User Menu -->
            <div class="dropdown" id="userDropdown">
                <button class="dropdown-btn user-btn" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-user-circle"></i>
                    {{ auth()->user()->first_name }}
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="dropdown-menu right" role="menu">
                    <a href="{{ route('profile.edit') }}" class="dropdown-item" role="menuitem">
                        <i class="fas fa-user"></i>
                        Profile
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item logout" role="menuitem">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Overlay for Mobile -->
<div class="mobile-overlay" aria-hidden="true"></div>

<!-- Filter Status Banner -->
@if(request('department'))
    <div class="filter-status">
        <div class="filter-status-content">
            <i class="fas fa-filter"></i>
            <span>Filtering by: <strong>{{ request('department') }} - {{ $departments[request('department')] ?? 'Unknown' }}</strong></span>
        </div>
        <a href="{{ route('dashboard', request()->except('department')) }}" class="filter-close" aria-label="Clear filter">
            <i class="fas fa-times"></i>
        </a>
    </div>
@endif