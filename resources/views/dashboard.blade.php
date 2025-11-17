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
                            @endphp

                            <div class="event-card"
                                data-end-date="{{ \Carbon\Carbon::parse($event->date)->format('Y-m-d') }}"
                                data-end-time="{{ \Carbon\Carbon::parse($event->end_time)->format('H:i:s') }}">
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

    <div id="toastContainer"></div>
    @include('layouts.footer')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.all.min.js"></script>
    <!-- <script src="{{ asset('user/js/dashboard.js') }}"></script> -->

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
                }
            });
        });
    </script>
    <style>
        * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
    background: linear-gradient(135deg, #5e2a84 0%, #3d1a5f 50%, #2c0e44 100%);
    color: #2d3748;
    line-height: 1.6;
    min-height: 100vh;
    position: relative;
}

body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background:
        radial-gradient(circle at 20% 30%, rgba(142, 68, 173, 0.3) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(155, 89, 182, 0.25) 0%, transparent 50%),
        radial-gradient(circle at 50% 50%, rgba(102, 51, 153, 0.2) 0%, transparent 60%);
    pointer-events: none;
    z-index: 0;
}

body > * {
    position: relative;
    z-index: 1;
}

/* Events Section */
.events-section {
    background: transparent;
    padding: 2rem 1rem;
    max-width: 1400px;
    margin: 2rem auto;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2.5rem;
    flex-wrap: wrap;
    gap: 1.5rem;
}

.section-title {
    font-size: 2rem;
    font-weight: 700;
    color: white;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
}

/* Search Input */
.search-container {
    position: relative;
    max-width: 600px;
    flex: 1;
    min-width: 250px;
}

.search-container form {
    position: relative;
    display: flex;
    align-items: center;
}

.search-input {
    width: 100%;
    padding: 1.1rem 1.75rem 1.1rem 3.75rem;
    border: 2px solid rgba(255, 255, 255, 0.2);
    border-radius: 50px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.95);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    color: #2d3748;
    font-weight: 500;
}

.search-input::placeholder {
    color: #a0aec0;
    font-weight: 400;
}

.search-input:focus {
    outline: none;
    background: white;
    border-color: rgba(255, 255, 255, 0.5);
    box-shadow: 0 6px 30px rgba(0, 0, 0, 0.3);
    transform: translateY(-2px);
}

.search-icon {
    position: absolute;
    left: 1.5rem;
    top: 50%;
    transform: translateY(-50%);
    color: #8b5cf6;
    pointer-events: none;
    font-size: 1.2rem;
    z-index: 2;
}

/* Events Grid - Responsive */
.events-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.5rem;
}

.event-card {
    background: transparent;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
    height: 400px;
    position: relative;
    cursor: pointer;
}

.event-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 16px 50px rgba(0, 0, 0, 0.4);
}

.event-image-container {
    position: absolute;
    width: 100%;
    height: 100%;
    overflow: hidden;
}

.event-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s ease;
}

.event-card:hover .event-image {
    transform: scale(1.1);
}

.no-image-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
}

.no-image-placeholder i {
    font-size: 3rem;
    color: rgba(255, 255, 255, 0.3);
}

/* Overlay gradient */
.event-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        to bottom,
        rgba(0, 0, 0, 0.3) 0%,
        rgba(0, 0, 0, 0.5) 50%,
        rgba(0, 0, 0, 0.9) 100%
    );
    z-index: 1;
    transition: opacity 0.4s ease;
}

.event-card:hover::before {
    opacity: 0.95;
}

/* Badge Styles */
.event-badge {
    position: absolute;
    top: 1rem;
    left: 1rem;
    padding: 0.35rem 0.7rem;
    border-radius: 6px;
    font-size: 0.6rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    backdrop-filter: blur(20px);
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);
    z-index: 3;
}

.event-badge.new {
    background: rgba(147, 51, 234, 0.95);
    color: white;
}

.event-badge.upcoming {
    background: rgba(239, 68, 68, 0.95);
    color: white;
}

.event-badge.event {
    background: rgba(59, 130, 246, 0.95);
    color: white;
}

/* Info Button */
.info-btn {
    position: absolute;
    top: 1rem;
    right: 1rem;
    width: 36px;
    height: 36px;
    background: rgba(30, 41, 59, 0.9);
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 3;
    transition: all 0.3s ease;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);
}

