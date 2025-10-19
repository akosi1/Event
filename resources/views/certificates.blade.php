<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'EventAP') }} - Certificates</title>
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

        /* certificated styles */
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
                        <i class="fas fa-medal"></i>
                        All Certificates
                    </h2>
                </div>

                @if(count($certificates) > 0)
                    <!-- Certificates Grid -->
                   <div class="certificates-grid">
                        @foreach($certificates as $certificate)
                            <div class="certificate-card">
                                <div class="certificate-image-container">
                                    <img src="{{ asset('storage/' . $certificate->certificate_path) }}"
                                        alt="Certificate for {{ e($certificate->event->title ?? 'Event') }}"
                                        class="certificate-image"
                                        onerror="this.onerror=null; this.src='{{ asset('images/default-event.jpg') }}';">
                                </div>

                                <div class="certificate-content">
                                    <h3 class="certificate-event-title">
                                        {{ e($certificate->event->title ?? 'Event Title Unavailable') }}
                                    </h3>

                                    <p class="certificate-date">
                                        Generated on: {{ $certificate->created_at->format('F d, Y') }}
                                    </p>

                                    <a href="{{ asset('storage/' . $certificate->certificate_path) }}"
                                    download
                                    class="download-btn">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="empty-state">
                        <i class="fas fa-calendar-times"></i>
                        <h3>No Certificated available yet</h3>
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
            images.forEach(img => {
                img.addEventListener('error', function() {
                    const placeholder = document.createElement('div');
                    placeholder.className = 'no-image-placeholder';
                    placeholder.innerHTML = '<i class="fas fa-calendar-alt"></i>';
                    this.parentElement.replaceChild(placeholder, this);
                });
            });
        });
    </script>
</body>
</html>
