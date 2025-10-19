<!-- Navigation -->
<nav class="navbar" id="navbar">
    <div class="nav-container">
        <a href="{{ route('dashboard') }}" class="nav-logo">
            <img src="{{ asset('images/logo.png') }}" alt="MCC Logo">
            <span>MCC E&PO</span>
        </a>

        <ul class="nav-menu" id="navMenu">
            <!-- Certificates Link -->
            <li>
                <a href="{{ route('certificates') }}">
                    <i class="fas fa-medal"></i>
                    Certificates
                </a>
            </li>

            <!-- Dashboard Link -->
            <li>
                <a href="{{ route('dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i>
                    Home
                </a>
            </li>

            <!-- Department Dropdown -->
            <li class="dropdown" id="deptDropdown">
                <a href="#" class="dropdown-toggle">
                    <i class="fas fa-graduation-cap"></i>
                    <span id="deptLabel">
                        @if(request('department'))
                            {{ request('department') }}
                        @else
                            Departments
                        @endif
                    </span>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </a>
                <div class="dropdown-menu">
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
                           class="dropdown-item {{ request('department') === $code ? 'active' : '' }}">
                            <i class="fas fa-graduation-cap"></i>
                            <div class="dept-info">
                                <div class="dept-code">{{ $code }}</div>
                                <div class="dept-name">{{ $name }}</div>
                            </div>
                        </a>
                    @endforeach

                    <div class="dropdown-divider"></div>
                    <a href="{{ route('dashboard', request()->except('department')) }}"
                       class="dropdown-item clear-filter">
                        <i class="fas fa-times"></i>
                        Clear Filter
                    </a>
                </div>
            </li>

            <!-- User Dropdown -->
            <li class="dropdown" id="userDropdown">
                <a href="#" class="dropdown-toggle user-toggle">
                    <i class="fas fa-user-circle"></i>
                    {{ auth()->user()->first_name }}
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a href="{{ route('profile.edit') }}" class="dropdown-item">
                        <i class="fas fa-user"></i>
                        Profile
                    </a>
                    <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                        @csrf
                        <button type="submit" class="dropdown-item logout">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </li>
        </ul>

        <button class="mobile-toggle" id="mobileToggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>
</nav>