.info-btn:hover {
    background: rgba(30, 41, 59, 1);
    border-color: rgba(255, 255, 255, 0.4);
    transform: scale(1.1);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
}

.info-btn i {
    color: white;
    font-size: 0.85rem;
}

/* Exclusivity Badge */
.exclusivity-badge {
    position: absolute;
    top: 1rem;
    right: 4.5rem;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.4rem 0.8rem;
    border-radius: 6px;
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    backdrop-filter: blur(20px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
    text-transform: uppercase;
    z-index: 3;
    white-space: nowrap;
}

.exclusivity-badge.exclusive {
    background: rgba(239, 68, 68, 0.95);
    color: white;
}

.exclusivity-badge.open {
    background: rgba(34, 197, 94, 0.95);
    color: white;
}

.exclusivity-badge i {
    font-size: 0.65rem;
}

/* Event Content */
.event-content {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    padding: 1.5rem;
    z-index: 2;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.location-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.35rem 0.65rem;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border-radius: 6px;
    font-size: 0.7rem;
    color: rgba(255, 255, 255, 0.95);
    font-weight: 500;
    align-self: flex-start;
    margin-bottom: 0.3rem;
}

.location-badge i {
    font-size: 0.65rem;
}

.event-title {
    font-size: 1.4rem;
    font-weight: 800;
    color: white;
    line-height: 1.2;
    margin-bottom: 0.75rem;
    text-shadow: 0 2px 15px rgba(0, 0, 0, 0.5);
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* ===== RECEIPT STYLE MODAL ===== */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.85);
    backdrop-filter: blur(12px);
    z-index: 9998;
    display: none;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s ease;
    padding: 1rem;
    overflow-y: auto;
}

.modal-overlay.active {
    display: flex;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Receipt Style Modal Content */
.modal-content {
    background: #ffffff;
    border-radius: 0;
    padding: 0;
    max-width: 420px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
    animation: slideUp 0.4s ease;
    margin: auto;
    font-family: 'Courier New', monospace;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* Custom scrollbar for modal */
.modal-content::-webkit-scrollbar {
    width: 6px;
}

.modal-content::-webkit-scrollbar-track {
    background: #f1f5f9;
}

.modal-content::-webkit-scrollbar-thumb {
    background: #94a3b8;
    border-radius: 10px;
}

.modal-content::-webkit-scrollbar-thumb:hover {
    background: #64748b;
}

/* Receipt Header */
.modal-header {
    position: relative;
    padding: 2rem 2rem 1rem 2rem;
    background: #ffffff;
    text-align: center;
    border-bottom: 2px dashed #e2e8f0;
}

.modal-close {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    background: transparent;
    border: none;
    width: 32px;
    height: 32px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    z-index: 10;
}

.modal-close:hover {
    transform: scale(1.1) rotate(90deg);
}

.modal-close i {
    color: #64748b;
    font-size: 1.2rem;
}

.modal-close:hover i {
    color: #ef4444;
}

.modal-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 0.75rem 0;
    line-height: 1.3;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.modal-subtitle {
    font-size: 0.85rem;
    color: #64748b;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    justify-content: center;
    flex-wrap: wrap;
}

.modal-subtitle i {
    font-size: 0.75rem;
}

/* Receipt Body */
.modal-body {
    padding: 1.5rem 2rem 2rem 2rem;
    background: #ffffff;
}

/* Receipt Item Style */
.info-item {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem 0;
    border-bottom: 1px dashed #e2e8f0;
    gap: 1rem;
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    font-size: 0.9rem;
    font-weight: 600;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
}

.info-label::after {
    content: ':';
    margin-left: 0;
}

.info-text {
    font-size: 0.9rem;
    color: #1e293b;
    font-weight: 600;
    text-align: right;
    line-height: 1.5;
}

/* Description Box - Receipt Style */
.description-box {
    background: #f8fafc;
    padding: 1rem;
    border-radius: 0;
    border: 1px dashed #cbd5e1;
    border-left: 3px solid #8b5cf6;
    margin-top: 1rem;
}

.description-box .info-label {
    margin-bottom: 0.5rem;
    font-size: 0.85rem;
    display: block;
    text-align: left;
}

.description-box .info-label::after {
    content: '';
}

.description-box .info-text {
    line-height: 1.7;
    color: #334155;
    white-space: pre-wrap;
    font-weight: 400;
    text-align: left;
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
}

/* Buttons */
.register-btn, .custom-btn {
    background: rgba(255, 255, 255, 0.95);
    color: #1e293b;
    border: none;
    padding: 0.75rem 1.25rem;
    border-radius: 8px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    transition: all 0.3s ease;
    font-size: 0.85rem;
    width: 100%;
    text-transform: capitalize;
    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.3);
    position: relative;
    overflow: hidden;
}

.register-btn::before, .custom-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s ease;
}

