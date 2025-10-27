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
                        <div class="live-search-spinner" id="searchSpinner" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                        <button type="button" class="clear-search-btn" id="clearSearchBtn" style="display: none;">
                            <i class="fas fa-times"></i>
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
                    <button type="button" class="btn btn-outline-primary w-100" id="resetFiltersBtn" title="Reset All Filters">
                        <i class="fas fa-refresh"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($events->count())
        <!-- Search Results Counter -->
        <div class="search-results-info mb-3" id="searchResultsInfo" style="display: none;">
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle me-2"></i>
                <span id="searchResultsText"></span>
                <button type="button" class="btn btn-sm btn-outline-info ms-2" id="clearSearchResults">
                    Clear Search
                </button>
            </div>
        </div>

        <!-- Events Table -->
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0 table-compact">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3 py-2">#</th>
                            <th class="py-2">Image</th>
                            <th class="py-2">Event</th>
                            <th class="py-2">Date & Time</th>
                            <th class="py-2">Access</th>
                            <th class="py-2">Type</th>
                            <th class="py-2">Status</th>
                            <th class="py-2">Location</th>
                            <th class="py-2 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="eventsTableBody">
                        @foreach($events as $event)
                        <tr class="event-row compact-row" data-searchable="{{ strtolower($event->title . ' ' . $event->description . ' ' . $event->location . ' #' . $event->id) }}">
                            <td class="ps-3 py-2 align-middle">
                                <span class="text-muted fw-medium small">#{{ $event->id }}</span>
                                @if($event->isRecurring())
                                    <div><small class="badge bg-info badge-xs">Series</small></div>
                                @endif
                            </td>
                            <td class="py-2 align-middle">
                                <div class="event-image-compact">
                                    @if($event->image)
                                        <img src="{{$event->image}}"
                                             alt="{{ $event->title }}"
                                             class="event-img-compact"
                                             onerror="this.parentElement.innerHTML='<div class=\'no-image-compact\'><i class=\'fas fa-image\'></i></div>'">
                                    @else
                                        <div class="no-image-compact">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="py-2 align-middle">
                                <div>
                                    <h6 class="mb-1 fw-semibold event-title-compact">{{ Str::limit($event->title, 35) }}</h6>
                                    <small class="text-muted event-desc-compact">{{ Str::limit($event->description, 50) }}</small>
                                    @if($event->isRecurring())
                                        <div><small class="text-info recurrence-compact"><i class="fas fa-redo me-1"></i>{{ $event->recurrence_display }}</small></div>
                                    @endif
                                </div>
                            </td>
                            <td class="py-2 align-middle">
                                <div class="fw-medium date-compact">{{ $event->date->format('M d, Y') }}</div>
                                @if($event->start_time)
                                    <small class="text-muted time-compact">{{ $event->start_time->format('h:i A') }}</small>
                                    @if($event->end_time)
                                        <small class="text-muted time-compact"> - {{ $event->end_time->format('h:i A') }}</small>
                                    @endif
                                @endif
                            </td>
                            <td class="py-2 align-middle">
                                @if($event->is_exclusive)
                                    <span class="badge bg-warning text-dark badge-compact" title="{{ $event->department_display }}">
                                        <i class="fas fa-lock me-1"></i>Exclusive
                                    </span>
                                    <div><small class="text-muted dept-compact">{{ Str::limit($event->department_display, 20) }}</small></div>
                                @else
                                    <span class="badge bg-success badge-compact">
                                        <i class="fas fa-globe me-1"></i>Open to All
                                    </span>
                                @endif
                            </td>
                            <td class="py-2 align-middle">
                                @if($event->is_recurring)
                                    <span class="badge bg-info badge-compact">
                                        <i class="fas fa-redo me-1"></i>Recurring
                                    </span>
                                    <div><small class="text-muted type-count-compact">{{ $event->childEvents->count() + 1 }} events</small></div>
                                @else
                                    <span class="badge bg-secondary badge-compact">
                                        <i class="fas fa-calendar me-1"></i>One-time
                                    </span>
                                @endif
                            </td>
                            <td class="py-2 align-middle">
                                <span class="badge status-{{ $event->status }} badge-compact">
                                    {{ ucfirst($event->status) }}
                                </span>
                            </td>
                            <td class="py-2 align-middle">
                                <span class="text-muted location-compact" title="{{ $event->location }}">{{ Str::limit($event->location, 20) }}</span>
                            </td>
                            <td class="py-2 align-middle text-center">
                                <div class="action-buttons-compact d-flex justify-content-center gap-1">
                                    <button class="btn btn-clean-compact btn-view" title="View" onclick="viewEvent({{ $event->id }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-clean-compact btn-edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-clean-compact btn-delete delete-btn" title="Delete"
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
        <div class="d-flex justify-content-between align-items-center mt-4" id="paginationSection">
            <div class="pagination-info">
                <span class="text-muted">
                    Showing {{ $events->firstItem() }}-{{ $events->lastItem() }} of {{ $events->total() }} results
                </span>
            </div>

            <nav aria-label="Events pagination">
                <ul class="pagination pagination-sm mb-0">
                    @if ($events->onFirstPage())
                        <li class="page-item disabled"><span class="page-link"><i class="fas fa-chevron-left"></i></span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $events->previousPageUrl() }}"><i class="fas fa-chevron-left"></i></a></li>
                    @endif

                    @foreach ($events->getUrlRange(1, $events->lastPage()) as $page => $url)
                        @if ($page == $events->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach

                    @if ($events->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $events->nextPageUrl() }}"><i class="fas fa-chevron-right"></i></a></li>
                    @else
                        <li class="page-item disabled"><span class="page-link"><i class="fas fa-chevron-right"></i></span></li>
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
                            Clear Filters
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
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="viewEventModalLabel">
                    <i class="fas fa-eye me-2"></i>Event Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="viewEventContent">
                    <!-- Content will be dynamically loaded -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Print Settings Modal -->
