@extends('admin.layouts.app')
@section('title', 'Archived Events')
@section('page-title', 'Archived Events')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 fw-bold mb-1">
                <i class="fas fa-archive text-secondary me-2"></i>Archived Events
            </h2>
            <p class="text-muted mb-0">{{ $archivedEvents->total() }} archived events</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.events.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Back to Events
            </a>
        </div>
    </div>

    <!-- Search Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" class="row g-3" id="searchForm">
                <div class="col-md-10">
                    <div class="position-relative">
                        <input type="text" class="form-control" name="search"
                               value="{{ request('search') }}" placeholder="Search archived events..." autocomplete="off">
                        <button type="button" class="btn btn-sm position-absolute top-50 end-0 translate-middle-y me-2" 
                                id="clearSearchBtn" style="display: {{ request('search') ? 'block' : 'none' }}; border: none; background: none;">
                            <i class="fas fa-times text-muted"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i>Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($archivedEvents->count())
        <!-- Archived Events Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-trash-restore me-2"></i>Archived Events - Can be Restored</h6>
                    <small class="text-muted">Archived on date shown</small>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3 py-3">#</th>
                            <th class="py-3">Image</th>
                            <th class="py-3">Event Details</th>
                            <th class="py-3">Date & Time</th>
                            <th class="py-3">Type</th>
                            <th class="py-3">Status</th>
                            <th class="py-3">Archived On</th>
                            <th class="py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($archivedEvents as $event)
                        <tr class="archived-event-row">
                            <td class="ps-3">
                                <div>
                                    <span class="text-muted fw-medium">#{{ $event->id }}</span>
                                    @if($event->isRecurring())
                                        <br><small class="badge bg-info mt-1">Series</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div style="width: 60px; height: 60px; overflow: hidden; border-radius: 8px; opacity: 0.7;">
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
                                <small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>{{ Str::limit($event->location, 30) }}</small>
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
                                @if($event->is_recurring)
                                    <span class="badge bg-info mb-1">
                                        <i class="fas fa-redo me-1"></i>Recurring
                                    </span>
                                    <br><small class="text-muted">{{ $event->childEvents()->withTrashed()->count() + 1 }} events</small>
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
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>{{ $event->deleted_at->format('M d, Y') }}
                                    <br>{{ $event->deleted_at->format('h:i A') }}
                                </small>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-success restore-btn" 
                                            title="Restore Event"
                                            data-event-id="{{ $event->id }}"
                                            data-title="{{ $event->title }}"
                                            data-is-recurring="{{ $event->is_recurring ? 'true' : 'false' }}">
                                        <i class="fas fa-trash-restore"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger permanent-delete-btn" 
                                            title="Delete Permanently"
                                            data-event-id="{{ $event->id }}"
                                            data-title="{{ $event->title }}"
                                            data-is-recurring="{{ $event->is_recurring ? 'true' : 'false' }}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($archivedEvents->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-3">
            <div class="pagination-info">
                <span class="text-muted">
                    Showing {{ $archivedEvents->firstItem() }}-{{ $archivedEvents->lastItem() }} of {{ $archivedEvents->total() }} results
                </span>
            </div>

            <nav aria-label="Archived events pagination">
                <ul class="pagination pagination-sm mb-0">
                    @if ($archivedEvents->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link"><i class="fas fa-chevron-left"></i></span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $archivedEvents->previousPageUrl() }}">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                    @endif

                    @foreach ($archivedEvents->getUrlRange(1, $archivedEvents->lastPage()) as $page => $url)
                        @if ($page == $archivedEvents->currentPage())
                            <li class="page-item active">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach

                    @if ($archivedEvents->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $archivedEvents->nextPageUrl() }}">
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
                <i class="fas fa-archive fa-4x text-muted mb-4"></i>
                <h5 class="text-muted mb-3">No Archived Events</h5>
                <p class="text-muted mb-4">
                    {{ request('search') 
                       ? 'No archived events match your search criteria.' 
                       : 'All your archived events will appear here.' }}
                </p>
                <div>
                    @if(request('search'))
                        <a href="{{ route('admin.events.archive') }}" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-times me-1"></i>Clear Search
                        </a>
                    @endif
                    <a href="{{ route('admin.events.index') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Events
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Hidden form for restore -->
<form id="restoreForm" method="POST" style="display: none;">
    @csrf
</form>

<!-- Hidden form for permanent delete -->
<form id="permanentDeleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('styles')
<style>
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

.archived-event-row {
    opacity: 0.85;
}

.archived-event-row:hover {
    opacity: 1;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Clear Search Button
    const clearSearchBtn = document.getElementById('clearSearchBtn');
    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', function() {
            window.location.href = '{{ route("admin.events.archive") }}';
        });
    }

    // Restore Event
    const restoreBtns = document.querySelectorAll('.restore-btn');
    restoreBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const eventId = this.dataset.eventId;
            const title = this.dataset.title;
            const isRecurring = this.dataset.isRecurring === 'true';

            Swal.fire({
                title: 'Restore Event?',
                html: `Are you sure you want to restore <strong>${title}</strong>?<br><small class="text-muted">This will move it back to active events.</small>` + 
                      (isRecurring ? '<br><br><div class="form-check"><input class="form-check-input" type="checkbox" id="restoreSeries"><label class="form-check-label" for="restoreSeries">Restore entire series</label></div>' : ''),
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, restore it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('restoreForm');
                    form.action = `/admin/events/${eventId}/restore`;
                    
                    if (isRecurring) {
                        const restoreSeries = document.getElementById('restoreSeries');
                        if (restoreSeries && restoreSeries.checked) {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'restore_series';
                            input.value = '1';
                            form.appendChild(input);
                        }
                    }
                    
                    form.submit();
                }
            });
        });
    });

    // Permanent Delete
    const permanentDeleteBtns = document.querySelectorAll('.permanent-delete-btn');
    permanentDeleteBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const eventId = this.dataset.eventId;
            const title = this.dataset.title;
            const isRecurring = this.dataset.isRecurring === 'true';

            Swal.fire({
                title: 'Permanently Delete?',
                html: `<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i><strong>Warning!</strong> This action cannot be undone!</div>
                       Are you sure you want to permanently delete <strong>${title}</strong>?` + 
                      (isRecurring ? '<br><br><div class="form-check"><input class="form-check-input" type="checkbox" id="deleteSeries"><label class="form-check-label" for="deleteSeries">Delete entire series permanently</label></div>' : ''),
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete permanently!',
                cancelButtonText: 'Cancel',
                input: 'checkbox',
                inputPlaceholder: 'I understand this cannot be undone'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (!result.value) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Confirmation Required',
                            text: 'Please check the confirmation box to proceed.',
                            confirmButtonColor: '#d33'
                        });
                        return;
                    }

                    const form = document.getElementById('permanentDeleteForm');
                    form.action = `/admin/events/${eventId}/force-delete`;
                    
                    if (isRecurring) {
                        const deleteSeries = document.getElementById('deleteSeries');
                        if (deleteSeries && deleteSeries.checked) {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'delete_series';
                            input.value = '1';
                            form.appendChild(input);
                        }
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