.register-btn:hover::before, .custom-btn:hover::before {
    left: 100%;
}

.register-btn:hover, .custom-btn:hover {
    background: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 18px rgba(0, 0, 0, 0.4);
}

.leave-btn {
    background: rgba(239, 68, 68, 0.95);
    color: white;
    box-shadow: 0 3px 12px rgba(239, 68, 68, 0.4);
}

.leave-btn:hover {
    background: rgba(220, 38, 38, 0.95);
    box-shadow: 0 5px 18px rgba(239, 68, 68, 0.5);
}

.generate-btn {
    background: rgba(34, 197, 94, 0.95);
    color: white;
}

.generate-btn:hover {
    background: rgba(22, 163, 74, 0.95);
}

.feedback-btn {
    background-color: #2e2f54ff;
    color: #fff;
}

.feedback-btn:hover {
    background-color: #2563eb;
}

.pending {
    background: rgba(251, 191, 36, 0.95);
    color: #1e293b;
    cursor: not-allowed;
}

.register-btn:disabled, .custom-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}

.flex-container {
    width: 100%;
    display: flex;
    gap: 0.5rem;
    align-items: center;
    flex-wrap: wrap;
}

.flex-container .custom-btn {
    flex: 1;
    min-width: 100px;
}

.time-left-badge {
    background: #9333ea;
    color: white;
    padding: 4px 10px;
    border-radius: 10px;
    font-size: 0.75rem;
    font-weight: 600;
    position: absolute;
    top: 10px;
    right: 10px;
}

.blinking {
    border: 1.5px solid red !important;
    animation: blink 1s infinite;
}

@keyframes blink {
    50% {
        opacity: 0.4;
    }
}

/* Pagination */
.pagination-container {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 3rem;
    margin-bottom: 2rem;
}

.pagination-wrapper {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
    justify-content: center;
}

.pagination-info {
    margin: 0 2rem;
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.9rem;
    font-weight: 500;
    white-space: nowrap;
}

.pagination-nav {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.pagination-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 44px;
    height: 44px;
    border: 2px solid rgba(139, 92, 246, 0.3);
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    color: white;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    font-size: 0.9rem;
    font-weight: 600;
    text-decoration: none;
    padding: 0 1rem;
}

.pagination-btn:hover {
    border-color: #8b5cf6;
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(139, 92, 246, 0.5);
}

.pagination-btn.active {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    border-color: #8b5cf6;
    color: white;
    box-shadow: 0 4px 15px rgba(139, 92, 246, 0.5);
}

.pagination-btn.disabled {
    background: rgba(255, 255, 255, 0.03);
    border-color: rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.3);
    cursor: not-allowed;
}

.pagination-btn.prev-next {
    min-width: 120px;
    gap: 0.5rem;
    padding: 0 1.5rem;
}

.pagination-dots {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    color: rgba(255, 255, 255, 0.4);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: white;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.empty-state i {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.7;
}

.empty-state h3 {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.empty-state p {
    opacity: 0.9;
    font-size: 1rem;
}

/* Feedback Modal */
#feedbackModal.modal-overlay {
    z-index: 10000 !important;
}

#feedbackModal .modal-content {
    background: rgba(37, 3, 92, 1);
    border-radius: 12px;
    max-width: 500px;
    width: 90%;
    padding: 1.5rem;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

#feedbackModal .modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding: 0;
    border-bottom: none;
}

#feedbackModal .modal-header h2 {
    color: white;
}

#feedbackModal .modal-close {
    background: none;
    border: none;
    font-size: 1.2rem;
    cursor: pointer;
    color: white;
    position: static;
    width: auto;
    height: auto;
}

#feedbackModal .stars {
    display: flex;
    gap: 5px;
    margin-top: 0.5rem;
    justify-content: center;
}

#feedbackModal .stars .fa-star {
    font-size: 1.5rem;
    color: #ccc;
    cursor: pointer;
    transition: color 0.2s ease;
}

#feedbackModal .stars .fa-star.selected {
    color: #fbbf24;
}

