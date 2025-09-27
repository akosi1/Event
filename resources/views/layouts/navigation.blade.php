<!-- Navigation -->
<nav class="navbar">
    <div class="nav-container">
        <a href="{{ route('dashboard') }}" class="nav-brand">
            <i class="fas fa-calendar-alt"></i>
            EventAps
        </a>
        
        <div class="nav-content">
            <!-- Dashboard Button -->
            <a href="{{ route('dashboard') }}" class="nav-btn">
                <i class="fas fa-tachometer-alt"></i>
                Dashboard
            </a>
            
            <!-- Department Filter -->
            <div class="dropdown" id="deptDropdown">
                <button class="dropdown-btn" onclick="toggleDropdown('deptDropdown')">
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
                    
                    <div style="height: 1px; background: #e2e8f0; margin: 0.5rem 0;"></div>
                    <a href="{{ route('dashboard', request()->except('department')) }}" 
                       class="dropdown-item logout">
                        <i class="fas fa-times"></i>
                        Clear Filter
                    </a>
                </div>
            </div>
            
            <!-- User Menu -->
            <div class="dropdown" id="userDropdown">
                <button class="dropdown-btn user-btn" onclick="toggleDropdown('userDropdown')">
                    <i class="fas fa-user-circle"></i>
                    {{ auth()->user()->first_name }}
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="dropdown-menu right">
                    <a href="{{ route('profile.edit') }}" class="dropdown-item">
                        <i class="fas fa-user"></i>
                        Profile
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item logout">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
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