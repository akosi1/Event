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

    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        /* Ensure SweetAlert stays below navigation */
        .swal2-container {
            z-index: 9998 !important;
        }

        /* If your nav has z-index, make sure it's higher */
        nav, .navbar {
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

        .swal2-confirm, .swal2-cancel {
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

        /* styles for flex button (leave and generate cert) */
        .flex-container{
            width: 100%;
            display: flex;
            gap: 1rem;
            align-items: center
        }
        .custom-btn{
            flex: 1;
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
            text-transform: capitalize;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
        }
        .leave-btn{
            background: rgba(239, 68, 68, 0.95);
            color: white;
            box-shadow: 0 3px 12px rgba(239, 68, 68, 0.4);
        }
        .leave-btn::hover{
            background: rgba(220, 38, 38, 0.95);
            box-shadow: 0 5px 18px rgba(239, 68, 68, 0.5);
        }
        .generate-btn{
            background: rgba(34, 197, 94, 0.95);;
        }
        .generate-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        /* certificate modal styles */
        .certificates-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.5rem;
        }

        .certificate-card {
            background: #fff;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .certificate-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 18px rgba(0,0,0,0.12);
        }

        .certificate-image-container {
            position: relative;
            width: 100%;
            height: 220px;
            overflow: hidden;
            background: #f9fafb;
        }

        .certificate-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .certificate-content {
            padding: 1rem 1.2rem;
            text-align: center;
        }

        .certificate-event-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.4rem;
        }

        .certificate-date {
            font-size: 0.9rem;
            color: #6b7280;
            margin-bottom: 0.8rem;
        }

        .download-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: #10b981;
            color: white;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background 0.2s;
        }

        .download-btn:hover {
            background: #059669;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #6b7280;
        }

        .empty-state i {
            font-size: 3rem;
            color: #d1d5db;
            margin-bottom: 0.5rem;
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
            50% { opacity: 0.4; }
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
                            @foreach(request()->except('search') as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <input type="text"
                                   name="search"
                                   class="search-input"
                                   placeholder="Search events..."
                                   value="{{ e(request('search')) }}"
                                   maxlength="100"
                                   autocomplete="off">
                        </form>
                    </div>
                </div>

                @if($events->count() > 0)
                    <!-- Events Grid -->
                    <div class="events-grid">
                        @foreach($events as $event)
                        <div class="time-left-badge" id="time-left-{{ $event->id }}">
                                <!-- Timer will update here -->
                        </div>

                        <div class="event-card" data-end-date="{{ \Carbon\Carbon::parse($event->date)->format('Y-m-d') }}" data-end-time="{{ \Carbon\Carbon::parse($event->end_time)->format('H:i:s') }}">
                            <!-- Background Image -->
                            <div class="event-image-container">
                                @if($event->hasImage())
                                    <img src="{{ $event->image_url }}"
                                         alt="{{ e($event->title) }}"
                                         class="event-image"
                                         onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'no-image-placeholder\'><i class=\'fas fa-calendar-alt\'></i></div>';">
                                @else
                                    <div class="no-image-placeholder">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                @endif
                            </div>

                            <!-- Status Badge (Top Left) -->
                            <div class="event-badge {{ $event->created_at >= now()->subWeek() ? 'new' : ($event->date >= now() && $event->date <= now()->addWeek() ? 'upcoming' : 'event') }}">
                                @if($event->created_at >= now()->subWeek())
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
                            <button class="info-btn"
                                    onclick="showEventInfo({{ $event->id }})"
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
                            @if($event->is_exclusive)
                                <div class="exclusivity-badge exclusive">
                                    <i class="fas fa-graduation-cap"></i>
                                    {{ e($event->department ?? 'BSIT') }}
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

                                @if($event->is_joined)
                                    <div class="flex-container">
                                        @php
                                            // Check if certificate exists for this event and user
                                            $certificate = $certificates->firstWhere('event_id', $event->id);
                                        @endphp
                                        <button class="custom-btn leave-btn {{ $event->is_joined ? 'joined' : '' }}"
                                            data-event-id="{{ $event->id }}"
                                            data-event-title="{{ e($event->title) }}"
                                            data-joined="{{ $event->is_joined ? 'true' : 'false' }}"
                                            onclick="toggleEventJoin(this)">
                                            <span class="btn-text">
                                                Leave Event
                                            </span>
                                        </button>
                                        <button class="custom-btn generate-btn {{ $event->is_joined ? 'joined' : '' }}"
                                            data-event-id="{{ $event->id }}"
                                            data-event-title="{{ e($event->title) }}"
                                            data-joined="{{ $event->is_joined ? 'true' : 'false' }}"
                                            @if($certificate)
                                                data-certificate-path="{{ asset('storage/' . $certificate->certificate_path) }}"
                                                data-event-title="{{ e($certificate->event->title ?? 'Event Title Unavailable') }}"
                                                data-certificate-date="{{ $certificate->created_at->format('F d, Y') }}"
                                                {{-- disabled = "{{$event->hasEnded && $event->certificate_template_image}}" --}}
                                                onclick="showCertificateInfo({{$certificate->event_id}})"
                                            @else
                                                onclick="toggleGenerateCertificate(this)"
                                            @endif>
                                                <span class="btn-text">
                                                    {{ $certificate ? 'View Certificate' : 'Generate Certificate' }}
                                                </span>
                                        </button>
                                    </div>

                                @else
                                    <button class="register-btn"
                                            data-event-id="{{ $event->id }}"
                                            data-event-title="{{ e($event->title) }}"
                                            onclick="toggleEventJoin(this)">
                                        <span class="btn-text">
                                            Join Now <span class="time-left-badge"></span>
                                        </span>
                                    </button>
                                @endif


                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Pagination (unchanged) -->
                    @if($events->hasPages())
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
                    <!-- Empty State -->
                    <div class="empty-state">
                        <i class="fas fa-calendar-times"></i>
                        <h3>No events available</h3>
                        <p>
                            @if(request('department') || request('search'))
                                No events match your current filters. Try adjusting your search criteria.
                            @else
                                There are no events available for your department at the moment. Please check back later.
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Include Navigation Script -->
    <script src="{{ asset('user/nav/js/navbar.js') }}"></script>

    <!-- Toast container -->
    <div id="toastContainer"></div>

    <!-- Event Info Modal -->
    <div id="eventInfoModal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Event Title</h2>
                <button class="modal-close" onclick="closeEventInfo()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" id="eventInfoContent">
                <!-- Dynamic content will be inserted here -->
            </div>
        </div>
    </div>

    <!-- Footer (unchanged) -->
    <footer class="site-footer">
        <div class="footer-content">
            <div class="footer-grid">
                <div class="footer-section">
                    <h3><i class="fas fa-calendar-alt"></i> MCC E&PO</h3>
                    <p>Your premier platform for discovering and managing university events. Stay connected with your campus community.</p>
                    <div class="social-links">
                        <a href="#" class="social-link" title="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-link" title="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="social-link" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="social-link" title="LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>

                <div class="footer-section">
                    <h3><i class="fas fa-link"></i> Quick Links</h3>
                    <div class="footer-links">
                        <a href="{{ route('dashboard') }}">
                            <i class="fas fa-home"></i> Home
                        </a>
                        <a href="#">
                            <i class="fas fa-calendar-check"></i> My Events
                        </a>
                        <a href="#">
                            <i class="fas fa-graduation-cap"></i> Departments
                        </a>
                        <a href="#">
                            <i class="fas fa-info-circle"></i> About Us
                        </a>
                    </div>
                </div>

                <div class="footer-section">
                    <h3><i class="fas fa-question-circle"></i> Support</h3>
                    <div class="footer-links">
                        <a href="#">
                            <i class="fas fa-life-ring"></i> Help Center
                        </a>
                        <a href="#">
                            <i class="fas fa-file-alt"></i> Terms of Service
                        </a>
                        <a href="#">
                            <i class="fas fa-shield-alt"></i> Privacy Policy
                        </a>
                        <a href="#">
                            <i class="fas fa-envelope"></i> Contact Us
                        </a>
                    </div>
                </div>

                <div class="footer-section">
                    <h3><i class="fas fa-map-marker-alt"></i> Contact Info</h3>
                    <div class="footer-links">
                        <p><i class="fas fa-map-pin"></i> Bunakan, Madridejos Community College</p>
                        <p><i class="fas fa-phone"></i> +63 XXX XXX XXXX</p>
                        <p><i class="fas fa-envelope"></i> events-org.com</p>
                        <p><i class="fas fa-clock"></i> Mon - Fri: 8:00 AM - 5:00 PM</p>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} MCC E&PO. All rights reserved. | Designed with <i class="fas fa-heart" style="color: #ef4444;"></i> for students</p>
            </div>
        </div>
    </footer>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.all.min.js"></script>

    <!-- Dashboard JavaScript -->
    <script src="{{ asset('user/js/dashboard.js') }}"></script>

    <script>
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
                reverseButtons: true,
                customClass: {
                    popup: 'swal-popup-custom',
                    confirmButton: 'swal-confirm-custom',
                    cancelButton: 'swal-cancel-custom'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // User confirmed, proceed with the action
                    processEventAction(button, eventId, action, eventTitle);
                }
            });
        }

        function toggleGenerateCertificate(button) {
            const eventId = button.getAttribute('data-event-id');

            // Show confirmation dialog
            Swal.fire({
                title: 'Generate Certificate',
                html: `Are you sure you want to generate Certificate?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No',
                confirmButtonColor: '#10b981',
                // confirmButtonColor: isJoined ? '#ef4444' : '#10b981',
                cancelButtonColor: '#6b7280',
                reverseButtons: true,
                customClass: {
                    popup: 'swal-popup-custom',
                    confirmButton: 'swal-confirm-custom',
                    cancelButton: 'swal-cancel-custom'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    processCertificateGeneration(button, eventId)
                }
            });
        }

        // Process certificate generation
        function processCertificateGeneration(button, eventId) {
            const url = `/events/${eventId}/generate-certificate`;

            // Disable button during request
            button.disabled = true;
            button.style.opacity = '0.6';

            // Use DELETE method for leave, POST for join
            const method = 'POST';

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
                    // Show success message
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
                    // Show error message
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
                // Re-enable button
                button.disabled = false;
                button.style.opacity = '1';
            });
        }

        // Process the actual join/leave action
        function processEventAction(button, eventId, action, eventTitle) {
            const url = `/events/${eventId}/${action}`;

            // Disable button during request
            button.disabled = true;
            button.style.opacity = '0.6';

            // Use DELETE method for leave, POST for join
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
                    // Update button state
                    const isNowJoined = action === 'join';
                    button.setAttribute('data-joined', isNowJoined);
                    button.querySelector('.btn-text').textContent = isNowJoined ? 'Leave Event' : 'Join Now';

                    if (isNowJoined) {
                        button.classList.add('joined');
                    } else {
                        button.classList.remove('joined');
                    }

                    // Show success message
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
                    // Show error message
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
                // Re-enable button
                button.disabled = false;
                button.style.opacity = '1';
            });
        }

        // Enhanced image error handling
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

                const endDateTime = new Date(`${endDate.split(' ')[0]}T${endTime}`);

                function updateTimer() {
                    const now = new Date();
                    const diff = endDateTime - now;

                    if (diff <= 0) {
                        return;
                    }

                    icon.classList.add('blinking');

                    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                    const hours = Math.floor(diff / (1000 * 60 * 60));
                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));


                    if (days > 0) {
                        timeLeftBadge.textContent = `${days}d ${hours}h ${minutes}m left`;
                    } else if (hours > 0) {
                        timeLeftBadge.textContent = `${hours}h ${minutes}m left`;
                    } else {
                        timeLeftBadge.textContent = `${minutes}m left`;
                    }
                }

                // initial render
                updateTimer();
                setInterval(updateTimer, 60000);
            });


        });
    </script>
</body>
</html>
