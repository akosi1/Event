@extends('admin.layouts.app')
@section('title', 'Events Management')
@section('page-title', 'Events Management')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 fw-bold mb-1">
                <i class="fas fa-calendar-alt text-primary me-2"></i>Events Management
            </h2>
            <p class="text-muted mb-0">{{ $events->total() }} total events</p>
        </div>
        <div class="d-flex gap-2">
            <!-- Print Summary Button -->
            <a href="{{ route('admin.events.print', request()->query()) }}" 
               target="_blank" 
               class="btn btn-info text-white">
                <i class="fas fa-print me-2"></i>Print Summary
            </a>
            
            <!-- Add Event Button -->
            <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add Event
            </a>
        </div>
    </div>

    <!-- Enhanced Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" class="row g-3" id="filtersForm">
                <div class="col-md-3">
                    <div class="position-relative">
                        <input type="text" class="form-control" name="search" id="liveSearchInput"
                               value="{{ request('search') }}" placeholder="Search events..." autocomplete="off">
                        <div class="live-search-spinner position-absolute top-50 end-0 translate-middle-y me-3" 
                             id="searchSpinner" style="display: none;">
                            <i class="fas fa-spinner fa-spin text-primary"></i>
                        </div>
                        <button type="button" class="btn btn-sm position-absolute top-50 end-0 translate-middle-y me-2" 
                                id="clearSearchBtn" style="display: none; border: none; background: none;">
                            <i class="fas fa-times text-muted"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="status" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        @foreach(['active', 'postponed', 'cancelled'] as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="department" onchange="this.form.submit()">
                        <option value="">All Departments</option>
                        @foreach(['BSIT' => 'BSIT', 'BSBA' => 'BSBA', 'BSED' => 'BSED', 'BEED' => 'BEED', 'BSHM' => 'BSHM'] as $code => $name)
                            <option value="{{ $code }}" {{ request('department') == $code ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="year_level" onchange="this.form.submit()">
                        <option value="">All Year Levels</option>
                        <option value="1" {{ request('year_level') == '1' ? 'selected' : '' }}>1st Year</option>
                        <option value="2" {{ request('year_level') == '2' ? 'selected' : '' }}>2nd Year</option>
                        <option value="3" {{ request('year_level') == '3' ? 'selected' : '' }}>3rd Year</option>
                        <option value="4" {{ request('year_level') == '4' ? 'selected' : '' }}>4th Year</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="exclusivity" onchange="this.form.submit()">
                        <option value="">All Access Types</option>
                        <option value="open" {{ request('exclusivity') == 'open' ? 'selected' : '' }}>Open to All</option>
                        <option value="exclusive" {{ request('exclusivity') == 'exclusive' ? 'selected' : '' }}>Exclusive</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-outline-secondary w-100" id="resetFiltersBtn" title="Reset All Filters">
                        <i class="fas fa-redo"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($events->count())
        <!-- Search Results Counter -->
        <div class="search-results-info mb-3" id="searchResultsInfo" style="display: none;">
            <div class="alert alert-info mb-0 d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-info-circle me-2"></i>
                    <span id="searchResultsText"></span>
                </div>
                <button type="button" class="btn btn-sm btn-outline-info" id="clearSearchResults">
                    Clear Search
                </button>
            </div>
        </div>

        <!-- Events Table -->
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3 py-3">#</th>
                            <th class="py-3">Image</th>
                            <th class="py-3">Event Details</th>
                            <th class="py-3">Date & Time</th>
                            <th class="py-3">Access</th>
                            <th class="py-3">Type</th>
                            <th class="py-3">Status</th>
                            <th class="py-3">Participants</th>
                            <th class="py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="eventsTableBody">
                        @foreach($events as $event)
                        <tr class="event-row" data-searchable="{{ strtolower($event->title . ' ' . $event->description . ' ' . $event->location . ' #' . $event->id) }}">
                            <td class="ps-3">
                                <div>
                                    <span class="text-muted fw-medium">#{{ $event->id }}</span>
                                    @if($event->isRecurring())
                                        <br><small class="badge bg-info mt-1">Series</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div style="width: 60px; height: 60px; overflow: hidden; border-radius: 8px;">
                                    @if($event->image)
                                        <img src="{{ $event->image }}"
                                             alt="{{ $event->title }}"
                                             class="w-100 h-100 object-fit-cover"
                                             onerror="this.parentElement.innerHTML='<div class=\'d-flex align-items-center justify-content-center bg-light w-100 h-100\'><i class=\'fas fa-image text-muted\'></i></div>'">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center bg-light w-100 h-100">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td style="max-width: 300px;">
                                <h6 class="mb-1 fw-semibold">{{ Str::limit($event->title, 40) }}</h6>
                                <p class="text-muted mb-1 small">{{ Str::limit($event->description, 60) }}</p>
                                @if($event->isRecurring())
                                    <small class="text-info">
                                        <i class="fas fa-redo me-1"></i>{{ $event->recurrence_display }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <div class="fw-medium">{{ $event->date->format('M d, Y') }}</div>
                                @if($event->start_time)
                                    <small class="text-muted">
                                        {{ $event->start_time->format('h:i A') }}
                                        @if($event->end_time)
                                            - {{ $event->end_time->format('h:i A') }}
                                        @endif
                                    </small>
                                @endif
                            </td>
                            <td>
                                @if($event->is_exclusive)
                                    <span class="badge bg-warning text-dark mb-1" title="{{ $event->department_display }}">
                                        <i class="fas fa-lock me-1"></i>Exclusive
                                    </span>
                                    <br>
                                    <small class="text-muted d-block">{{ Str::limit($event->department_display, 20) }}</small>
                                    @if($event->allowed_year_levels && count($event->allowed_year_levels) > 0)
                                        <small class="text-muted d-block">
                                            <i class="fas fa-graduation-cap me-1"></i>
                                            @foreach($event->allowed_year_levels as $year)
                                                {{ $year }}{{ !$loop->last ? ',' : '' }}
                                            @endforeach
                                            Year
                                        </small>
                                    @endif
                                @else
                                    <span class="badge bg-success">
                                        <i class="fas fa-globe me-1"></i>Open to All
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($event->is_recurring)
                                    <span class="badge bg-info mb-1">
                                        <i class="fas fa-redo me-1"></i>Recurring
                                    </span>
                                    <br><small class="text-muted">{{ $event->childEvents->count() + 1 }} events</small>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-calendar me-1"></i>One-time
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="badge 
                                    @if($event->status == 'active') bg-success
                                    @elseif($event->status == 'postponed') bg-warning text-dark
                                    @elseif($event->status == 'cancelled') bg-danger
                                    @else bg-secondary
                                    @endif">
                                    {{ ucfirst($event->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-users text-primary me-2"></i>
                                    <span class="fw-semibold">{{ $event->joinedUsers->count() }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.events.edit', ['event' => $event->id]) }}" 
                                       class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-btn" 
                                            title="Delete"
                                            data-event-id="{{ $event->id }}"
                                            data-title="{{ $event->title }}"
                                            data-is-recurring="{{ $event->is_recurring ? 'true' : 'false' }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Enhanced Pagination -->
        @if($events->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-3" id="paginationSection">
            <div class="pagination-info">
                <span class="text-muted">
                    Showing {{ $events->firstItem() }}-{{ $events->lastItem() }} of {{ $events->total() }} results
                </span>
            </div>

            <nav aria-label="Events pagination">
                <ul class="pagination pagination-sm mb-0">
                    @if ($events->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link"><i class="fas fa-chevron-left"></i></span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $events->previousPageUrl() }}">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                    @endif

                    @foreach ($events->getUrlRange(1, $events->lastPage()) as $page => $url)
                        @if ($page == $events->currentPage())
                            <li class="page-item active">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach

                    @if ($events->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $events->nextPageUrl() }}">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link"><i class="fas fa-chevron-right"></i></span>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
        @endif

    @else
        <!-- Empty State -->
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-calendar-alt fa-4x text-muted mb-4"></i>
                <h5 class="text-muted mb-3">No Events Found</h5>
                <p class="text-muted mb-4">
                    {{ request()->hasAny(['search', 'status', 'department', 'year_level', 'exclusivity', 'recurrence'])
                       ? 'No events match your search criteria.'
                       : 'Get started by creating your first event!' }}
                </p>
                <div>
                    @if(request()->hasAny(['search', 'status', 'department', 'year_level', 'exclusivity', 'recurrence']))
                        <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-times me-1"></i>Clear Filters
                        </a>
                    @endif
                    <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Create Event
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Hidden form for delete -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('styles')
<style>
/* Custom Styles */
.table th {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
}

.btn-group .btn {
    padding: 0.25rem 0.5rem;
}

.badge {
    padding: 0.35em 0.65em;
    font-weight: 500;
}

.pagination .page-link {
    border-radius: 0.25rem;
    margin: 0 2px;
}

.pagination .page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.object-fit-cover {
    object-fit: cover;
}

@media print {
    .no-print {
        display: none !important;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Live Search Functionality
    const searchInput = document.getElementById('liveSearchInput');
    const searchSpinner = document.getElementById('searchSpinner');
    const clearSearchBtn = document.getElementById('clearSearchBtn');
    const eventsTableBody = document.getElementById('eventsTableBody');
    const searchResultsInfo = document.getElementById('searchResultsInfo');
    const searchResultsText = document.getElementById('searchResultsText');
    let searchTimeout;

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            
            // Show/hide clear button
            clearSearchBtn.style.display = searchTerm ? 'block' : 'none';
            
            // Show spinner
            searchSpinner.style.display = 'block';
            
            // Clear previous timeout
            clearTimeout(searchTimeout);
            
            // Debounce search
            searchTimeout = setTimeout(() => {
                performSearch(searchTerm);
                searchSpinner.style.display = 'none';
            }, 300);
        });

        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            clearSearchBtn.style.display = 'none';
            performSearch('');
        });
    }

    function performSearch(searchTerm) {
        const rows = eventsTableBody.querySelectorAll('.event-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const searchableContent = row.dataset.searchable || '';
            if (searchableContent.includes(searchTerm)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Update search results info
        if (searchTerm) {
            searchResultsInfo.style.display = 'block';
            searchResultsText.textContent = `Found ${visibleCount} event(s) matching "${searchTerm}"`;
        } else {
            searchResultsInfo.style.display = 'none';
        }
    }

    // Clear Search Results
    const clearSearchResults = document.getElementById('clearSearchResults');
    if (clearSearchResults) {
        clearSearchResults.addEventListener('click', function() {
            searchInput.value = '';
            clearSearchBtn.style.display = 'none';
            performSearch('');
        });
    }

    // Reset Filters
    const resetFiltersBtn = document.getElementById('resetFiltersBtn');
    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener('click', function() {
            window.location.href = '{{ route("admin.events.index") }}';
        });
    }

    // Delete Event
    const deleteBtns = document.querySelectorAll('.delete-btn');
    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const eventId = this.dataset.eventId;
            const title = this.dataset.title;
            const isRecurring = this.dataset.isRecurring === 'true';

            Swal.fire({
                title: 'Delete Event?',
                html: `Are you sure you want to delete <strong>${title}</strong>?` + 
                      (isRecurring ? '<br><small class="text-danger">This will delete all recurring instances.</small>' : ''),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('deleteForm');
                    form.action = `/admin/events/${eventId}`;
                    
                    if (isRecurring) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'delete_series';
                        input.value = '1';
                        form.appendChild(input);
                    }
                    
                    form.submit();
                }
            });
        });
    });
});

// Success Message
@if(session('success'))
Swal.fire({
    icon: 'success',
    title: 'Success!',
    text: '{{ session('success') }}',
    timer: 3000,
    showConfirmButton: false
});
@endif

// Error Message
@if(session('error'))
Swal.fire({
    icon: 'error',
    title: 'Error!',
    text: '{{ session('error') }}',
    confirmButtonColor: '#d33'
});
@endif
</script>
@endpush

@endsection