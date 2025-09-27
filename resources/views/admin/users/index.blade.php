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
                    <form method="GET" action="{{ route('admin.users.index') }}">
                        <div class="row g-3 align-items-end">
                            <div class="col-lg-6 col-md-8">
                                <label class="form-label fw-medium text-dark mb-1">Search Users</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="search" 
                                           value="{{ request('search') }}" 
                                           placeholder="Search by name, email, or role...">
                                    <button class="btn btn-outline-primary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
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
                                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort_by' => 'email', 'sort_order' => 'asc']) }}">
                                            <i class="fas fa-envelope me-2"></i>Email A-Z
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
                                                <!-- Avatar -->
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" 
                                                         style="width: 50px; height: 50px;">
                                                        {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                                                    </div>
                                                </div>
                                                
                                                <!-- User Info -->
                                                <div class="flex-grow-1">
                                                    <h6 class="card-title mb-1 fw-semibold">
                                                        {{ $user->first_name }} 
                                                        @if($user->middle_name){{ strtoupper(substr($user->middle_name, 0, 1)) }}.@endif 
                                                        {{ $user->last_name }}
                                                    </h6>
                                                    <p class="text-muted small mb-2">
                                                        <i class="fas fa-envelope me-1"></i>{{ $user->email }}
                                                    </p>
                                                    
                                                    <div class="d-flex gap-2 mb-2">
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
                                                                    onclick="return confirm('Are you sure you want to delete {{ $user->first_name }} {{ $user->last_name }}?')">
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
                                            <th class="border-0 fw-semibold text-dark" style="width: 80px;">#</th>
                                            <th class="border-0 fw-semibold text-dark">User Information</th>
                                            <th class="border-0 fw-semibold text-dark" style="width: 120px;">Role</th>
                                            <th class="border-0 fw-semibold text-dark" style="width: 120px;">Status</th>
                                            <th class="border-0 fw-semibold text-dark" style="width: 150px;">Date Created</th>
                                            <th class="border-0 fw-semibold text-dark text-center" style="width: 120px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($users as $user)
                                        <tr class="border-bottom">
                                            <td class="text-muted fw-bold">#{{ $user->id }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 fw-bold" 
                                                         style="width: 45px; height: 45px;">
                                                        {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold text-dark mb-1">
                                                            {{ $user->first_name }} 
                                                            @if($user->middle_name){{ strtoupper(substr($user->middle_name, 0, 1)) }}.@endif 
                                                            {{ $user->last_name }}
                                                        </div>
                                                        <small class="text-muted">
                                                            <i class="fas fa-envelope me-1"></i>{{ $user->email }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $user->role == 'admin' ? 'primary' : 'secondary' }} text-white px-3 py-2">
                                                    <i class="fas fa-user-tag me-1"></i>{{ ucfirst($user->role) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $user->status == 'active' ? 'success' : 'danger' }} text-white px-3 py-2">
                                                    <i class="fas fa-circle me-1" style="font-size: 8px;"></i>{{ ucfirst($user->status) }}
                                                </span>
                                            </td>
                                            <td class="text-muted">
                                                <div>{{ $user->created_at->format('M d, Y') }}</div>
                                                <small>{{ $user->created_at->format('h:i A') }}</small>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <a href="{{ route('admin.users.edit', $user) }}" 
                                                       class="btn btn-sm btn-outline-warning d-flex align-items-center justify-content-center" 
                                                       style="width: 36px; height: 36px;"
                                                       title="Edit User">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if($user->id != Auth::id())
                                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-outline-danger d-flex align-items-center justify-content-center" 
                                                                style="width: 36px; height: 36px;"
                                                                title="Delete User"
                                                                onclick="return confirm('Are you sure you want to delete {{ $user->first_name }} {{ $user->last_name }}?')">
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
    font-size: 14px;
    letter-spacing: 0.5px;
}

.table td {
    font-size: 14px;
    padding: 1rem 0.75rem;
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

@media (max-width: 768px) {
    .container-fluid {
        padding: 0.5rem;
    }
    
    .card-body {
        padding: 1rem;
    }
}
</style>
@endsection