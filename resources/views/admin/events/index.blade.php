@extends('admin.layouts.app')
@section('title', 'Events Management')
@section('page-title', 'Events Management')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 fw-bold mb-1"><i class="fas fa-calendar-alt text-primary me-2"></i>Events Management</h2>
            <p class="text-muted mb-0">{{ $events->total() }} total events</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.events.archive') }}" class="btn btn-secondary"><i class="fas fa-archive me-2"></i>Archive</a>
            <a href="{{ route('admin.events.print', request()->query()) }}" target="_blank" class="btn btn-info text-white"><i class="fas fa-print me-2"></i>Print</a>
            <a href="{{ route('admin.events.create') }}" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Event</a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" class="row g-3" id="filtersForm">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="search" id="searchInput" value="{{ request('search') }}" placeholder="Search events...">
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="status" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        @foreach(['active', 'postponed', 'cancelled'] as $status)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="department" onchange="this.form.submit()">
                        <option value="">All Departments</option>
                        @foreach(['BSIT', 'BSBA', 'BSED', 'BEED', 'BSHM'] as $dept)
                        <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="year_level" onchange="this.form.submit()">
                        <option value="">All Years</option>
                        @foreach(range(1, 4) as $year)
                        <option value="{{ $year }}" {{ request('year_level') == $year ? 'selected' : '' }}>{{ $year }}{{ ['st','nd','rd','th'][$year-1] ?? 'th' }} Year</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="exclusivity" onchange="this.form.submit()">
                        <option value="">All Access</option>
                        <option value="open" {{ request('exclusivity') == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="exclusive" {{ request('exclusivity') == 'exclusive' ? 'selected' : '' }}>Exclusive</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-outline-secondary w-100" onclick="window.location='{{ route('admin.events.index') }}'"><i class="fas fa-redo"></i></button>
                </div>
            </form>
        </div>
    </div>

    @if($events->count())
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-3">#</th>
                        <th>Image</th>
                        <th>Event Details</th>
                        <th>Date & Time</th>
                        <th>Access</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Participants</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($events as $event)
                    <tr>
                        <td class="ps-3">
                            <span class="text-muted fw-medium">#{{ $event->id }}</span>
                            @if($event->isRecurring())<br><small class="badge bg-info mt-1">Series</small>@endif
                        </td>
                        <td>
                            <div style="width:60px;height:60px;overflow:hidden;border-radius:8px;">
                                @if($event->image)
                                <img src="{{ $event->image }}" alt="{{ $event->title }}" class="w-100 h-100 object-fit-cover" onerror="this.outerHTML='<div class=\'d-flex align-items-center justify-content-center bg-light w-100 h-100\'><i class=\'fas fa-image text-muted\'></i></div>'">
                                @else
                                <div class="d-flex align-items-center justify-content-center bg-light w-100 h-100"><i class="fas fa-image text-muted"></i></div>
                                @endif
                            </div>
                        </td>
                        <td style="max-width:300px;">
                            <h6 class="mb-1 fw-semibold">{{ Str::limit($event->title, 40) }}</h6>
                            <p class="text-muted mb-1 small">{{ Str::limit($event->description, 60) }}</p>
                            @if($event->isRecurring())<small class="text-info"><i class="fas fa-redo me-1"></i>{{ $event->recurrence_display }}</small>@endif
                        </td>
                        <td>
                            <div class="fw-medium">{{ $event->date->format('M d, Y') }}</div>
                            @if($event->start_time)
                            <small class="text-muted">{{ $event->start_time->format('h:i A') }}@if($event->end_time) - {{ $event->end_time->format('h:i A') }}@endif</small>
                            @endif
                        </td>
                        <td>
                            @if($event->is_exclusive)
                            <span class="badge bg-warning text-dark mb-1" title="{{ $event->department_display }}"><i class="fas fa-lock me-1"></i>Exclusive</span>
                            <br><small class="text-muted">{{ Str::limit($event->department_display, 20) }}</small>
                            @else
                            <span class="badge bg-success"><i class="fas fa-globe me-1"></i>Open</span>
                            @endif
                        </td>
                        <td>
                            @if($event->is_recurring)
                            <span class="badge bg-info mb-1"><i class="fas fa-redo me-1"></i>Recurring</span>
                            <br><small class="text-muted">{{ $event->childEvents->count() + 1 }} events</small>
                            @else
                            <span class="badge bg-secondary"><i class="fas fa-calendar me-1"></i>One-time</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $event->status == 'active' ? 'success' : ($event->status == 'postponed' ? 'warning text-dark' : 'danger') }}">
                                {{ ucfirst($event->status) }}
                            </span>
                            @if(in_array($event->status, ['cancelled', 'postponed']) && $event->hasCancellationDocument())
                            <br><small class="text-info mt-1"><i class="{{ $event->cancellation_document_icon }} me-1"></i>Doc attached</small>
                            @endif
                        </td>
                        <td><div class="d-flex align-items-center"><i class="fas fa-users text-primary me-2"></i><span class="fw-semibold">{{ $event->joinedUsers->count() }}</span></div></td>
                        <td class="text-center">
                            <div class="btn-group">
                                @if(in_array($event->status, ['cancelled', 'postponed']) && $event->hasCancellationDocument())
                                <button type="button" 
                                        class="btn btn-sm btn-outline-info view-doc-btn" 
                                        title="View Document" 
                                        data-url="{{ $event->cancellation_document_url }}" 
                                        data-name="{{ $event->cancellation_document_name }}" 
                                        data-ext="{{ $event->cancellation_document_extension }}">
                                    <i class="fas fa-file-alt"></i>
                                </button>
                                @endif
                                <a href="{{ route('admin.events.show', $event->id) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.events.edit', $event->id) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger delete-btn" title="Delete" data-id="{{ $event->id }}" data-title="{{ $event->title }}" data-recurring="{{ $event->is_recurring ? 'true' : 'false' }}">
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

    @if($events->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-4">
        <span class="text-muted">Showing {{ $events->firstItem() }}-{{ $events->lastItem() }} of {{ $events->total() }}</span>
        <nav>
            <ul class="pagination pagination-sm mb-0">
                @if($events->onFirstPage())
                <li class="page-item disabled"><span class="page-link"><i class="fas fa-chevron-left"></i></span></li>
                @else
                <li class="page-item"><a class="page-link" href="{{ $events->previousPageUrl() }}"><i class="fas fa-chevron-left"></i></a></li>
                @endif
                
                @foreach($events->getUrlRange(1, $events->lastPage()) as $page => $url)
                <li class="page-item {{ $page == $events->currentPage() ? 'active' : '' }}">
                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                </li>
                @endforeach
                
                @if($events->hasMorePages())
                <li class="page-item"><a class="page-link" href="{{ $events->nextPageUrl() }}"><i class="fas fa-chevron-right"></i></a></li>
                @else
                <li class="page-item disabled"><span class="page-link"><i class="fas fa-chevron-right"></i></span></li>
                @endif
            </ul>
        </nav>
    </div>
    @endif

    @else
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="fas fa-calendar-alt fa-4x text-muted mb-4"></i>
            <h5 class="text-muted mb-3">No Events Found</h5>
            <p class="text-muted mb-4">{{ request()->hasAny(['search', 'status', 'department', 'year_level', 'exclusivity']) ? 'No events match your criteria.' : 'Get started by creating your first event!' }}</p>
            <div>
                @if(request()->hasAny(['search', 'status', 'department', 'year_level', 'exclusivity']))
                <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary me-2"><i class="fas fa-times me-1"></i>Clear Filters</a>
                @endif
                <a href="{{ route('admin.events.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Create Event</a>
            </div>
        </div>
    </div>
    @endif
</div>

{{-- Document Viewer Modal --}}
<div class="modal fade" id="docViewerModal" tabindex="-1" aria-labelledby="docViewerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="docViewerModalLabel">
                    <i class="fas fa-file-alt me-2"></i><span id="modalDocTitle"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="min-height: 500px;">
                <div id="documentViewerContent" class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Loading document...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="modalDownloadBtn">
                    <i class="fas fa-download me-2"></i>Download
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" style="display:none;">@csrf @method('DELETE')</form>

@push('styles')
<style>
.table th{font-weight:600;text-transform:uppercase;font-size:0.85rem;}
.btn-group .btn{padding:0.25rem 0.5rem;}
.badge{padding:0.35em 0.65em;font-weight:500;}
.object-fit-cover{object-fit:cover;}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Document viewer function
document.querySelectorAll('.view-doc-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const url = this.dataset.url;
        const name = this.dataset.name;
        const ext = this.dataset.ext || 'pdf';
        
        const modal = new bootstrap.Modal(document.getElementById('docViewerModal'));
        document.getElementById('modalDocTitle').textContent = name;
        
        const viewer = document.getElementById('documentViewerContent');
        viewer.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-3 text-muted">Loading document...</p></div>';
        
        // Setup download button
        document.getElementById('modalDownloadBtn').onclick = function() {
            if (url.startsWith('data:')) {
                // Handle base64 download
                const link = document.createElement('a');
                link.href = url;
                link.download = name;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } else {
                // Handle regular URL download
                window.open(url, '_blank');
            }
        };
        
        modal.show();
        
        setTimeout(() => {
            if (ext === 'pdf') {
                viewer.innerHTML = `
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        PDF Document Preview - Use download button to save or open in new tab for full features
                    </div>
                    <iframe src="${url}#toolbar=1&navpanes=0&scrollbar=1" 
                            style="width:100%;height:600px;border:1px solid #ddd;border-radius:8px;" 
                            frameborder="0"></iframe>`;
            } else if (['doc', 'docx'].includes(ext)) {
                viewer.innerHTML = `
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        Word Document - Click download button to save and view with full features
                    </div>
                    <div class="text-center py-5">
                        <i class="fas fa-file-word fa-5x text-primary mb-4"></i>
                        <h5 class="mb-3">${name}</h5>
                        <p class="text-muted mb-4">Word documents must be downloaded to view properly</p>
                        <button class="btn btn-primary btn-lg" onclick="document.getElementById('modalDownloadBtn').click()">
                            <i class="fas fa-download me-2"></i>Download Document
                        </button>
                    </div>`;
            } else {
                viewer.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Preview not available for this file type. Please download to view.
                    </div>
                    <div class="text-center mt-4">
                        <i class="fas fa-file fa-5x text-muted mb-3"></i>
                        <p class="text-muted">${name}</p>
                    </div>`;
            }
        }, 300);
    });
});

// Delete event
document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        const title = this.dataset.title;
        const recurring = this.dataset.recurring === 'true';
        
        Swal.fire({
            title: 'Delete Event?',
            html: `Delete <strong>${title}</strong>?<br><small class="text-muted">Can restore from Archive.</small>` + (recurring ? '<br><small class="text-warning">This will delete all instances in the series.</small>' : ''),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Yes, delete it!'
        }).then(result => {
            if(result.isConfirmed) {
                const form = document.getElementById('deleteForm');
                form.action = `/admin/events/${id}`;
                if(recurring) {
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

// Live search
let searchTimeout;
document.getElementById('searchInput')?.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => this.form.submit(), 500);
});

@if(session('success'))
Swal.fire({
    icon: 'success',
    title: 'Success!',
    text: '{{ session('success') }}',
    timer: 3000,
    showConfirmButton: false
});
@endif

@if(session('error'))
Swal.fire({
    icon: 'error',
    title: 'Error!',
    text: '{{ session('error') }}'
});
@endif
</script>
@endpush
@endsection