<div class="modal fade" id="printSettingsModal" tabindex="-1" aria-labelledby="printSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
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
                            <div class="text-center mb-3">
                                <img id="eventsLeftLogoPreview" 
                                     src="{{ $printSettings?->events_left_logo_url ?? asset('images/default-left-logo.png') }}" 
                                     alt="Left Logo" 
                                     class="img-thumbnail mb-2"
                                     style="max-height: 150px; object-fit: contain;">
                            </div>
                            <input type="file" name="events_left_logo" class="form-control" accept="image/*" id="eventsLeftLogoInput">
                            <small class="text-muted">Recommended: 200x200px, PNG or JPG</small>
                        </div>

                        <!-- Right Logo -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Right Logo</label>
                            <div class="text-center mb-3">
                                <img id="eventsRightLogoPreview" 
                                     src="{{ $printSettings?->events_right_logo_url ?? asset('images/default-right-logo.png') }}" 
                                     alt="Right Logo" 
                                     class="img-thumbnail mb-2"
                                     style="max-height: 150px; object-fit: contain;">
                            </div>
                            <input type="file" name="events_right_logo" class="form-control" accept="image/*" id="eventsRightLogoInput">
                            <small class="text-muted">Recommended: 200x200px, PNG or JPG</small>
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <label class="form-label fw-bold">Header Description</label>
                            <textarea name="events_description" class="form-control" rows="3" placeholder="Enter header description for events print summary...">{{ $printSettings?->events_description ?? '' }}</textarea>
                            <small class="text-muted">This will appear in the center of the print header below the title</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
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
    @csrf @method('DELETE')
</form>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/events-index.css') }}">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/admin/events-index.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
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

@if(session('success'))
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session('success') }}',
        timer: 3000,
        showConfirmButton: false
    });
});
@endif
</script>
@endpush

@endsection