#feedbackModal textarea {
    width: 100%;
    border-radius: 6px;
    padding: 0.75rem;
    margin-top: 0.5rem;
    border: 1px solid rgba(114, 22, 22, 1);
    resize: vertical;
    font-family: inherit;
    font-size: 0.95rem;
    min-height: 120px;
}

#feedbackModal label {
    color: white;
    font-weight: 600;
    display: block;
    margin-bottom: 0.5rem;
}

#feedbackModal .modal-body {
    padding: 1rem 0 0 0;
}

.swal2-container {
    z-index: 10000 !important;
}

nav,
.navbar {
    z-index: 10001 !important;
    position: relative;
}

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

/* Spinner Animation */
.spinner {
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top: 2px solid white;
    border-radius: 50%;
    width: 16px;
    height: 16px;
    animation: spin 1s linear infinite;
    display: inline-block;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Toast Notifications */
.toast {
    position: fixed;
    top: 1rem;
    right: 1rem;
    background: linear-gradient(145deg, #1e293b 0%, #0f172a 100%);
    padding: 1rem 1.5rem;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    border-left: 4px solid #48bb78;
    z-index: 9999;
    transform: translateX(400px);
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    max-width: 400px;
    color: white;
}

.toast.show {
    transform: translateX(0);
}

.toast.error {
    border-left-color: #f56565;
}

/* Animation */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.event-card {
    animation: fadeInUp 0.6s ease forwards;
}

/* Ripple Effect */
.ripple {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.5);
    transform: scale(0);
    animation: ripple-animation 0.6s ease-out;
    pointer-events: none;
}

@keyframes ripple-animation {
    to {
        transform: scale(4);
        opacity: 0;
    }
}

/* Responsive Design */
@media (max-width: 1200px) {
    .events-grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    }
}

@media (max-width: 1024px) {
    .section-title {
        font-size: 1.75rem;
    }
    
    .modal-event-image-container {
        height: 250px;
    }
}

@media (max-width: 768px) {
    .events-section {
        padding: 1rem;
    }
    
    .section-header {
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
    }

    .section-title {
        font-size: 1.5rem;
        justify-content: center;
    }

    .search-container {
        max-width: 100%;
    }
    
    .search-input {
        padding: 1rem 1.5rem 1rem 3.5rem;
        font-size: 0.95rem;
    }

    .events-grid {
        grid-template-columns: 1fr;
        gap: 1.25rem;
    }

    .event-card {
        height: 380px;
    }

    .event-title {
        font-size: 1.3rem;
    }
    
    .exclusivity-badge {
        right: 3.75rem;
        font-size: 0.6rem;
        padding: 0.35rem 0.65rem;
    }
    
    .flex-container {
        gap: 0.4rem;
        flex-wrap: nowrap;
    }
    
    .flex-container .custom-btn {
        min-width: 60px;
        font-size: 0.65rem;
        padding: 0.55rem 0.5rem;
        flex: 1;
    }
    
    .flex-container .custom-btn i {
        font-size: 0.7rem;
    }
    
    .register-btn {
        font-size: 0.75rem;
        padding: 0.65rem 1rem;
    }

    .pagination-wrapper {
        flex-direction: column;
        gap: 1rem;
    }

    .pagination-info {
        margin: 0;
        order: 2;
        font-size: 0.85rem;
    }

    .pagination-nav {
        order: 1;
        justify-content: center;
    }

    .pagination-btn {
        min-width: 40px;
        height: 40px;
        font-size: 0.85rem;
    }

    .pagination-btn.prev-next {
        min-width: 100px;
        font-size: 0.8rem;
    }
    
    .modal-content {
        max-width: 95%;
        max-height: 85vh;
    }
    
    .modal-title {
        font-size: 1.25rem;
    }
    
    .modal-subtitle {
        font-size: 0.8rem;
    }
    
    .modal-header {
        padding: 1.5rem 1.5rem 1rem 1.5rem;
    }
    
    .modal-body {
        padding: 1.25rem 1.5rem 1.5rem 1.5rem;
    }
    
    .toast {
        right: 0.5rem;
        left: 0.5rem;
        max-width: calc(100% - 1rem);
    }
}

