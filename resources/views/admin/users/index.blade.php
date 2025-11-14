@extends('admin.layouts.app')

@section('title', 'Users Management')
@section('page-title', 'Users Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <!-- Header -->
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0 text-dark fw-semibold">
                                <i class="fas fa-users me-2 text-primary"></i>All Users
                            </h4>
                            <small class="text-muted">Manage system users and their roles</small>
                        </div>
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm px-3">
                            <i class="fas fa-plus me-1"></i>Add New User
                        </a>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card-body border-bottom bg-light py-3">
                    <form method="GET" action="{{ route('admin.users.index') }}" id="searchForm">
                        <div class="row g-3 align-items-end">
                            <div class="col-lg-6 col-md-8">
                                <label class="form-label fw-medium text-dark mb-1">Search Users</label>
                                <div class="position-relative">
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control" 
                                               name="search"
                                               id="searchInput"
                                               value="{{ request('search') }}"
                                               placeholder="Search by name, email, department, or ID..."
                                               autocomplete="off">
                                        <button class="btn btn-outline-primary" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                    <!-- Autocomplete Dropdown -->
                                    <div id="autocompleteDropdown" class="autocomplete-dropdown">
                                        <div id="autocompleteResults"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-4">
                                <label class="form-label fw-medium text-dark mb-1">Items Per Page</label>
                                <select class="form-select" name="per_page" onchange="this.form.submit()">
                                    @foreach([10, 25, 50, 100] as $option)
                                        <option value="{{ $option }}" {{ request('per_page', 10) == $option ? 'selected' : '' }}>
                                            {{ $option }} items
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-3">
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary w-100" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-sort me-1"></i>Sort By
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort_by' => 'created_at', 'sort_order' => 'desc']) }}">
                                            <i class="fas fa-clock me-2"></i>Newest First
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort_by' => 'created_at', 'sort_order' => 'asc']) }}">
                                            <i class="fas fa-clock me-2"></i>Oldest First
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort_by' => 'first_name', 'sort_order' => 'asc']) }}">
                                            <i class="fas fa-sort-alpha-down me-2"></i>Name A-Z
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort_by' => 'department', 'sort_order' => 'asc']) }}">
                                            <i class="fas fa-building me-2"></i>Department
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort_by' => 'year_level', 'sort_order' => 'asc']) }}">
                                            <i class="fas fa-graduation-cap me-2"></i>Year Level
                                        </a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Results Summary -->
                <div class="card-body py-2 bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted fw-medium">
                            <i class="fas fa-info-circle me-1"></i>
                            Showing {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users
                        </small>
                        @if(request('search'))
                            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Clear Filters
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Content -->
                <div class="card-body p-0">
                    @if($users->count() > 0)
                        <!-- Mobile Cards View -->
                        <div class="d-block d-lg-none p-3">
                            <div class="row g-3">
                                @foreach($users as $user)
                                <div class="col-12">
                                    <div class="card h-100 border shadow-sm">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-start">
                                                <!-- Avatar / Profile Picture -->
                                                <div class="flex-shrink-0 me-3">
                                                    @if($user->profile_picture)
                                                        <img src="{{ $user->profile_picture_url }}" 
                                                             alt="{{ $user->full_name }}"
                                                             class="rounded-circle"
                                                             style="width: 50px; height: 50px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                                             style="width: 50px; height: 50px;">
                                                            {{ $user->initials }}
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- User Info -->
                                                <div class="flex-grow-1">
                                                    <h6 class="card-title mb-1 fw-semibold">{{ $user->full_name_with_initial }}</h6>
                                                    <p class="text-muted small mb-2">
                                                        <i class="fas fa-envelope me-1"></i>{{ $user->email }}
                                                    </p>
                                                    <p class="text-muted small mb-2">
                                                        <i class="fas fa-id-card me-1"></i>{{ $user->id_number }}
                                                    </p>

                                                    <div class="d-flex gap-2 mb-2 flex-wrap">
                                                        <span class="badge bg-info text-white">
                                                            <i class="fas fa-building me-1"></i>{{ $user->department }}
                                                        </span>
                                                        <span class="badge bg-purple text-white">
                                                            <i class="fas fa-graduation-cap me-1"></i>{{ $user->year_level_name }}
                                                        </span>
                                                        <span class="badge bg-{{ $user->role == 'admin' ? 'primary' : 'secondary' }} text-white">
                                                            <i class="fas fa-user-tag me-1"></i>{{ ucfirst($user->role) }}
                                                        </span>
                                                        <span class="badge bg-{{ $user->status == 'active' ? 'success' : 'danger' }} text-white">
                                                            <i class="fas fa-circle me-1" style="font-size: 8px;"></i>{{ ucfirst($user->status) }}
                                                        </span>
                                                    </div>

                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar me-1"></i>{{ $user->created_at->format('M d, Y') }}
                                                    </small>
                                                </div>

                                                <!-- Actions -->
                                                <div class="flex-shrink-0">
                                                    <div class="d-flex gap-1">
                                                        <a href="{{ route('admin.users.edit', $user) }}"
                                                           class="btn btn-sm btn-outline-warning"
                                                           style="width: 36px; height: 36px;"
                                                           title="Edit User">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        @if($user->id != Auth::id())
                                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="btn btn-sm btn-outline-danger"
                                                                    style="width: 36px; height: 36px;"
                                                                    title="Delete User"
                                                                    onclick="return confirm('Are you sure you want to delete {{ $user->full_name }}?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Desktop Table View -->
                        <div class="d-none d-lg-block">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="border-0 fw-semibold text-dark" style="width: 60px;">#</th>
                                            <th class="border-0 fw-semibold text-dark">User Information</th>
                                            <th class="border-0 fw-semibold text-dark" style="width: 120px;">ID Number</th>
                                            <th class="border-0 fw-semibold text-dark" style="width: 120px;">Department</th>
                                            <th class="border-0 fw-semibold text-dark" style="width: 100px;">Year</th>
                                            <th class="border-0 fw-semibold text-dark" style="width: 100px;">Role</th>
                                            <th class="border-0 fw-semibold text-dark" style="width: 100px;">Status</th>
                                            <th class="border-0 fw-semibold text-dark text-center" style="width: 100px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($users as $user)
                                        <tr class="border-bottom">
                                            <td class="text-muted fw-bold">#{{ $user->id }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($user->profile_picture)
                                                        <img src="{{ $user->profile_picture_url }}" 
                                                             alt="{{ $user->full_name }}"
                                                             class="rounded-circle me-3"
                                                             style="width: 45px; height: 45px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 fw-bold"
                                                             style="width: 45px; height: 45px;">
                                                            {{ $user->initials }}
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="fw-semibold text-dark mb-1">{{ $user->full_name_with_initial }}</div>
                                                        <small class="text-muted">
                                                            <i class="fas fa-envelope me-1"></i>{{ $user->email }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border px-2 py-1">
                                                    {{ $user->id_number }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info text-white px-2 py-1">
                                                    {{ $user->department }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-purple text-white px-2 py-1">
                                                    {{ $user->year_level_name }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $user->role == 'admin' ? 'primary' : 'secondary' }} text-white px-2 py-1">
                                                    {{ ucfirst($user->role) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $user->status == 'active' ? 'success' : 'danger' }} text-white px-2 py-1">
                                                    {{ ucfirst($user->status) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <a href="{{ route('admin.users.edit', $user) }}"
                                                       class="btn btn-sm btn-outline-warning d-flex align-items-center justify-content-center"
                                                       style="width: 32px; height: 32px;"
                                                       title="Edit User">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if($user->id != Auth::id())
                                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="btn btn-sm btn-outline-danger d-flex align-items-center justify-content-center"
                                                                style="width: 32px; height: 32px;"
                                                                title="Delete User"
                                                                onclick="return confirm('Are you sure you want to delete {{ $user->full_name }}?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Enhanced Pagination -->
                        <div class="card-footer bg-white border-top">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                                <div class="text-muted">
                                    <small class="fw-medium">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Page {{ $users->currentPage() }} of {{ $users->lastPage() }}
                                        ({{ number_format($users->total()) }} total users)
                                    </small>
                                </div>

                                <nav aria-label="Users pagination">
                                    @if ($users->hasPages())
                                        <ul class="pagination pagination-sm mb-0">
                                            {{-- Previous Page Link --}}
                                            @if ($users->onFirstPage())
                                                <li class="page-item disabled">
                                                    <span class="page-link">
                                                        <i class="fas fa-chevron-left me-1"></i>Previous
                                                    </span>
                                                </li>
                                            @else
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $users->previousPageUrl() }}">
                                                        <i class="fas fa-chevron-left me-1"></i>Previous
                                                    </a>
                                                </li>
                                            @endif

                                            {{-- Pagination Elements --}}
                                            @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                                                @if ($page == $users->currentPage())
                                                    <li class="page-item active">
                                                        <span class="page-link bg-primary border-primary">{{ $page }}</span>
                                                    </li>
                                                @else
                                                    <li class="page-item">
                                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                                    </li>
                                                @endif
                                            @endforeach

                                            {{-- Next Page Link --}}
                                            @if ($users->hasMorePages())
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $users->nextPageUrl() }}">
                                                        Next<i class="fas fa-chevron-right ms-1"></i>
                                                    </a>
                                                </li>
                                            @else
                                                <li class="page-item disabled">
                                                    <span class="page-link">
                                                        Next<i class="fas fa-chevron-right ms-1"></i>
                                                    </span>
                                                </li>
                                            @endif
                                        </ul>
                                    @endif
                                </nav>
                            </div>
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-users fa-4x text-muted opacity-50"></i>
                            </div>
                            @if(request()->hasAny(['search']))
                                <h4 class="text-muted mb-2">No users found</h4>
                                <p class="text-muted mb-4">No users match your search criteria. Try a different search term.</p>
                                <a href="{{ route('admin.users.index') }}" class="btn btn-primary">
                                    <i class="fas fa-arrow-left me-1"></i>View All Users
                                </a>
                            @else
                                <h4 class="text-muted mb-2">No users yet</h4>
                                <p class="text-muted mb-4">Get started by creating your first user account.</p>
                                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>Create First User
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 12px;
    overflow: hidden;
}

.card-header {
    border-radius: 12px 12px 0 0 !important;
}

.table th {
    font-weight: 600;
    font-size: 13px;
    letter-spacing: 0.5px;
}

.table td {
    font-size: 13px;
    padding: 0.75rem 0.5rem;
}

.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

.badge {
    font-weight: 500;
    letter-spacing: 0.25px;
    font-size: 11px;
}

.bg-purple {
    background-color: #6f42c1 !important;
}

.pagination .page-link {
    border-radius: 8px;
    margin: 0 2px;
    font-weight: 500;
}

.pagination .page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

/* Autocomplete Dropdown Styles */
.autocomplete-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 1000;
    max-height: 400px;
    overflow-y: auto;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 0 0 8px 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    display: none;
    margin-top: 2px;
}

