@extends('admin.layouts.app')

@section('title', 'Users Report')
@section('page-title', 'Users Report')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 fw-bold text-dark">Users Report</h5>
                <button onclick="printReport()" class="btn btn-success btn-sm">
                    <i class="fas fa-print me-1"></i> Print Report
                </button>
            </div>
            
            <!-- Clean Filters Section -->
            <div class="card-body border-bottom bg-light">
                <form method="GET" id="reportForm">
                    <div class="row g-3 align-items-end">
                        <!-- Search -->
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label fw-semibold small">Search Users</label>
                            <input type="text" class="form-control form-control-sm" name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Name, email, role...">
                        </div>
                        
                        <!-- Role Filter -->
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label fw-semibold small">Role</label>
                            <select class="form-select form-select-sm" name="role">
                                <option value="">All Roles</option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
                            </select>
                        </div>
                        
                        <!-- Status Filter -->
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label fw-semibold small">Status</label>
                            <select class="form-select form-select-sm" name="status">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        
                        <!-- Date From -->
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label fw-semibold small">From Date</label>
                            <input type="date" class="form-control form-control-sm" name="date_from" 
                                   value="{{ request('date_from') }}">
                        </div>
                        
                        <!-- Date To -->
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label fw-semibold small">To Date</label>
                            <input type="date" class="form-control form-control-sm" name="date_to" 
                                   value="{{ request('date_to') }}">
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="col-lg-1 col-md-6">
                            <div class="d-flex gap-1">
                                <button type="submit" class="btn btn-primary btn-sm" title="Apply Filter">
                                    <i class="fas fa-filter"></i>
                                </button>
                                <a href="{{ route('admin.reports.users') }}" class="btn btn-outline-secondary btn-sm" title="Clear Filters">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Results Section -->
            <div class="card-body">
                <!-- Header with Count and Sort -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h6 class="mb-0 text-muted">
                            <i class="fas fa-users me-2"></i>
                            Total Users: <span class="text-dark fw-bold">{{ $users->count() }}</span>
                        </h6>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-sort me-1"></i> Sort By
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort_by' => 'created_at', 'sort_order' => 'desc']) }}">
                                <i class="fas fa-clock me-2"></i>Date Created (Newest)
                            </a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort_by' => 'created_at', 'sort_order' => 'asc']) }}">
                                <i class="fas fa-clock me-2"></i>Date Created (Oldest)
                            </a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort_by' => 'name', 'sort_order' => 'asc']) }}">
                                <i class="fas fa-sort-alpha-down me-2"></i>Name (A-Z)
                            </a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort_by' => 'name', 'sort_order' => 'desc']) }}">
                                <i class="fas fa-sort-alpha-up me-2"></i>Name (Z-A)
                            </a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort_by' => 'email', 'sort_order' => 'asc']) }}">
                                <i class="fas fa-envelope me-2"></i>Email (A-Z)
                            </a></li>
                        </ul>
                    </div>
                </div>
                
                @if($users->count() > 0)
                    <!-- Clean Data Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="fw-semibold" style="width: 60px;">#</th>
                                    <th class="fw-semibold">User</th>
                                    <th class="fw-semibold">Email</th>
                                    <th class="fw-semibold text-center" style="width: 100px;">Role</th>
                                    <th class="fw-semibold text-center" style="width: 100px;">Status</th>
                                    <th class="fw-semibold text-center" style="width: 130px;">Created</th>
                                    <th class="fw-semibold text-center" style="width: 130px;">Updated</th>
                                    <th class="fw-semibold text-center" style="width: 100px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td class="text-muted fw-medium">{{ $user->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle bg-primary text-white me-3">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-dark">{{ $user->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-muted">{{ $user->email }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $user->role == 'admin' ? 'primary' : 'secondary' }} bg-opacity-10 text-{{ $user->role == 'admin' ? 'primary' : 'secondary' }} border border-{{ $user->role == 'admin' ? 'primary' : 'secondary' }} border-opacity-25">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $user->status == 'active' ? 'success' : 'danger' }} bg-opacity-10 text-{{ $user->status == 'active' ? 'success' : 'danger' }} border border-{{ $user->status == 'active' ? 'success' : 'danger' }} border-opacity-25">
                                            {{ ucfirst($user->status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="small text-dark fw-medium">{{ $user->created_at->format('M d, Y') }}</div>
                                        <div class="small text-muted">{{ $user->created_at->format('g:i A') }}</div>
                                    </td>
                                    <td class="text-center">
                                        <div class="small text-dark fw-medium">{{ $user->updated_at->format('M d, Y') }}</div>
                                        <div class="small text-muted">{{ $user->updated_at->format('g:i A') }}</div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <button class="btn btn-outline-primary btn-sm" title="Edit User">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-outline-danger btn-sm" 
                                                    onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')" 
                                                    title="Delete User">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-search fa-3x text-muted"></i>
                        </div>
                        <h6 class="text-muted mb-2">No users found</h6>
                        <p class="text-muted small">Try adjusting your search criteria or filters</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Hidden Form for Delete -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    font-weight: bold;
    flex-shrink: 0;
}

.table th {
    border-top: none;
    border-bottom: 2px solid #dee2e6;
    font-size: 0.875rem;
    color: #495057;
}

.table td {
    border-top: 1px solid #f8f9fa;
    font-size: 0.875rem;
    padding: 1rem 0.75rem;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
    font-weight: 500;
}

/* SweetAlert2 Custom Styles */
.swal2-popup {
    width: 400px !important;
    max-width: 90% !important;
    border-radius: 15px !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
}

.swal2-title {
    font-size: 1.5rem !important;
    font-weight: 600 !important;
    color: #2d3748 !important;
}

.swal2-html-container {
    font-size: 1rem !important;
    color: #4a5568 !important;
    margin: 1rem 0 !important;
}

.swal2-confirm {
    background-color: #e53e3e !important;
    border: none !important;
    border-radius: 8px !important;
    padding: 10px 25px !important;
    font-weight: 500 !important;
    font-size: 0.95rem !important;
    margin: 0 5px !important;
}

.swal2-confirm:hover {
    background-color: #c53030 !important;
}

.swal2-cancel {
    background-color: #718096 !important;
    border: none !important;
    border-radius: 8px !important;
    padding: 10px 25px !important;
    font-weight: 500 !important;
    font-size: 0.95rem !important;
    margin: 0 5px !important;
}

.swal2-cancel:hover {
    background-color: #4a5568 !important;
}

.swal2-icon.swal2-warning {
    color: #f56565 !important;
    border-color: #f56565 !important;
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.8rem;
    }
    
    .avatar-circle {
        width: 35px;
        height: 35px;
        font-size: 14px;
    }
    
    .swal2-popup {
        width: 320px !important;
    }
}
</style>
@endsection

@push('scripts')
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function printReport() {
    const form = document.getElementById('reportForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    
    // Remove empty parameters
    for (let [key, value] of params.entries()) {
        if (!value) {
            params.delete(key);
        }
    }
    
    window.open(`{{ route('admin.reports.users.print') }}?${params.toString()}`, '_blank');
}

function confirmDelete(userId, userName) {
    Swal.fire({
        title: 'Delete User?',
        html: `Are you sure you want to delete <strong>${userName}</strong>?<br><span style="color: #e53e3e; font-size: 0.9rem;">This action cannot be undone.</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        focusCancel: true,
        allowOutsideClick: false,
        allowEscapeKey: true,
        customClass: {
            popup: 'swal2-popup',
            title: 'swal2-title',
            htmlContainer: 'swal2-html-container',
            confirmButton: 'swal2-confirm',
            cancelButton: 'swal2-cancel'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
                title: 'Deleting...',
                text: 'Please wait while we delete the user.',
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Set the form action and submit
            const form = document.getElementById('deleteForm');
            form.action = `{{ route('admin.users.destroy', ':id') }}`.replace(':id', userId);
            form.submit();
        }
    });
}

// Success message after redirect (if you want to show success message)
@if(session('success'))
    Swal.fire({
        title: 'Success!',
        text: '{{ session('success') }}',
        icon: 'success',
        timer: 3000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
@endif

// Error message after redirect (if you want to show error message)
@if(session('error'))
    Swal.fire({
        title: 'Error!',
        text: '{{ session('error') }}',
        icon: 'error',
        confirmButtonText: 'OK'
    });
@endif
</script>
@endpush