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

        /* styles for flex button (leave and generate cert) */
        .flex-container {
            width: 100%;
            display: flex;
            gap: 1rem;
            align-items: center
        }


        .custom-btn {
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

        .leave-btn {
            background: rgba(239, 68, 68, 0.95);
            color: white;
            box-shadow: 0 3px 12px rgba(239, 68, 68, 0.4);
        }

        .leave-btn::hover {
            background: rgba(220, 38, 38, 0.95);
            box-shadow: 0 5px 18px rgba(239, 68, 68, 0.5);
        }

        .generate-btn {
            background: rgba(34, 197, 94, 0.95);
            ;
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
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .certificate-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.12);
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

        .pending {
            background: rgba(251, 191, 36, 0.95);
            /* yellow */
            color: #1e293b;
            cursor: not-allowed;
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

        /* opacity: 0.4; */
        /* ===========================
       FEEDBACK FEATURE STYLES
       =========================== */

        /* Feedback button under each event */
        .feedback-btn {
            background-color: #2e2f54ff;
            color: #fff;
            transition: background-color 0.2s ease, transform 0.1s ease;
        }

        .feedback-btn:hover {
            background-color: #2563eb;
            transform: translateY(-2px);
        }

        /* Feedback modal overlay */
        #feedbackModal.modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 10000 !important;
        }

        /* Modal box */
        #feedbackModal .modal-content {
            background: rgba(37, 3, 92, 1);
            border-radius: 12px;
            max-width: 500px;
            width: 90%;
            padding: 1.5rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            animation: fadeInScale 0.2s ease-in-out;
        }

        /* Header */
        #feedbackModal .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        #feedbackModal .modal-close {
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            color: #555;
        }

        #feedbackModal .modal-close:hover {
            color: #2e084dff;
        }

        /* Stars */
        #feedbackModal .stars {
            display: flex;
            gap: 5px;
            margin-top: 0.5rem;
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

        /* Textarea */
        #feedbackModal textarea {
            width: 100%;
            border-radius: 6px;
            padding: 0.5rem;
            margin-top: 0.5rem;
            border: 1px solid rgba(114, 22, 22, 1);
            resize: vertical;
            font-family: inherit;
            font-size: 0.95rem;
        }

        /* Feedback modal animation */
        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
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
                                <button class="info-btn" onclick="showEventInfo({{ $event->id }})"
                                    title="Event Details" data-event-title="{{ e($event->title) }}"
                                    data-event-location="{{ e($event->location) }}"
                                    data-event-date="{{ $event->date->format('F d, Y') }}"
                                    data-event-time="{{ e($event->start_time ? $event->start_time->format('g:i A') : 'TBA') }}"
                                    data-event-department="{{ e($event->department_display) }}"
                                    data-event-description="{{ e($event->description ?? 'No description available.') }}"
                                    data-event-image="{{ $event->image }}"
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
                                            <button class=" custom-btn feedback-btn"
                                                onclick="openFeedbackModal({{ $event->id }}, '{{ e($event->title) }}')">
                                                <i class="fas fa-comment-dots"></i> Feedback
                                            </button>

                                            @php
                                                $certificate = $certificates->firstWhere('event_id', $event->id);
                                            @endphp

                                            <button class="custom-btn generate-btn"
                                                @if ($certificate)
                                                    data-certificate-path="{{ $certificate->certificate_path }}"
                                                    data-event-title={{$certificate->event->title}}
                                                    data-certificate-date={{$certificate->created_at}}
                                                    onclick="showCertificateInfo({{ $certificate->event_id }})"
                                                @else
                                                    data-event-id="{{$event->id}}"
                                                    onclick="toggleGenerateCertificate(this)"
                                                @endif
                                                title="{{ (!$event->hasEnded || !$event->certificate_template_image)
                                                ? 'The Event is not yet end or no Certificate template'
                                                : '' }}"
                                                @disabled(!$event->hasEnded || !$event->certificate_template_image)>
                                                <span class="btn-text">
                                                    {{$certificate && $event->hasEnded ? 'View' : 'Certificate'}}
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

                    <!-- Pagination (unchanged) -->
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
                @csrf
                <input type="hidden" id="feedbackEventId" name="event_id">
                <label for="feedbackEmail" style="font-weight:600;display:block;margin-bottom:6px;">Your Email</label>
                <input id="feedbackEmail" name="email" type="email"
                       placeholder="you@example.com"
                       style="width:100%;padding:8px;border-radius:6px;border:1px solid #ddd;margin-bottom:0.75rem;"
                       required>
                <label for="feedbackMessage" style="font-weight:600;display:block;margin-bottom:6px;margin-top:6px;">Feedback</label>
                <textarea id="feedbackMessage" name="feedback" placeholder="Share your thoughts..." required
                          style="width:100%;border-radius:6px;padding:8px;border:1px solid #ddd;min-height:100px;"></textarea>

                <div class="rating" style="margin-top:0.75rem;">
                    <label style="display:block;font-weight:600;margin-bottom:6px;">Rate the event:</label>
                    <div class="stars" style="display:flex;gap:8px;">
                        <i class="fas fa-star" data-value="1" style="cursor:pointer;"></i>
                        <i class="fas fa-star" data-value="2" style="cursor:pointer;"></i>
                        <i class="fas fa-star" data-value="3" style="cursor:pointer;"></i>
                        <i class="fas fa-star" data-value="4" style="cursor:pointer;"></i>
                        <i class="fas fa-star" data-value="5" style="cursor:pointer;"></i>
                    </div>
                </div>

                <div id="feedbackResponse" class="feedback-response" style="text-align:center;margin-top:12px;display:none;font-weight:600;"></div>
                <div style="display:flex;gap:0.5rem;margin-top:1rem;">
                    <button type="button" class="custom-btn" style="flex:1;background:#ef4444;color:#fff" onclick="closeFeedbackModal()">Cancel</button>
                    <button type="submit" id="submitFeedbackBtn" class="custom-btn" style="flex:1;background:#10b981;color:#fff">Submit Feedback</button>
                </div>
            </form>
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
@include('layouts.footer')
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.all.min.js"></script>

    <!-- Dashboard JavaScript -->
    <script src="{{ asset('user/js/dashboard.js') }}"></script>

    <script>
       function openFeedbackModal(eventId, eventTitle) {
        document.getElementById('feedbackModal').style.display = 'flex';
        document.getElementById('feedbackEventId').value = eventId;
        document.getElementById('feedbackMessage').value = '';
        document.getElementById('feedbackEmail').value = '{{ auth()->user()->email ?? "" }}'; 
        document.querySelectorAll('#feedbackModal .stars .fa-star').forEach(s => s.classList.remove('selected'));
        hideFeedbackResponse();
    }

    function closeFeedbackModal() {
        document.getElementById('feedbackModal').style.display = 'none';
        hideFeedbackResponse();
    }
    
    document.querySelectorAll('#feedbackModal .stars .fa-star').forEach(star => {
        star.addEventListener('click', function () {
            const value = parseInt(this.dataset.value, 10);
            document.querySelectorAll('#feedbackModal .stars .fa-star').forEach(s => {
                s.classList.toggle('selected', parseInt(s.dataset.value, 10) <= value);
            });
        });
    });
   
    function showFeedbackResponse(type, text) {
        const el = document.getElementById('feedbackResponse');
        el.style.display = 'block';
        el.style.color = (type === 'success') ? '#10b981' : '#ef4444';
        el.textContent = text;
    }
    function hideFeedbackResponse() {
        const el = document.getElementById('feedbackResponse');
        el.style.display = 'none';
        el.textContent = '';
    }
    document.getElementById('feedbackForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        hideFeedbackResponse();

        const submitBtn = document.getElementById('submitFeedbackBtn');
        const originalBtnText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Sending...';

        const eventId = document.getElementById('feedbackEventId').value;
        const email = document.getElementById('feedbackEmail').value.trim();
        const feedback = document.getElementById('feedbackMessage').value.trim();
        const rating = document.querySelectorAll('#feedbackModal .stars .selected').length;

        if (!email || !feedback) {
            showFeedbackResponse('error', 'Please provide both your email and feedback.');
            submitBtn.disabled = false;
            submitBtn.textContent = originalBtnText;
            return;
        }

        try {
            const res = await fetch(`/events/${eventId}/feedback`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email, feedback, rating })
            });

            const contentType = res.headers.get('content-type') || '';
            let data = null;
            if (contentType.includes('application/json')) {
                data = await res.json();
            } else {
                const text = await res.text();
                throw new Error('Unexpected server response: ' + text.slice(0, 300));
            }

            if (res.ok) {
                showFeedbackResponse('success', data.message || 'Thank you — feedback submitted!');
                document.getElementById('feedbackForm').reset();
                document.querySelectorAll('#feedbackModal .stars .fa-star').forEach(s => s.classList.remove('selected'));
            
                setTimeout(() => {
                    closeFeedbackModal();
                }, 1200);
            } else {
                const message = data.message || (data.errors ? Object.values(data.errors).flat().join(' ') : 'Submission failed.');
                showFeedbackResponse('error', message);
            }
        } catch (err) {
            console.error('Feedback submit error:', err);
            showFeedbackResponse('error', 'Unable to send feedback. Please try again later.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalBtnText;
        }
    });

    //end of the feedback feature
        function toggleEventJoin(button) {
            const eventId = button.getAttribute('data-event-id');
            const isJoined = button.getAttribute('data-joined') === 'true';
            const eventTitle = button.getAttribute('data-event-title');
            const action = isJoined ? 'leave' : 'join';
          
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
                        }).then(() => {
                            location.reload();
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
                        button.querySelector('.btn-text').textContent = isNowJoined ? 'Pending Approval' : 'Join Now';

                        if (isNowJoined) {
                            button.classList.add('pending');
                        } else {
                            button.classList.remove('pending');
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
                console.log(`end date time${endDateTime}`)

                function updateTimer() {
                    const now = new Date();
                    const diff = endDateTime - now;
                    console.log(diff)

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

                // initial render
                updateTimer();
                setInterval(updateTimer, 60000);
            });


        });
    </script>
</body>

</html>
