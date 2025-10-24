<!-- Navigation -->
<nav class="navbar" id="navbar">
    <div class="nav-container">
        <a href="{{ route('dashboard') }}" class="nav-logo">
            <img src="{{ asset('images/logo.png') }}" alt="MCC Logo">
            <span>MCC E&PO</span>
        </a>

        <button class="mobile-toggle" id="mobileToggle" aria-label="Toggle Navigation">
            <i class="fas fa-bars"></i>
        </button>

        <div class="nav-content" id="navContent">
            
            <a href="{{ route('dashboard') }}" class="nav-btn" data-nav-link>
                <i class="fas fa-home"></i>
                Home
            </a>

            <div class="dropdown" id="deptDropdown">
                <button class="dropdown-btn" type="button" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-graduation-cap"></i>
                    <span id="deptLabel">
                        @if(request('department'))
                            {{ request('department') }}
                        @else
                            Departments
                        @endif
                    </span>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
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
                       class="dropdown-item"
                       role="menuitem">
                        <i class="fas fa-times"></i>
                        Clear Filter
                    </a>
                </div>
            </div>

            <a href="{{ route('certificates') }}" class="nav-btn" data-nav-link>
                <i class="fas fa-certificate"></i>
                Certificates
            </a>
            
            <div class="dropdown" id="userDropdown">
                <button class="dropdown-btn user-btn" type="button" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-user-circle"></i>
                    {{ auth()->user()->first_name }}
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right" role="menu">
                    <a href="{{ route('profile.edit') }}" class="dropdown-item" role="menuitem">
                        <i class="fas fa-user"></i>
                        Profile
                    </a>
                    <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
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

<div class="mobile-overlay" id="mobileOverlay"></div>