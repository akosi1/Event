<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'EventAP') }} - Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('user/dashboard/dashboard.css') }}" rel="stylesheet">
    <link href="{{ asset('user/nav/css/navbar.css') }}" rel="stylesheet">
    <link href="{{ asset('user/footer/footer.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<body>
    @include('layouts.navigation')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="events-section">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-fire"></i>
                        All Events
                    </h2>
                    <div class="search-container">
                        <form method="GET" action="{{ route('dashboard') }}">
                            <i class="fas fa-search search-icon"></i>
                            @foreach (request()->except('search') as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <input type="text" name="search" class="search-input" placeholder="Search events..."
                                value="{{ e(request('search')) }}" maxlength="100" autocomplete="off">
                        </form>
                    </div>
                </div>
                @if ($events->count() > 0)
                    <div class="events-grid">
                        @foreach ($events as $event)
                            @php
                                $user = auth()->user();
                                $canJoin = $event->canUserJoin($user);
                                $isEligible = $event->isAvailableForUser($user);
                                $departmentMatch = $event->isAvailableForDepartment($user->department);
                                $yearLevelMatch = $event->isAvailableForYearLevel($user->year_level);
                                // Check if event is cancelled and should be shown with watermark
                                $isCancelled = $event->status === 'cancelled';
                                $showCancelledCard = $isCancelled && now()->diffInDays($event->updated_at) <= 2;
                                // Calculate time since cancellation for display
                                $daysSinceCancellation = $isCancelled ? now()->diffInDays($event->updated_at) : 0;
                            @endphp
                            <!-- Always show the event card, but with different styling for cancelled events -->
                            <div class="event-card {{ $isCancelled ? 'cancelled-event' : '' }}"
                                data-end-date="{{ \Carbon\Carbon::parse($event->date)->format('Y-m-d') }}"
                                data-end-time="{{ \Carbon\Carbon::parse($event->end_time)->format('H:i:s') }}"
                                data-cancelled-at="{{ $event->updated_at }}">
                                @if ($isCancelled)
                                    <!-- Watermark overlay for cancelled events -->
                                    <div class="cancelled-watermark">
                                        <div class="watermark-content">
                                            <i class="fas fa-ban"></i> CANCELLED
                                        </div>
                                    </div>
                                @endif
                                <div class="event-image-container">
                                    @if ($event->image)
                                        <img src="{{ $event->image }}" alt="{{ e($event->title) }}"
                                            class="event-image"
                                            onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'no-image-placeholder\'><i class=\'fas fa-calendar-alt\'></i></div>';">
                                    @else
                                        <div class="no-image-placeholder">
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="event-badge" style="background: transparent; color: #ffffff; font-weight: 700; padding: 0.4rem 0.9rem; border: none; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8), 0 0 10px rgba(0, 0, 0, 0.5);">
                                    {{ e($event->year_level_display ?? 'All Years') }}
                                </div>
                                <button class="info-btn" onclick="showEventInfo(this)"
                                    title="Event Details" 
                                    data-event-title="{{ e($event->title) }}"
                                    data-event-location="{{ e($event->location) }}"
                                    data-event-date="{{ $event->date->format('F d, Y') }}"
                                    data-event-time="{{ e($event->start_time ? $event->start_time->format('g:i A') : 'TBA') }}"
                                    data-event-department="{{ e($event->department_display) }}"
                                    data-event-year-level="{{ e($event->year_level_display) }}"
                                    data-event-description="{{ e($event->description ?? 'No description available.') }}"
                                    data-event-status="{{ e($event->status) }}">
                                    <i class="fas fa-info"></i>
                                </button>
                                @if ($event->is_exclusive)
                                    <div class="exclusivity-badge exclusive">
                                        <i class="fas fa-graduation-cap"></i>
                                        {{ $event->department_display }}
                                    </div>
                                @else
                                    <div class="exclusivity-badge open">
                                        <i class="fas fa-globe"></i>
                                        ALL
                                    </div>
                                @endif
                                <div class="event-content">
                                    <div class="location-badge">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>{{ e(Str::limit($event->location, 25)) }}</span>
                                    </div>
                                    <h3 class="event-title">{{ e($event->title) }}</h3>
                                    <!-- Show status for cancelled events -->
                                    @if ($isCancelled)
                                        <div class="cancelled-message">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            This event has been cancelled.
                                            @if ($event->cancel_reason)
                                                Reason: {{ $event->cancel_reason }}
                                            @endif
                                        </div>
                                    @endif
                                    <!-- View cancellation document button for cancelled events -->
                                    @if ($isCancelled && $event->hasCancellationDocument())
                                        <div class="cancellation-document-section">
                                            <button class="custom-btn view-doc-btn" 
                                                onclick="viewCancellationDocument({{ $event->id }})"
                                                title="View Cancellation Document">
                                                <i class="fas fa-file-alt"></i> View Proof
                                            </button>
                                        </div>
                                    @endif
                                    <!-- Show how many days left until removal for cancelled events -->
                                    @if ($isCancelled && $daysSinceCancellation < 2)
                                        <div class="days-left-message">
                                            Will be removed in {{ 2 - $daysSinceCancellation }} day{{ 2 - $daysSinceCancellation > 1 ? 's' : '' }}
                                        </div>
                                    @elseif ($isCancelled)
                                        <div class="days-left-message">
                                            Last day before removal
                                        </div>
                                    @endif
                                    <!-- Regular event actions -->
                                    @if (!$isCancelled)
                                        @if ($event->join_status === 'joined')
                                            <div class="flex-container">
                                                <button class="custom-btn leave-btn joined"
                                                    data-event-id="{{ $event->id }}"
                                                    data-event-title="{{ e($event->title) }}"
                                                    onclick="toggleEventJoin(this)">
                                                    <span class="btn-text">Leave</span>
                                                </button>
                                                <button class="custom-btn feedback-btn"
                                                    onclick="openFeedbackModal({{ $event->id }}, '{{ e($event->title) }}')">
                                                    <i class="fas fa-comment-dots"></i> Feedback
                                                </button>
                                                @php
                                                    $certificate = $certificates->firstWhere('event_id', $event->id);
                                                @endphp
                                                <button class="custom-btn generate-btn"
                                                    @if ($certificate)
                                                        data-certificate-path="{{ asset('storage/' . $certificate->certificate_path) }}"
                                                        data-event-title="{{ $certificate->event->title }}"
                                                        data-certificate-date="{{ $certificate->created_at }}"
                                                        onclick="showCertificateInfo({{ $certificate->event_id }})"
                                                    @else
                                                        data-event-id="{{ $event->id }}"
                                                        onclick="toggleGenerateCertificate(this)"
                                                    @endif
                                                    title="{{ (!$event->hasEnded || !$event->certificate_template_image) ? 'The Event is not yet end or no Certificate template' : '' }}">
                                                    <span class="btn-text">
                                                        {{ $certificate && $event->hasEnded ? 'View' : 'Certificate' }}
                                                    </span>
                                                </button>
                                            </div>
                                        @elseif($event->join_status === 'pending')
                                            <div class="flex-container">
                                                <button class="custom-btn leave-btn pending" 
                                                    data-event-id="{{ $event->id }}"
                                                    data-event-title="{{ e($event->title) }}"
                                                    onclick="toggleEventJoin(this)">
                                                    <span class="btn-text">Cancel Request</span>
                                                </button>
                                                <button class="custom-btn" disabled style="opacity: 0.6;">
                                                    <span class="btn-text">Pending Approval</span>
                                                </button>
                                            </div>
                                        @else
                                            @if (!$isEligible)
                                                <div style="text-align: center; padding: 0.5rem;">
                                                    <button class="register-btn" disabled style="opacity: 0.6; cursor: not-allowed;">
                                                        <span class="btn-text">Not Eligible</span>
                                                    </button>
                                                    <p style="font-size: 0.75rem; color: #ef4444; margin-top: 0.5rem;">
                                                        @if (!$departmentMatch)
                                                            Department mismatch
                                                        @elseif (!$yearLevelMatch)
                                                            Year level mismatch
                                                        @else
                                                            Not eligible for this event
                                                        @endif
                                                    </p>
                                                </div>
                                            @elseif ($event->status !== 'active')
                                                <button class="register-btn" disabled style="opacity: 0.6; cursor: not-allowed;">
                                                    <span class="btn-text">{{ ucfirst($event->status) }}</span>
                                                </button>
                                            @elseif ($event->date < now())
                                                <button class="register-btn" disabled style="opacity: 0.6; cursor: not-allowed;">
                                                    <span class="btn-text">Event Ended</span>
                                                </button>
                                            @else
                                                <button class="register-btn" 
                                                    data-event-id="{{ $event->id }}"
                                                    data-event-title="{{ e($event->title) }}" 
                                                    onclick="toggleEventJoin(this)">
                                                    <span class="btn-text">Join Now <span class="time-left-badge"></span></span>
                                                </button>
                                            @endif
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if ($events->hasPages())
                        <div class="pagination-container">
                            <div class="pagination-wrapper">
                                <div class="pagination-nav">
                                    @if ($events->onFirstPage())
                                        <span class="pagination-btn prev-next disabled">
                                            <i class="fas fa-chevron-left"></i>
                                            <span>Previous</span>
                                        </span>
                                    @else
                                        <a href="{{ $events->previousPageUrl() }}" class="pagination-btn prev-next">
                                            <i class="fas fa-chevron-left"></i>
                                            <span>Previous</span>
                                        </a>
                                    @endif
                                    @foreach ($events->getUrlRange(1, $events->lastPage()) as $page => $url)
                                        @if ($page == $events->currentPage())
                                            <span class="pagination-btn active"><span>{{ $page }}</span></span>
                                        @elseif ($page == 1 || $page == $events->lastPage() || ($page >= $events->currentPage() - 2 && $page <= $events->currentPage() + 2))
                                            <a href="{{ $url }}" class="pagination-btn"><span>{{ $page }}</span></a>
                                        @elseif ($page == $events->currentPage() - 3 || $page == $events->currentPage() + 3)
                                            <span class="pagination-dots">...</span>
                                        @endif
                                    @endforeach
                                    @if ($events->hasMorePages())
                                        <a href="{{ $events->nextPageUrl() }}" class="pagination-btn prev-next">
                                            <span>Next</span>
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    @else
                                        <span class="pagination-btn prev-next disabled">
                                            <span>Next</span>
                                            <i class="fas fa-chevron-right"></i>
                                        </span>
                                    @endif
                                </div>
                                <div class="pagination-info">
                                    Showing {{ $events->firstItem() }} to {{ $events->lastItem() }} of {{ $events->total() }} results
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="empty-state">
                        <i class="fas fa-calendar-times"></i>
                        <h3>No events available</h3>
                        <p>
                            @if (request('department') || request('search'))
                                No events match your current filters. Try adjusting your search criteria.
                            @else
                                There are no upcoming events available for your department at the moment. Please check back later.
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <!-- ✅ RECEIPT STYLE EVENT INFO MODAL -->
    <div id="eventInfoModal" class="modal-overlay">
        <div class="modal-content">
            <button class="modal-close" onclick="closeEventInfo()">
                <i class="fas fa-times"></i>
            </button>
            <!-- Receipt Header -->
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle">EVENT DETAILS</h2>
                <p class="modal-subtitle">
                    <i class="fas fa-calendar-alt"></i> 
                    <span id="modalHeaderDate">Date</span>
                </p>
            </div>
            <!-- Receipt Body -->
            <div class="modal-body">
                <!-- Event Title as First Item -->
                <div class="info-item">
                    <span class="info-label">Event</span>
                    <span class="info-text" id="modalEventName">Event Name</span>
                </div>
                <!-- Location -->
                <div class="info-item">
                    <span class="info-label">Location</span>
                    <span class="info-text" id="modalLocation">Location</span>
                </div>
                <!-- Date -->
                <div class="info-item">
                    <span class="info-label">Date</span>
                    <span class="info-text" id="modalDate">Date</span>
                </div>
                <!-- Time -->
                <div class="info-item">
                    <span class="info-label">Time</span>
                    <span class="info-text" id="modalTime">Time</span>
                </div>
                <!-- Department -->
                <div class="info-item">
                    <span class="info-label">Department</span>
                    <span class="info-text" id="modalDepartment">Department</span>
                </div>
                <!-- Year Level -->
                <div class="info-item">
                    <span class="info-label">Year Level</span>
                    <span class="info-text" id="modalYearLevel">Year Level</span>
                </div>
                <!-- Description Box -->
                <div class="description-box">
                    <div class="info-label">Description</div>
                    <div class="info-text" id="modalDescription">
                        Event description will appear here.
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ✅ FEEDBACK MODAL -->
    <div id="feedbackModal" class="modal-overlay">
        <div class="modal-content">
            <button class="modal-close" onclick="closeFeedbackModal()">
                <i class="fas fa-times"></i>
            </button>
            <div class="modal-header">
                <h2 class="modal-title">Event Feedback</h2>
                <p class="modal-subtitle">Share your thoughts about this event</p>
            </div>
            <div class="modal-body">
                <form id="feedbackForm">
                    <input type="hidden" id="feedbackEventId">
                    <label for="feedbackMessage">Your Feedback</label>
                    <textarea id="feedbackMessage" placeholder="Share your thoughts..." required></textarea>
                    <div class="rating">
                        <label>Rate the event:</label>
                        <div class="stars">
                            <i class="fas fa-star" data-value="1"></i>
                            <i class="fas fa-star" data-value="2"></i>
                            <i class="fas fa-star" data-value="3"></i>
                            <i class="fas fa-star" data-value="4"></i>
                            <i class="fas fa-star" data-value="5"></i>
                        </div>
                    </div>
                    <button type="submit" class="register-btn" style="margin-top: 1rem;">Submit Feedback</button>
                </form>
            </div>
        </div>
    </div>
    <!-- ✅ CANCELLATION DOCUMENT VIEWER MODAL -->
    <div id="cancellationDocumentModal" class="modal-overlay">
        <div class="modal-content">
            <button class="modal-close" onclick="closeCancellationDocumentModal()">
                <i class="fas fa-times"></i>
            </button>
            <div class="modal-header">
                <h2 class="modal-title">Cancellation Document</h2>
                <p class="modal-subtitle">Official proof of event cancellation</p>
            </div>
            <div class="modal-body">
                <div id="documentViewerContent" class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Loading document...</p>
                </div>
                <div class="document-actions mt-3">
                    <button class="custom-btn" id="downloadDocBtn" style="margin-right: 0.5rem;">
                        <i class="fas fa-download"></i> Download
                    </button>
                    <button class="custom-btn" id="closeDocBtn">
                        <i class="fas fa-times"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div id="toastContainer"></div>
    @include('layouts.footer')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.all.min.js"></script>
    <script src="{{ asset('user/js/dashboard.js') }}"></script>
    <script>
        // ✅ Show Event Info Modal - RECEIPT STYLE
        function showEventInfo(button) {
            const modal = document.getElementById('eventInfoModal');
            const eventTitle = button.getAttribute('data-event-title');
            const eventLocation = button.getAttribute('data-event-location');
            const eventDate = button.getAttribute('data-event-date');
            const eventTime = button.getAttribute('data-event-time');
            const eventDepartment = button.getAttribute('data-event-department');
            const eventYearLevel = button.getAttribute('data-event-year-level');
            const eventDescription = button.getAttribute('data-event-description');
            // Set modal content
            document.getElementById('modalTitle').textContent = 'EVENT DETAILS';
            document.getElementById('modalHeaderDate').textContent = eventDate;
            document.getElementById('modalEventName').textContent = eventTitle;
            document.getElementById('modalLocation').textContent = eventLocation;
            document.getElementById('modalDate').textContent = eventDate;
            document.getElementById('modalTime').textContent = eventTime;
            document.getElementById('modalDepartment').textContent = eventDepartment;
            document.getElementById('modalYearLevel').textContent = eventYearLevel || 'All Years';
            document.getElementById('modalDescription').textContent = eventDescription;
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        function closeEventInfo() {
            document.getElementById('eventInfoModal').classList.remove('active');
            document.body.style.overflow = '';
        }
        document.getElementById('eventInfoModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEventInfo();
            }
        });
        // ✅ Feedback Modal
        function openFeedbackModal(eventId, eventTitle) {
            document.getElementById('feedbackModal').classList.add('active');
            document.getElementById('feedbackEventId').value = eventId;
            document.getElementById('feedbackMessage').value = '';
            document.querySelectorAll('.stars .fa-star').forEach(s => s.classList.remove('selected'));
            document.body.style.overflow = 'hidden';
        }
        function closeFeedbackModal() {
            document.getElementById('feedbackModal').classList.remove('active');
            document.body.style.overflow = '';
        }
        document.getElementById('feedbackModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeFeedbackModal();
            }
        });
        document.querySelectorAll('.stars .fa-star').forEach(star => {
            star.addEventListener('click', function() {
                const value = this.dataset.value;
                document.querySelectorAll('.stars .fa-star').forEach(s => {
                    s.classList.toggle('selected', s.dataset.value <= value);
                });
            });
        });
        document.getElementById('feedbackForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const eventId = document.getElementById('feedbackEventId').value;
            const feedback = document.getElementById('feedbackMessage').value;
            const rating = document.querySelectorAll('.stars .selected').length;
            fetch(`/events/${eventId}/feedback`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ feedback, rating })
            })
            .then(res => res.json())
            .then(data => {
                Swal.fire({
                    icon: data.success ? 'success' : 'error',
                    title: data.message,
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
                closeFeedbackModal();
            })
            .catch(() => {
                Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
            });
        });
        // ✅ View Cancellation Document - FIXED VERSION
        function viewCancellationDocument(eventId) {
            const modal = document.getElementById('cancellationDocumentModal');
            const viewer = document.getElementById('documentViewerContent');
            const downloadBtn = document.getElementById('downloadDocBtn');
            const closeBtn = document.getElementById('closeDocBtn');
            
            // Show loading state
            viewer.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-3 text-muted">Loading document...</p></div>';
            
            // Fetch event data to get document info
            fetch(`/events/${eventId}`)
                .then(response => response.json())
                .then(eventData => {
                    if (!eventData.cancellation_document_url) {
                        viewer.innerHTML = `
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                No cancellation document available.
                            </div>
                        `;
                        return;
                    }
                    
                    // Setup download button
                    downloadBtn.onclick = () => {
                        window.open(eventData.cancellation_document_url, '_blank');
                    };
                    
                    // Display document based on type
                    const docExtension = eventData.cancellation_document_extension || '';
                    const docName = eventData.cancellation_document_name || 'cancellation_document.' + docExtension;
                    
                    if (docExtension === 'pdf') {
                        // For PDF, use iframe for preview
                        viewer.innerHTML = `
                            <div class="text-center">
                                <iframe src="${eventData.cancellation_document_url}#toolbar=1&navpanes=0&scrollbar=1" 
                                        style="width:100%;height:600px;border:none;border-radius:8px;" 
                                        frameborder="0"></iframe>
                            </div>
                        `;
                    } else if (docExtension === 'docx') {
                        // For DOCX, show a message since browser can't preview it directly
                        viewer.innerHTML = `
                            <div class="alert alert-info mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                Word document preview - Download for full editing capabilities
                            </div>
                            <div class="text-center mt-4">
                                <i class="fas fa-file-word fa-5x text-primary mb-3"></i>
                                <p class="text-muted">${docName}</p>
                                <p class="small text-muted">Click download button to view this document</p>
                            </div>
                        `;
                    } else {
                        // For other formats, show a warning
                        viewer.innerHTML = `
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Preview not available. Please download to view.
                            </div>
                            <div class="text-center mt-4">
                                <i class="fas fa-file fa-5x text-muted mb-3"></i>
                                <p class="text-muted">${docName}</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error loading document:', error);
                    viewer.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Failed to load document. Please try again.
                        </div>
                    `;
                })
                .finally(() => {
                    modal.classList.add('active');
                    document.body.style.overflow = 'hidden';
                });
        }
        function closeCancellationDocumentModal() {
            document.getElementById('cancellationDocumentModal').classList.remove('active');
            document.body.style.overflow = '';
        }
        document.getElementById('cancellationDocumentModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCancellationDocumentModal();
            }
        });
        document.getElementById('closeDocBtn').addEventListener('click', closeCancellationDocumentModal);
        // ✅ Event Join/Leave - HANDLES BOTH JOINED AND PENDING
        function toggleEventJoin(button) {
            const eventId = button.getAttribute('data-event-id');
            const isJoined = button.classList.contains('joined');
            const isPending = button.classList.contains('pending');
            const eventTitle = button.getAttribute('data-event-title');
            let action, title, message, confirmColor;
            if (isJoined) {
                action = 'leave';
                title = 'Leave Event?';
                message = `Are you sure you want to leave <strong>"${eventTitle}"</strong>?`;
                confirmColor = '#ef4444';
            } else if (isPending) {
                action = 'leave';
                title = 'Cancel Request?';
                message = `Are you sure you want to cancel your pending request for <strong>"${eventTitle}"</strong>?`;
                confirmColor = '#ef4444';
            } else {
                action = 'join';
                title = 'Join Event?';
                message = `Are you sure you want to join <strong>"${eventTitle}"</strong>?`;
                confirmColor = '#10b981';
            }
            Swal.fire({
                title: title,
                html: message,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No',
                confirmButtonColor: confirmColor,
                cancelButtonColor: '#6b7280',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    processEventAction(button, eventId, action, eventTitle);
                }
            });
        }
        function toggleGenerateCertificate(button) {
            const eventId = button.getAttribute('data-event-id');
            Swal.fire({
                title: 'Generate Certificate',
                html: `Are you sure you want to generate Certificate?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No',
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    processCertificateGeneration(button, eventId);
                }
            });
        }
        function processCertificateGeneration(button, eventId) {
            const url = `/events/${eventId}/generate-certificate`;
            button.disabled = true;
            button.style.opacity = '0.6';
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#ef4444'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'An error occurred. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#ef4444'
                });
            })
            .finally(() => {
                button.disabled = false;
                button.style.opacity = '1';
            });
        }
        function processEventAction(button, eventId, action, eventTitle) {
            const url = `/events/${eventId}/${action}`;
            button.disabled = true;
            button.style.opacity = '0.6';
            const method = action === 'leave' ? 'DELETE' : 'POST';
            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#ef4444'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'An error occurred. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#ef4444'
                });
            })
            .finally(() => {
                button.disabled = false;
                button.style.opacity = '1';
            });
        }
        function showCertificateInfo(eventId) {
            window.location.href = `/certificates?event_id=${eventId}`;
        }
        // ✅ Timer functionality for countdown
        document.addEventListener('DOMContentLoaded', function() {
            const images = document.querySelectorAll('.event-image');
            const eventCards = document.querySelectorAll('.event-card');
            images.forEach(img => {
                img.addEventListener('error', function() {
                    const placeholder = document.createElement('div');
                    placeholder.className = 'no-image-placeholder';
                    placeholder.innerHTML = '<i class="fas fa-calendar-alt"></i>';
                    this.parentElement.replaceChild(placeholder, this);
                });
            });
            eventCards.forEach(card => {
                const endDate = card.getAttribute('data-end-date');
                const endTime = card.getAttribute('data-end-time');
                const icon = card.querySelector('.info-btn');
                const timeLeftBadge = card.querySelector('.time-left-badge');
                if (!endDate || !endTime || !timeLeftBadge) return;
                const endDateTime = new Date(`${endDate}T${endTime}`);
                function updateTimer() {
                    const now = new Date();
                    const diff = endDateTime - now;
                    if (diff <= 0) {
                        timeLeftBadge.textContent = '';
                        icon.classList.remove('blinking');
                        return;
                    }
                    icon.classList.add('blinking');
                    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    if (days > 0) {
                        timeLeftBadge.textContent = `${days}d ${hours}h ${minutes}m left`;
                    } else if (hours > 0) {
                        timeLeftBadge.textContent = `${hours}h ${minutes}m left`;
                    } else {
                        timeLeftBadge.textContent = `${minutes}m left`;
                    }
                }
                updateTimer();
                setInterval(updateTimer, 60000);
            });
            // Close modals on ESC key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeEventInfo();
                    closeFeedbackModal();
                    closeCancellationDocumentModal();
                }
            });
        });
    </script>
</body>
</html>