.autocomplete-dropdown.show {
    display: block;
}

.autocomplete-item {
    padding: 12px 16px;
    cursor: pointer;
    border-bottom: 1px solid #f0f0f0;
    transition: background-color 0.2s ease;
}

.autocomplete-item:last-child {
    border-bottom: none;
}

.autocomplete-item:hover {
    background-color: #f8f9fa;
}

.autocomplete-item.active {
    background-color: #e7f3ff;
}

.autocomplete-user-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.autocomplete-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
}

.autocomplete-avatar-text {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #0d6efd;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
    flex-shrink: 0;
}

.autocomplete-details {
    flex: 1;
    min-width: 0;
}

.autocomplete-name {
    font-weight: 600;
    color: #212529;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.autocomplete-meta {
    font-size: 12px;
    color: #6c757d;
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.autocomplete-meta span {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.autocomplete-no-results {
    padding: 20px;
    text-align: center;
    color: #6c757d;
}

.autocomplete-loading {
    padding: 20px;
    text-align: center;
    color: #6c757d;
}

.highlight {
    background-color: #fff3cd;
    font-weight: 600;
    padding: 0 2px;
}

@media (max-width: 768px) {
    .container-fluid {
        padding: 0.5rem;
    }

    .card-body {
        padding: 1rem;
    }
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const autocompleteDropdown = document.getElementById('autocompleteDropdown');
    const autocompleteResults = document.getElementById('autocompleteResults');
    const searchForm = document.getElementById('searchForm');
    
    let currentFocus = -1;
    let searchTimeout = null;

    // Search function with debounce
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const searchTerm = this.value.trim();

        if (searchTerm.length < 1) {
            hideDropdown();
            return;
        }

        // Show loading state
        showLoading();
        showDropdown();

        // Debounce search
        searchTimeout = setTimeout(() => {
            fetchSearchResults(searchTerm);
        }, 300);
    });

    // Fetch search results
    function fetchSearchResults(term) {
        fetch(`{{ route('admin.users.index') }}?search=${encodeURIComponent(term)}&autocomplete=1`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            displayResults(data.users, term);
        })
        .catch(error => {
            console.error('Search error:', error);
            showError();
        });
    }

    // Display results
    function displayResults(users, searchTerm) {
        if (users.length === 0) {
            showNoResults();
            return;
        }

        let html = '';
        users.forEach(user => {
            const highlightedName = highlightText(user.full_name, searchTerm);
            const highlightedEmail = highlightText(user.email, searchTerm);
            const highlightedId = highlightText(user.id_number, searchTerm);

            html += `
                <div class="autocomplete-item" data-search="${user.full_name}" onclick="selectUser('${escapeHtml(user.full_name)}')">
                    <div class="autocomplete-user-info">
                        ${user.profile_picture ? 
                            `<img src="${user.profile_picture}" class="autocomplete-avatar" alt="${user.full_name}">` :
                            `<div class="autocomplete-avatar-text">${user.initials}</div>`
                        }
                        <div class="autocomplete-details">
                            <div class="autocomplete-name">${highlightedName}</div>
                            <div class="autocomplete-meta">
                                <span><i class="fas fa-envelope"></i> ${highlightedEmail}</span>
                                <span><i class="fas fa-id-card"></i> ${highlightedId}</span>
                                <span><i class="fas fa-building"></i> ${user.department}</span>
                                <span class="badge bg-${user.role === 'admin' ? 'primary' : 'secondary'} text-white">${user.role}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        autocompleteResults.innerHTML = html;
        showDropdown();
    }

    // Highlight matching text
    function highlightText(text, search) {
        if (!search) return text;
        const regex = new RegExp(`(${escapeRegExp(search)})`, 'gi');
        return text.replace(regex, '<span class="highlight">$1</span>');
    }

    // Escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Escape RegExp special characters
    function escapeRegExp(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    // Show loading state
    function showLoading() {
        autocompleteResults.innerHTML = `
            <div class="autocomplete-loading">
                <i class="fas fa-spinner fa-spin me-2"></i>Searching...
            </div>
        `;
    }

    // Show no results
    function showNoResults() {
        autocompleteResults.innerHTML = `
            <div class="autocomplete-no-results">
                <i class="fas fa-search me-2"></i>No users found
            </div>
        `;
    }

    // Show error
    function showError() {
        autocompleteResults.innerHTML = `
            <div class="autocomplete-no-results text-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>Error loading results
            </div>
        `;
    }

    // Select user from dropdown
    window.selectUser = function(userName) {
        searchInput.value = userName;
        hideDropdown();
        searchForm.submit();
    };

    // Show dropdown
    function showDropdown() {
        autocompleteDropdown.classList.add('show');
    }

    // Hide dropdown
    function hideDropdown() {
        autocompleteDropdown.classList.remove('show');
        currentFocus = -1;
    }

    // Keyboard navigation
    searchInput.addEventListener('keydown', function(e) {
        const items = autocompleteResults.querySelectorAll('.autocomplete-item');
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            currentFocus++;
            addActive(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            currentFocus--;
            addActive(items);
        } else if (e.key === 'Enter') {
            if (currentFocus > -1 && items[currentFocus]) {
                e.preventDefault();
                items[currentFocus].click();
            }
        } else if (e.key === 'Escape') {
            hideDropdown();
        }
    });

    // Add active class to current item
    function addActive(items) {
        if (!items || items.length === 0) return;
        
        removeActive(items);
        
        if (currentFocus >= items.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = items.length - 1;
        
        items[currentFocus].classList.add('active');
        items[currentFocus].scrollIntoView({ block: 'nearest' });
    }

    // Remove active class from all items
    function removeActive(items) {
        items.forEach(item => item.classList.remove('active'));
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !autocompleteDropdown.contains(e.target)) {
            hideDropdown();
        }
    });

    // Focus on search input
    searchInput.addEventListener('focus', function() {
        if (this.value.trim().length >= 1 && autocompleteResults.innerHTML) {
            showDropdown();
        }
    });
});
</script>
@endpush
@endsection