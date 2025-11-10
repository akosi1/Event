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
            <!-- Print Settings Button -->
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#printSettingsModal">
                <i class="fas fa-cog me-2"></i>Print Settings
            </button>
            
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
                        @foreach(['BSIT' => 'Bachelor of Science in Information Technology', 'BSBA' => 'Bachelor of Science in Business Administration', 'BSED' => 'Bachelor of Science in Education', 'BEED' => 'Bachelor of Elementary Education', 'BSHM' => 'Bachelor of Science in Hospitality Management'] as $code => $name)
                            <option value="{{ $code }}" {{ request('department') == $code ? 'selected' : '' }}>
                                {{ $code }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="exclusivity" onchange="this.form.submit()">
                        <option value="">All Access Types</option>
                        <option value="open" {{ request('exclusivity') == 'open' ? 'selected' : '' }}>Open to All</option>
                        <option value="exclusive" {{ request('exclusivity') == 'exclusive' ? 'selected' : '' }}>Department Exclusive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="recurrence" onchange="this.form.submit()">
                        <option value="">All Event Types</option>
                        <option value="one_time" {{ request('recurrence') == 'one_time' ? 'selected' : '' }}>One-time</option>
                        <option value="recurring" {{ request('recurrence') == 'recurring' ? 'selected' : '' }}>Recurring</option>
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
                            <th class="py-3">Location</th>
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
                                    <br><small class="text-muted">{{ Str::limit($event->department_display, 25) }}</small>
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
                                <span class="text-muted" title="{{ $event->location }}">
                                    {{ Str::limit($event->location, 25) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            title="View" onclick="viewEvent({{ $event->id }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
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
                    {{ request()->hasAny(['search', 'status', 'department', 'exclusivity', 'recurrence'])
                       ? 'No events match your search criteria.'
                       : 'Get started by creating your first event!' }}
                </p>
                <div>
                    @if(request()->hasAny(['search', 'status', 'department', 'exclusivity', 'recurrence']))
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

<!-- View Event Modal -->
<div class="modal fade" id="viewEventModal" tabindex="-1" aria-labelledby="viewEventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="viewEventModalLabel">
                    <i class="fas fa-eye me-2"></i>Event Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="viewEventContent" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Print Settings Modal -->
<div class="modal fade" id="printSettingsModal" tabindex="-1" aria-labelledby="printSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="printSettingsModalLabel">
                    <i class="fas fa-cog me-2"></i>Events Print Summary Settings
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.events.update-print-settings') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        These settings are specific to the Events Summary report and won't affect other reports.
                    </div>
                    
                    <div class="row g-4">
                        <!-- Left Logo -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Left Logo</label>
                            <div class="text-center mb-3 p-3 bg-light rounded">
                                <img id="eventsLeftLogoPreview" 
                                     src="{{ $printSettings?->events_left_logo_url ?? asset('images/default-left-logo.png') }}" 
                                     alt="Left Logo" 
                                     class="img-thumbnail"
                                     style="max-height: 150px; max-width: 100%; object-fit: contain;">
                            </div>
                            <input type="file" name="events_left_logo" class="form-control" accept="image/*" id="eventsLeftLogoInput">
                            <small class="text-muted d-block mt-1">Recommended: 200x200px, PNG or JPG</small>
                        </div>

                        <!-- Right Logo -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Right Logo</label>
                            <div class="text-center mb-3 p-3 bg-light rounded">
                                <img id="eventsRightLogoPreview" 
                                     src="{{ $printSettings?->events_right_logo_url ?? asset('images/default-right-logo.png') }}" 
                                     alt="Right Logo" 
                                     class="img-thumbnail"
                                     style="max-height: 150px; max-width: 100%; object-fit: contain;">
                            </div>
                            <input type="file" name="events_right_logo" class="form-control" accept="image/*" id="eventsRightLogoInput">
                            <small class="text-muted d-block mt-1">Recommended: 200x200px, PNG or JPG</small>
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <label class="form-label fw-bold">Header Description</label>
                            <textarea name="events_description" class="form-control" rows="3" placeholder="Enter header description for events print summary...">{{ $printSettings?->events_description ?? '' }}</textarea>
                            <small class="text-muted d-block mt-1">This will appear in the center of the print header below the title</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
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

.modal-body img {
    transition: transform 0.2s;
}

.modal-body img:hover {
    transform: scale(1.05);
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
                    form.submit();
                }
            });
        });
    });

    // Image Preview for Events Left Logo
    const eventsLeftLogoInput = document.getElementById('eventsLeftLogoInput');
    if (eventsLeftLogoInput) {
        eventsLeftLogoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('eventsLeftLogoPreview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Image Preview for Events Right Logo
    const eventsRightLogoInput = document.getElementById('eventsRightLogoInput');
    if (eventsRightLogoInput) {
        eventsRightLogoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('eventsRightLogoPreview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }
});

// View Event Function
function viewEvent(eventId) {
    const modal = new bootstrap.Modal(document.getElementById('viewEventModal'));
    const content = document.getElementById('viewEventContent');
    
    // Show loading state
    content.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    
    modal.show();
    
    // Fetch event details
    fetch(`/admin/events/${eventId}`)
        .then(response => response.json())
        .then(data => {
            content.innerHTML = `
                <div class="row">
                    ${data.image ? `
                        <div class="col-12 mb-3">
                            <img src="${data.image}" class="img-fluid rounded" alt="${data.title}">
                        </div>
                    ` : ''}
                    <div class="col-12">
                        <h4 class="mb-3">${data.title}</h4>
                        <p class="text-muted">${data.description}</p>
                        <hr>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong><i class="fas fa-calendar me-2"></i>Date:</strong><br>
                                ${data.date}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong><i class="fas fa-clock me-2"></i>Time:</strong><br>
                                ${data.time}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong><i class="fas fa-map-marker-alt me-2"></i>Location:</strong><br>
                                ${data.location}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong><i class="fas fa-info-circle me-2"></i>Status:</strong><br>
                                <span class="badge bg-${data.status === 'active' ? 'success' : data.status === 'postponed' ? 'warning' : 'danger'}">${data.status}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            content.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Failed to load event details. Please try again.
                </div>
            `;
        });
}

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
</script>
@endpush

@endsection