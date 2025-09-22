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
            <a href="{{ route('admin.events.print-summary', request()->query()) }}" 
               target="_blank" 
               class="btn btn-outline-secondary"
               title="Print Events Summary">
                <i class="fas fa-print me-2"></i>Print
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
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="search" 
                           value="{{ request('search') }}" placeholder="Search events...">
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
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($events->count())
        <!-- Events Table -->
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">#</th>
                            <th class="py-3">Image</th>
                            <th class="py-3">Event</th>
                            <th class="py-3">Date & Time</th>
                            <th class="py-3">Access</th>
                            <th class="py-3">Type</th>
                            <th class="py-3">Status</th>
                            <th class="py-3">Location</th>
                            <th class="py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($events as $event)
                        <tr>
                            <td class="ps-4 py-3 align-middle">
                                <span class="text-muted fw-medium">#{{ $event->id }}</span>
                                @if($event->isRecurring())
                                    <div><small class="badge bg-info">Series</small></div>
                                @endif
                            </td>
                            <td class="py-3 align-middle">
                                <div class="event-image">
                                    @if($event->image && file_exists(public_path('storage/' . $event->image)))
                                        <img src="{{ asset('storage/' . $event->image) }}" 
                                             alt="{{ $event->title }}" 
                                             class="event-img"
                                             onerror="this.parentElement.innerHTML='<div class=\'no-image\'><i class=\'fas fa-image\'></i></div>'">
                                    @elseif($event->image)
                                        <img src="{{ $event->image }}" 
                                             alt="{{ $event->title }}" 
                                             class="event-img"
                                             onerror="this.parentElement.innerHTML='<div class=\'no-image\'><i class=\'fas fa-image\'></i></div>'">
                                    @else
                                        <div class="no-image">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 align-middle">
                                <div>
                                    <h6 class="mb-1 fw-semibold">{{ Str::limit($event->title, 35) }}</h6>
                                    <small class="text-muted">{{ Str::limit($event->description, 50) }}</small>
                                    @if($event->isRecurring())
                                        <div><small class="text-info"><i class="fas fa-redo me-1"></i>{{ $event->recurrence_display }}</small></div>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 align-middle">
                                <div class="fw-medium">{{ $event->date->format('M d, Y') }}</div>
                                @if($event->start_time)
                                    <small class="text-muted">{{ $event->start_time->format('h:i A') }}</small>
                                    @if($event->end_time)
                                        <small class="text-muted"> - {{ $event->end_time->format('h:i A') }}</small>
                                    @endif
                                @endif
                            </td>
                            <td class="py-3 align-middle">
                                @if($event->is_exclusive)
                                    <span class="badge bg-warning text-dark" title="{{ $event->department_display }}">
                                        <i class="fas fa-lock me-1"></i>Exclusive
                                    </span>
                                    <div><small class="text-muted">{{ Str::limit($event->department_display, 20) }}</small></div>
                                @else
                                    <span class="badge bg-success">
                                        <i class="fas fa-globe me-1"></i>Open to All
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 align-middle">
                                @if($event->is_recurring)
                                    <span class="badge bg-info">
                                        <i class="fas fa-redo me-1"></i>Recurring
                                    </span>
                                    <div><small class="text-muted">{{ $event->childEvents->count() + 1 }} events</small></div>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-calendar me-1"></i>One-time
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 align-middle">
                                <span class="badge status-{{ $event->status }}">
                                    {{ ucfirst($event->status) }}
                                </span>
                            </td>
                            <td class="py-3 align-middle">
                                <span class="text-muted" title="{{ $event->location }}">{{ Str::limit($event->location, 20) }}</span>
                            </td>
                            <td class="py-3 align-middle text-center">
                                <div class="action-buttons d-flex justify-content-center gap-1">
                                    <button class="btn btn-clean btn-view" title="View" onclick="viewEvent({{ $event->id }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-clean btn-edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-clean btn-delete delete-btn" title="Delete" 
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
        <div class="d-flex justify-content-between align-items-center mt-4">
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

<!-- Hidden form for delete -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf @method('DELETE')
</form>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/events-index.css') }}">
<style>
    .event-image {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
    }
    .event-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .no-image {
        color: #6c757d;
        font-size: 1.2rem;
    }
    .status-active { background-color: #28a745; }
    .status-postponed { background-color: #ffc107; color: #212529; }
    .status-cancelled { background-color: #dc3545; }
    .btn-clean {
        border: none;
        background: none;
        padding: 6px 8px;
        border-radius: 4px;
        transition: background-color 0.2s;
    }
    .btn-view:hover { background-color: #e3f2fd; color: #1976d2; }
    .btn-edit:hover { background-color: #fff3e0; color: #f57c00; }
    .btn-delete:hover { background-color: #ffebee; color: #d32f2f; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// View event function
function viewEvent(eventId) {
    fetch(`/admin/events/${eventId}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('viewEventContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('viewEventModal')).show();
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error!', 'Failed to load event details.', 'error');
        });
}

// Enhanced delete function for recurring events
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-btn');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const eventId = this.dataset.eventId;
            const title = this.dataset.title;
            const isRecurring = this.dataset.isRecurring === 'true';
            
            let confirmText = `Are you sure you want to delete "${title}"?`;
            let confirmButtonText = 'Yes, delete it!';
            
            if (isRecurring) {
                confirmText = `This is a recurring event. How would you like to proceed?`;
            }
            
            if (isRecurring) {
                // Special handling for recurring events
                Swal.fire({
                    title: 'Delete Recurring Event',
                    text: confirmText,
                    icon: 'warning',
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'Delete entire series',
                    denyButtonText: 'Delete only this event',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#d33',
                    denyButtonColor: '#3085d6'
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteEvent(eventId, true); // Delete series
                    } else if (result.isDenied) {
                        deleteEvent(eventId, false); // Delete single
                    }
                });
            } else {
                // Regular delete confirmation
                Swal.fire({
                    title: 'Are you sure?',
                    text: confirmText,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: confirmButtonText
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteEvent(eventId, false);
                    }
                });
            }
        });
    });
});

function deleteEvent(eventId, deleteSeries = false) {
    const form = document.getElementById('deleteForm');
    form.action = `/admin/events/${eventId}`;
    
    // Add delete_series parameter if needed
    if (deleteSeries) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'delete_series';
        input.value = '1';
        form.appendChild(input);
    }
    
    form.submit();
}

function showSuccessMessage(message) {
    Swal.fire({
        title: 'Success!',
        text: message,
        icon: 'success',
        timer: 3000,
        showConfirmButton: false
    });
}

// Pass success message to JavaScript
@if(session('success'))
    document.addEventListener('DOMContentLoaded', function() {
        showSuccessMessage('{{ session('success') }}');
    });
@endif
</script>
@endpush

@endsection