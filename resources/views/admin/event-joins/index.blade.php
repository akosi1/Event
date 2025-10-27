@extends('admin.layouts.app')
@section('title', 'Event Joins')
@section('page-title', 'Event Join Requests')

@section('content')
<div class="container-fluid px-4">
    <!-- Header with Print Settings Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 fw-bold mb-1">
                <i class="fas fa-users text-primary me-2"></i>Event Join Requests
            </h2>
            <p class="text-muted mb-0">{{ $eventJoins->total() }} total requests</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#printSettingsModal">
                <i class="fas fa-cog me-2"></i>Print Settings
            </button>
            <a href="{{ route('admin.event-joins.print', request()->query()) }}" 
               target="_blank" 
               class="btn btn-primary">
                <i class="fas fa-print me-2"></i>Print Summary
            </a>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="search" name="search" id="liveSearchInput" class="form-control"
                        placeholder="Search users..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <select name="event_id" class="form-select" onchange="this.form.submit()">
                        <option value="">All Events</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                {{ $event->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.event-joins.index') }}" class="btn btn-outline-secondary w-100">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    @if($eventJoins->count())
        <!-- Event Joins Table -->
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0 table-compact">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3 py-2">#</th>
                            <th class="py-2">User</th>
                            <th class="py-2">Event</th>
                            <th class="py-2">Joined At</th>
                            <th class="py-2">Approved</th>
                            <th class="py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="eventJoinsTableBody">
                        @foreach($eventJoins as $join)
                        <tr class="join-row compact-row"
                            data-searchable="{{ strtolower($join->user->first_name . ' ' . $join->user->last_name . ' ' . $join->user->email . ' ' . $join->event->title) }}"
                            data-event-id="{{ $join->event_id }}">
                            <td class="ps-3 py-2 align-middle">
                                <span class="text-muted fw-medium small">#{{ $join->id }}</span>
                            </td>
                            <td class="py-2 align-middle">
                                <div>
                                    <strong>{{ $join->user->first_name }} {{ $join->user->last_name }}</strong>
                                    <small class="text-muted d-block">{{ $join->user->email ?? '' }}</small>
                                </div>
                            </td>
                            <td class="py-2 align-middle">
                                <div>
                                    <strong>{{ $join->event->title ?? 'N/A' }}</strong>
                                    <small class="text-muted d-block">{{ $join->event->date?->format('M d, Y') }}</small>
                                </div>
                            </td>
                            <td class="py-2 align-middle">
                                {{ $join->joined_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="py-2 align-middle">
                                @if($join->approved)
                                    <span class="badge bg-success badge-compact">
                                        <i class="fas fa-check me-1"></i>Approved
                                    </span>
                                @else
                                    <span class="badge bg-warning text-dark badge-compact">
                                        <i class="fas fa-hourglass-half me-1"></i>Pending
                                    </span>
                                @endif
                            </td>
                            <td class="py-2 align-middle text-center">
                                <div class="action-buttons-compact d-flex justify-content-center gap-1">
                                    @if(!$join->approved)
                                        <form action="{{ route('admin.event-joins.approve', $join) }}" method="POST">
                                            @csrf
                                            <button class="btn btn-success btn-sm" title="Approve">
                                                Approve
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted small">No Actions</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($eventJoins->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="pagination-info">
                <span class="text-muted">
                    Showing {{ $eventJoins->firstItem() }}-{{ $eventJoins->lastItem() }} of {{ $eventJoins->total() }} results
                </span>
            </div>
            <nav aria-label="Pagination">
                {{ $eventJoins->links() }}
            </nav>
        </div>
        @endif
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-users fa-4x text-muted mb-4"></i>
                <h5 class="text-muted mb-3">No Join Requests Found</h5>
                <p class="text-muted mb-4">
                    There are no users who have joined events yet.
                </p>
            </div>
        </div>
    @endif
</div>

<!-- Print Settings Modal -->
<div class="modal fade" id="printSettingsModal" tabindex="-1" aria-labelledby="printSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="printSettingsModalLabel">
                    <i class="fas fa-cog me-2"></i>Print Summary Settings
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.event-joins.update-print-settings') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-4">
                        <!-- Left Logo -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Left Logo</label>
                            <div class="text-center mb-3">
                                <img id="leftLogoPreview" 
                                     src="{{ $printSettings?->left_logo_url ?? asset('images/default-left-logo.png') }}" 
                                     alt="Left Logo" 
                                     class="img-thumbnail mb-2"
                                     style="max-height: 150px; object-fit: contain;">
                            </div>
                            <input type="file" name="left_logo" class="form-control" accept="image/*" id="leftLogoInput">
                            <small class="text-muted">Recommended: 200x200px, PNG or JPG</small>
                        </div>

                        <!-- Right Logo -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Right Logo (SAIL)</label>
                            <div class="text-center mb-3">
                                <img id="rightLogoPreview" 
                                     src="{{ $printSettings?->right_logo_url ?? asset('images/default-right-logo.png') }}" 
                                     alt="Right Logo" 
                                     class="img-thumbnail mb-2"
                                     style="max-height: 150px; object-fit: contain;">
                            </div>
                            <input type="file" name="right_logo" class="form-control" accept="image/*" id="rightLogoInput">
                            <small class="text-muted">Recommended: 200x200px, PNG or JPG</small>
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <label class="form-label fw-bold">Header Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Enter header description for print summary...">{{ $printSettings?->description ?? '' }}</textarea>
                            <small class="text-muted">This will appear in the center of the print header</small>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Live Search
        const searchInput = document.getElementById('liveSearchInput');
        const tableRows = document.querySelectorAll('#eventJoinsTableBody .join-row');

        searchInput.addEventListener('input', function() {
            const query = this.value.trim().toLowerCase();

            tableRows.forEach(row => {
                const searchable = row.dataset.searchable || '';
                if(query === '' || searchable.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Image Preview for Left Logo
        document.getElementById('leftLogoInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('leftLogoPreview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Image Preview for Right Logo
        document.getElementById('rightLogoInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('rightLogoPreview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    });
</script>

@push('styles')
<link rel="stylesheet" href="{{ asset('public/css/admin/events-index.css') }}">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session('success') }}',
        timer: 3000,
        showConfirmButton: false
    });
</script>
@endif
@endpush
@endsection