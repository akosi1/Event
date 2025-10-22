<!-- Navigation -->
<nav class="navbar" id="navbar">
    <div class="nav-container">
        <a href="{{ route('dashboard') }}" class="nav-brand">
            <img src="{{ asset('images/logo.png') }}" alt="MCC Logo">
            <span>MCC E&PO</span>
        </a>

        <!-- Mobile toggle will be inserted here by JavaScript -->

        <div class="nav-content">
            <!-- Dashboard Button -->
            <a href="{{ route('dashboard') }}" class="nav-btn">
                <i class="fas fa-tachometer-alt"></i>
                <span>Home</span>
            </a>

                        <!-- Department Filter -->
            <div class="dropdown" id="deptDropdown">
                <button class="dropdown-btn" type="button">
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

                    <div style="height: 1px; background: rgba(229, 62, 62, 0.2); margin: 0.5rem 0;"></div>
                    <a href="{{ route('dashboard', request()->except('department')) }}"
                       class="dropdown-item clear-filter">
                        <i class="fas fa-times"></i>
                        <span>Clear Filter</span>
                    </a>
                </div>
            </div>

                        <!-- Certificates Button -->
            <a href="{{ route('certificates') }}" class="nav-btn">
                <i class="fas fa-medal"></i>
                <span>Certificates</span>
            </a>


            <!-- User Menu -->
            <div class="dropdown" id="userDropdown">
                <button class="dropdown-btn user-btn" type="button">
                    <i class="fas fa-user-circle"></i>
                    <span>{{ auth()->user()->first_name }}</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="dropdown-menu right">
                    <a href="{{ route('profile.edit') }}" class="dropdown-item">
                        <i class="fas fa-user"></i>
                        <span>Profile</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                        @csrf
                        <button type="submit" class="dropdown-item logout">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Filter Status Banner -->
@if(request('department'))
    <div class="filter-status">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <i class="fas fa-filter"></i>
            <span>Filtering by: <strong>{{ request('department') }} - {{ $departments[request('department')] ?? '' }}</strong></span>
        </div>
        <a href="{{ route('dashboard', request()->except('department')) }}" class="filter-close">
            <i class="fas fa-times"></i>
        </a>
    </div>
@endif