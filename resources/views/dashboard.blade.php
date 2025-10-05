<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'EventAP') }} - Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('user/css/dashboard.css') }}" rel="stylesheet">
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
                        Latest Events
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
                        <div class="event-card">
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
                                
                                <!-- Register Button -->
                                <button class="register-btn {{ $event->is_joined ? 'joined' : '' }}" 
                                        data-event-id="{{ $event->id }}" 
                                        data-joined="{{ $event->is_joined ? 'true' : 'false' }}"
                                        onclick="toggleEventJoin(this)">
                                    <span class="btn-text">
                                        {{ $event->is_joined ? 'Leave Event' : 'Register Now' }}
                                    </span>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <!-- Custom Pagination -->
                    @if($events->hasPages())
                        <div class="pagination-container">
                            <div class="pagination-wrapper">
                                <div class="pagination-nav">
                                    {{-- Previous Page Link --}}
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

                                    {{-- Pagination Elements --}}
                                    @foreach ($events->getUrlRange(1, $events->lastPage()) as $page => $url)
                                        @if ($page == $events->currentPage())
                                            <span class="pagination-btn active"><span>{{ $page }}</span></span>
                                        @elseif ($page == 1 || $page == $events->lastPage() || ($page >= $events->currentPage() - 2 && $page <= $events->currentPage() + 2))
                                            <a href="{{ $url }}" class="pagination-btn"><span>{{ $page }}</span></a>
                                        @elseif ($page == $events->currentPage() - 3 || $page == $events->currentPage() + 3)
                                            <span class="pagination-dots">...</span>
                                        @endif
                                    @endforeach

                                    {{-- Next Page Link --}}
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
                    
                    <!-- Hide default Laravel pagination -->
                    <div style="display: none;">
                        {{ $events->appends(request()->query())->links() }}
                    </div>
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

    <!-- Event Info Modal (Hidden by default) -->
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

    <!-- Footer -->
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
                        <p><i class="fas fa-map-pin"></i>Bunakan, Madridejos Community College</p>
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
    
    <!-- Dashboard JavaScript -->
    <script src="{{ asset('user/js/dashboard.js') }}"></script>
    
    <script>
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