@extends('admin.layouts.app')
@section('title', 'Certificate Management')
@section('page-title', 'Certificate Management')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 fw-bold mb-1">
                <i class="fas fa-award text-success me-2"></i>Certificate Management
            </h2>
            <p class="text-muted mb-0">{{ $certificates->total() }} total certificates</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" class="row g-3" id="filtersForm">
                <div class="col-md-4">
                    <div class="position-relative">
                        <input type="search" class="form-control" name="search" id="liveSearchInput"
                            value="{{ request('search') }}" placeholder="Search by User or Event..." autocomplete="off">
                    </div>
                </div>

                {{-- Filter by Event ID --}}
                <div class="col-md-3">
                    <select class="form-select" name="event_id" onchange="this.form.submit()">
                        <option value="">All Events</option>
                        @foreach($events_list as $id => $title)
                            <option value="{{ $id }}" {{ request('event_id') == $id ? 'selected' : '' }}>
                                {{ Str::limit($title, 30) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <a href="{{route('admin.certificates')}}" class="btn btn-outline-secondary me-2">
                        Clear Filters
                    </a>
                </div>
            </form>
        </div>
    </div>

    @if($certificates->count())
        <div class="search-results-info mb-3" id="searchResultsInfo" style="display: none;">
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle me-2"></i>
                <span id="searchResultsText"></span>
                <a href="{{route('admin.certificates')}}" class="btn btn-outline-secondary me-2">
                    Clear Filters
                </a>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0 table-compact">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3 py-2">Cert #</th>
                            <th class="py-2">Recipient (User ID)</th>
                            <th class="py-2">Event (Event ID)</th>
                            <th class="py-2">Generated On</th>
                            <th class="py-2">Certificate</th>
                            <th class="py-2 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="eventsTableBody">
                        @foreach($certificates as $certificate)
                        <tr class="certificate-row compact-row"
                            data-searchable="{{ strtolower(($certificate->user->name ?? '') . ' ' . ($certificate->event->title ?? '') . ' #' . $certificate->id) }}">

                            {{-- Certificate ID --}}
                            <td class="ps-3 py-2 align-middle">
                                <span class="text-muted fw-medium small">#{{ $certificate->id }}</span>
                            </td>

                            <td class="py-2 align-middle">
                                <div class="fw-semibold">{{ $certificate->user->first_name . $certificate->user->last_name  }}</div>
                                <small class="text-muted">User ID: {{ $certificate->user_id }}</small>
                            </td>

                            <td class="py-2 align-middle">
                                <div class="fw-medium">{{ Str::limit($certificate->event->title ?? 'Event N/A', 40) }}</div>
                                <small class="text-muted">Event ID: {{ $certificate->event_id }}</small>
                            </td>

                            <td class="py-2 align-middle">
                                <div class="fw-medium">{{ \Carbon\Carbon::parse($certificate->created_at)->format('M d, Y') }}</div>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($certificate->created_at)->format('h:i A') }}</small>
                            </td>

                            <td class="py-2 align-middle">
                                <div class="text-center">
                                    <img src="{{ asset('storage/' . $certificate->certificate_path) }}"
                                        alt="crtificate"
                                        class="img-fluid rounded shadow"
                                        style="width:50px; height:50px; max-height: 300px; object-fit: cover;">
                                </div>
                            </td>

                            <td class="py-2 align-middle text-center">
                                <div class="action-buttons-compact d-flex justify-content-center gap-1">
                                    {{-- Download Button --}}
                                    <a class="btn btn-clean-compact btn-view"
                                        href="{{ asset('storage/' . $certificate->certificate_path) }}"
                                        download="{{ strtolower($certificate->user->first_name) . '_' .
                                        strtolower(preg_replace('/[^A-Za-z0-9\-]/', '_', $certificate->event->title ?? 'event')) }}_certificate.jpg"
                                        title="Download Certificate">
                                        <i class="fas fa-download"></i>
                                    </a>

                                    <button class="btn btn-clean-compact btn-view"
                                        title="View Details"
                                        onclick="viewCertificate({{ $certificate->id }})"
                                        data-path="{{ asset('storage/' . $certificate->certificate_path) }}">
                                            <i class="fas fa-eye"></i>
                                    </button>

                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if($certificates->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-4" id="paginationSection">
            <div class="pagination-info">
                <span class="text-muted">
                    Showing {{ $certificates->firstItem() }}-{{ $certificates->lastItem() }} of {{ $certificates->total() }} results
                </span>
            </div>

            <nav aria-label="Certificates pagination">
                <ul class="pagination pagination-sm mb-0">
                    @if ($certificates->onFirstPage())
                        <li class="page-item disabled"><span class="page-link"><i class="fas fa-chevron-left"></i></span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $certificates->previousPageUrl() }}"><i class="fas fa-chevron-left"></i></a></li>
                    @endif

                    @foreach ($certificates->getUrlRange(1, $certificates->lastPage()) as $page => $url)
                        @if ($page == $certificates->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach

                    @if ($certificates->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $certificates->nextPageUrl() }}"><i class="fas fa-chevron-right"></i></a></li>
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
                <i class="fas fa-award fa-4x text-muted mb-4"></i>
                <h5 class="text-muted mb-3">No Certificates Found</h5>
                <p class="text-muted mb-4">
                    {{ request()->hasAny(['search', 'event_id', 'department'])
                        ? 'No certificates match your search criteria.'
                        : 'No certificates have been generated yet.' }}
                </p>
            </div>
        </div>
    @endif
</div>

<div class="modal fade" id="viewEventModal" tabindex="-1" aria-labelledby="viewEventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="viewEventModalLabel">
                    <i class="fas fa-eye me-2"></i>Certificate Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="viewEventContent">
                </div>
            </div>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" style="display: none;">
    @csrf @method('DELETE')
</form>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/certificates-index.css') }}">
@endpush
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/admin/certificates-index.js') }}"></script>
@if(session('success'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    showSuccessMessage('{{ session('success') }}');
});
</script>
@endif
<script>
    // Placeholder function for SweetAlert success message
    function showSuccessMessage(message) {
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: message,
            timer: 3000,
            showConfirmButton: false
        });
    }

    function viewCertificate(certificateId) {
        const viewModal = new bootstrap.Modal(document.getElementById('viewEventModal'));
        const btn = document.querySelector(`button[onclick='viewCertificate(${certificateId})']`);
        const path = btn?.dataset.path;
        viewModal.show();

        if(!path) {
            return;
        }

        document.getElementById('viewEventContent').innerHTML =
        `<div class="text-center">
            <img src="${path}"
                alt="crtificate"
                class="img-fluid rounded shadow"
                style="width:500px; height:300px; max-height: 300px; object-fit: cover;">
        </div>

        `;
    };

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('liveSearchInput');
        const tableRows = document.querySelectorAll('#eventsTableBody .certificate-row');
        const searchResultsInfo = document.getElementById('searchResultsInfo');
        const searchResultsText = document.getElementById('searchResultsText');

        function filterCertificates() {
            const query = searchInput.value.trim().toLowerCase();
            let visibleCount = 0;

            tableRows.forEach(row => {
                const searchable = row.dataset.searchable || '';
                if(searchable.includes(query) && query !== '') {
                    row.style.display = '';
                    visibleCount++;
                } else if(query === '') {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });


            // Show search info
            if(query && visibleCount === 0) {
                searchResultsInfo.style.display = 'block';
                searchResultsText.textContent = `No certificates match "${query}".`;
            } else {
                searchResultsInfo.style.display = 'none';
            }
        }

        searchInput.addEventListener('input', filterCertificates);

    });

</script>
@endpush

@endsection