@media (max-width: 480px) {
    .events-section {
        padding: 0.75rem;
    }

    .section-title {
        font-size: 1.25rem;
        gap: 0.5rem;
    }
    
    .section-title i {
        font-size: 1rem;
    }

    .search-input {
        padding: 0.9rem 1.25rem 0.9rem 3rem;
        font-size: 0.9rem;
    }
    
    .search-icon {
        left: 1rem;
        font-size: 1rem;
    }

    .event-card {
        height: 380px;
    }

    .event-title {
        font-size: 1.15rem;
    }
    
    .event-content {
        padding: 1.25rem;
    }
    
    .location-badge {
        font-size: 0.65rem;
        padding: 0.3rem 0.55rem;
    }
    
    .info-btn {
        width: 32px;
        height: 32px;
        top: 0.75rem;
        right: 0.75rem;
    }
    
    .info-btn i {
        font-size: 0.75rem;
    }
    
    .exclusivity-badge {
        top: 0.75rem;
        right: 3.25rem;
        font-size: 0.55rem;
        padding: 0.3rem 0.55rem;
    }
    
    .event-badge {
        top: 0.75rem;
        left: 0.75rem;
        font-size: 0.55rem;
        padding: 0.3rem 0.6rem;
    }
    
    .flex-container {
        gap: 0.35rem;
        flex-wrap: nowrap;
    }
    
    .flex-container .custom-btn {
        min-width: 50px;
        font-size: 0.6rem;
        padding: 0.5rem 0.4rem;
        gap: 0.2rem;
    }
    
    .flex-container .custom-btn i {
        font-size: 0.65rem;
    }
    
    .register-btn {
        font-size: 0.7rem;
        padding: 0.6rem 0.9rem;
    }
    
    .modal-content {
        max-width: 100%;
        margin: 0.5rem;
    }
    
    .modal-title {
        font-size: 1.15rem;
    }
    
    .modal-subtitle {
        font-size: 0.75rem;
    }
    
    .modal-header {
        padding: 1.25rem 1.25rem 0.75rem 1.25rem;
    }
    
    .modal-body {
        padding: 1rem 1.25rem 1.25rem 1.25rem;
    }
    
    .info-item {
        padding: 0.6rem 0;
    }
    
    .info-label {
        font-size: 0.8rem;
    }
    
    .info-text {
        font-size: 0.8rem;
    }
    
    .description-box {
        padding: 0.85rem;
        margin-top: 0.85rem;
    }
    
    .description-box .info-label {
        font-size: 0.75rem;
    }
    
    .description-box .info-text {
        font-size: 0.8rem;
    }
    
    .pagination-btn {
        min-width: 36px;
        height: 36px;
        font-size: 0.8rem;
        padding: 0 0.75rem;
    }

    .pagination-btn.prev-next {
        min-width: 90px;
        font-size: 0.75rem;
        padding: 0 1rem;
    }
    
    .pagination-info {
        font-size: 0.8rem;
    }
    
    .empty-state {
        padding: 3rem 1.5rem;
    }
    
    .empty-state i {
        font-size: 3rem;
    }
    
    .empty-state h3 {
        font-size: 1.25rem;
    }
    
    .empty-state p {
        font-size: 0.9rem;
    }
    
    #feedbackModal .modal-content {
        width: 95%;
        padding: 1.25rem;
    }
    
    #feedbackModal .modal-header h2 {
        font-size: 1.25rem;
    }
    
    #feedbackModal textarea {
        min-height: 100px;
        font-size: 0.9rem;
    }
    
    #feedbackModal .stars .fa-star {
        font-size: 1.3rem;
    }
}

@media (max-width: 360px) {
    .section-title {
        font-size: 1.1rem;
    }
    
    .event-card {
        height: 400px;
    }
    
    .event-title {
        font-size: 1rem;
    }
    
    .flex-container {
        flex-direction: column;
        gap: 0.4rem;
    }
    
    .flex-container .custom-btn {
        width: 100%;
        min-width: 100%;
        font-size: 0.7rem;
        padding: 0.6rem 0.75rem;
    }
    
    .exclusivity-badge {
        right: 3rem;
        font-size: 0.5rem;
        padding: 0.25rem 0.5rem;
    }
    
    .info-btn {
        width: 30px;
        height: 30px;
    }
    
    .modal-content {
        margin: 0.25rem;
    }
    
    .modal-title {
        font-size: 1rem;
    }
    
    .modal-header {
        padding: 1rem 1rem 0.75rem 1rem;
    }
    
    .modal-body {
        padding: 0.85rem 1rem 1rem 1rem;
    }
}
        </style>
</body>
</html>