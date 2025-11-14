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
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        /* Ensure SweetAlert stays below navigation */
        .swal2-container {
            z-index: 9998 !important;
        }

        /* If your nav has z-index, make sure it's higher */
        nav,
        .navbar {
            z-index: 9999 !important;
            position: relative;
        }

        /* Custom SweetAlert styling */
        .swal2-popup {
            border-radius: 15px;
            padding: 2rem;
        }

        .swal2-title {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .swal2-html-container {
            font-size: 1rem;
        }

        .swal2-confirm,
        .swal2-cancel {
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .swal2-confirm {
            background-color: #10b981 !important;
        }

        .swal2-confirm:hover {
            background-color: #059669 !important;
        }

        .swal2-cancel {
            background-color: #ef4444 !important;
        }

        .swal2-cancel:hover {
            background-color: #dc2626 !important;
        }
    </style>
</head>

<body>
    <!-- Include Navigation -->
    @include('layouts.navigation')

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Events Section -->
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
                    <!-- Events Grid -->
                    <div class="events-grid">
                        @foreach ($events as $event)
                            <div class="event-card"
                                data-end-date="{{ \Carbon\Carbon::parse($event->date)->format('Y-m-d') }}"
                                data-end-time="{{ \Carbon\Carbon::parse($event->end_time)->format('H:i:s') }}">
                                <!-- Background Image -->
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

                                <!-- Status Badge (Top Left) -->
                                <div
                                    class="event-badge {{ $event->created_at >= now()->subWeek() ? 'new' : ($event->date >= now() && $event->date <= now()->addWeek() ? 'upcoming' : 'event') }}">
                                    @if ($event->created_at >= now()->subWeek())
                                        NEW
                                    @elseif($event->date >= now() && $event->date <= now()->addWeek())
                                        Early Bird
                                    @elseif($event->is_recurring)
                                        Popular
                                    @else
                                        EVENT
                                    @endif
                                </div>

                                <!-- Info Button -->
                                <button class="info-btn" onclick="showEventInfo(this)"
                                    title="Event Details" 
                                    data-event-title="{{ e($event->title) }}"
                                    data-event-location="{{ e($event->location) }}"
                                    data-event-date="{{ $event->date->format('F d, Y') }}"
                                    data-event-time="{{ e($event->start_time ? $event->start_time->format('g:i A') : 'TBA') }}"
                                    data-event-department="{{ e($event->department_display) }}"
                                    data-event-description="{{ e($event->description ?? 'No description available.') }}"
                                    data-event-image="{{ $event->hasImage() ? $event->image_url : '' }}"
                                    data-event-status="{{ e($event->status) }}">
                                    <i class="fas fa-info"></i>
                                </button>

                                <!-- Department/Exclusivity Badge (Top Right) -->
                                @if ($event->is_exclusive)
                                    <div class="exclusivity-badge exclusive">
                                        <i class="fas fa-graduation-cap"></i>
                                        {{ auth()->user()->department }}
                                    </div>
                                @else
                                    <div class="exclusivity-badge open">
                                        <i class="fas fa-globe"></i>
                                        ALL
                                    </div>
                                @endif

                                <!-- Event Content (Bottom Overlay) -->
                                <div class="event-content">
                                    <!-- Location Badge -->
                                    <div class="location-badge">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>{{ e(Str::limit($event->location, 25)) }}</span>
                                    </div>

                                    <!-- Event Title -->
                                    <h3 class="event-title">{{ e($event->title) }}</h3>

                                    <!-- Button -->
                                    @if ($event->join_status === 'joined')
                                        <div class="flex-container">
                                            <button class="custom-btn leave-btn joined"
                                                data-event-id="{{ $event->id }}"
                                                data-event-title="{{ e($event->title) }}"
                                                onclick="toggleEventJoin(this)">
                                                <span class="btn-text">Leave</span>
                                            </button>

                                            <!-- Feedback Button -->
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
                                        <button class="custom-btn leave-btn pending" disabled>
                                            <span class="btn-text">Pending Approval</span>
                                        </button>
                                    @else
                                        <button class="register-btn" data-event-id="{{ $event->id }}"
                                            data-event-title="{{ e($event->title) }}" onclick="toggleEventJoin(this)">
                                            <span class="btn-text">Join Now <span class="time-left-badge"></span></span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
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
                                        @elseif (
                                            $page == 1 ||
                                                $page == $events->lastPage() ||
                                                ($page >= $events->currentPage() - 2 && $page <= $events->currentPage() + 2))
                                            <a href="{{ $url }}"
                                                class="pagination-btn"><span>{{ $page }}</span></a>
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
                                    Showing {{ $events->firstItem() }} to {{ $events->lastItem() }} of
                                    {{ $events->total() }} results
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="empty-state">
                        <i class="fas fa-calendar-times"></i>
                        <h3>No events available</h3>
                        <p>
                            @if (request('department') || request('search'))
                                No events match your current filters. Try adjusting your search criteria.
                            @else
                                There are no events available for your department at the moment. Please check back
                                later.
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Event Info Modal - Profile Card Style -->
    <div id="eventInfoModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-event-image-container" id="modalImageContainer">
                    <img src="" alt="Event" class="modal-event-image" id="modalImage" style="display: none;">
                    <div class="modal-event-image-placeholder" id="modalImagePlaceholder" style="display: none;">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
                <button class="modal-close" onclick="closeEventInfo()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-profile-section">
                <h2 class="modal-event-title" id="modalTitle">Event Title</h2>
                <p class="modal-event-subtitle" id="modalSubtitle">Event Information</p>
                <div class="modal-badges">
                    <span class="modal-badge badge-featured">FEATURED</span>
                    <span class="modal-badge" id="modalStatusBadge">ACTIVE</span>
                </div>
            </div>
            <div class="modal-body" id="eventInfoContent">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-item-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="info-label">Location</div>
                        <div class="info-text" id="modalLocation">Convention Center</div>
                    </div>
                    <div class="info-item">
                        <div class="info-item-icon">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="info-label">Date & Time</div>
                        <div class="info-text" id="modalDateTime">Nov 15, 2025</div>
                    </div>
                    <div class="info-item">
                        <div class="info-item-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div class="info-label">Department</div>
                        <div class="info-text" id="modalDepartment">All Departments</div>
                    </div>
                    <div class="info-item">
                        <div class="info-item-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="info-label">Time</div>
                        <div class="info-text" id="modalTime">5:00 PM</div>
                    </div>
                </div>
                <div class="description-box">
                    <div class="info-label">Event Description</div>
                    <div class="info-text" id="modalDescription">
                        Join us for an exciting event featuring industry leaders and innovators.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Feedback Modal -->
    <div id="feedbackModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Event Feedback</h2>
                <button class="modal-close" onclick="closeFeedbackModal()">
                    <i class="fas fa-times"></i>
                </button>
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

    <!-- Include Navigation Script -->
    <script src="{{ asset('user/nav/js/navbar.js') }}"></script>

    <!-- Toast container -->
    <div id="toastContainer"></div>

    @include('layouts.footer')
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.all.min.js"></script>

    <!-- Dashboard JavaScript -->
    <script src="{{ asset('user/js/dashboard.js') }}"></script>

    <script>
        // Show Event Info Modal with Profile Card Style
        function showEventInfo(button) {
            const modal = document.getElementById('eventInfoModal');
            const eventTitle = button.getAttribute('data-event-title');
            const eventLocation = button.getAttribute('data-event-location');
            const eventDate = button.getAttribute('data-event-date');
            const eventTime = button.getAttribute('data-event-time');
            const eventDepartment = button.getAttribute('data-event-department');
            const eventDescription = button.getAttribute('data-event-description');
            const eventImage = button.getAttribute('data-event-image');
            const eventStatus = button.getAttribute('data-event-status');

            // Update modal content
            document.getElementById('modalTitle').textContent = eventTitle;
            document.getElementById('modalSubtitle').textContent = `${eventLocation} • ${eventDate}`;
            document.getElementById('modalLocation').textContent = eventLocation;
            document.getElementById('modalDateTime').textContent = eventDate;
            document.getElementById('modalTime').textContent = eventTime;
            document.getElementById('modalDepartment').textContent = eventDepartment;
            document.getElementById('modalDescription').textContent = eventDescription;

            // Update status badge
            const statusBadge = document.getElementById('modalStatusBadge');
            if (eventStatus === 'active') {
                statusBadge.textContent = 'ACTIVE';
                statusBadge.className = 'modal-badge badge-active';
            } else {
                statusBadge.textContent = 'INACTIVE';
                statusBadge.className = 'modal-badge badge-inactive';
            }

            // Handle image display
            const modalImage = document.getElementById('modalImage');
            const modalPlaceholder = document.getElementById('modalImagePlaceholder');

            if (eventImage) {
                modalImage.src = eventImage;
                modalImage.style.display = 'block';
                modalPlaceholder.style.display = 'none';
            } else {
                modalImage.style.display = 'none';
                modalPlaceholder.style.display = 'flex';
            }

            modal.classList.add('active');
        }

        function closeEventInfo() {
            document.getElementById('eventInfoModal').classList.remove('active');
        }

        // Close modal when clicking outside
        document.getElementById('eventInfoModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEventInfo();
            }
        });

        function openFeedbackModal(eventId, eventTitle) {
            document.getElementById('feedbackModal').style.display = 'flex';
            document.getElementById('feedbackEventId').value = eventId;
            document.getElementById('feedbackMessage').value = '';
            document.querySelectorAll('.stars .fa-star').forEach(s => s.classList.remove('selected'));
        }

        function closeFeedbackModal() {
            document.getElementById('feedbackModal').style.display = 'none';
        }

        // Handle Star Rating
        document.querySelectorAll('.stars .fa-star').forEach(star => {
            star.addEventListener('click', function() {
                const value = this.dataset.value;
                document.querySelectorAll('.stars .fa-star').forEach(s => {
                    s.classList.toggle('selected', s.dataset.value <= value);
                });
            });
        });

        // Submit Feedback
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
                    body: JSON.stringify({
                        feedback,
                        rating
                    })
                })
                .then(res => res.json())
                .then(data => {
                    Swal.fire({
                        icon: data.success ? 'success' : 'error',
                        title: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    closeFeedbackModal();
                })
                .catch(() => {
                    Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
                });
        });

        // Enhanced toggle event join with SweetAlert confirmation
        function toggleEventJoin(button) {
            const eventId = button.getAttribute('data-event-id');
            const isJoined = button.getAttribute('data-joined') === 'true';
            const eventTitle = button.getAttribute('data-event-title');
            const action = isJoined ? 'leave' : 'join';

            // Show confirmation dialog
            Swal.fire({
                title: isJoined ? 'Leave Event?' : 'Join Event?',
                html: `Are you sure you want to ${action} <strong>"${eventTitle}"</strong>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No',
                confirmButtonColor: isJoined ? '#ef4444' : '#10b981',
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
                        const isNowJoined = action === 'join';
                        button.setAttribute('data-joined', isNowJoined);
                        button.querySelector('.btn-text').textContent = isNowJoined ? 'Pending Approval' : 'Join Now';

                        if (isNowJoined) {
                            button.classList.add('pending');
                        } else {
                            button.classList.remove('pending');
                        }

                        Swal.fire({
                            title: 'Success!',
                            text: data.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
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

        // Timer functionality
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
        });
    </script>
</body>
</html>