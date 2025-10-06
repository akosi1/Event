<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'EventAP') }} - Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- <link href="{{ asset('user/css/dashboard.css') }}" rel="stylesheet"> -->
    <link href="{{ asset('user/nav/css/navbar.css') }}" rel="stylesheet">
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
<style>
         * { 
    margin: 0; 
    padding: 0; 
    box-sizing: border-box; 
}

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
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
    padding: 2rem 0;
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

/* Events Grid - 4 Cards Per Row */
.events-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
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

/* Overlay gradient for better text readability */
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

/* Event Content - Positioned at bottom */
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

/* Location Badge */
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

/* Department/Exclusivity Badge */
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

.event-description {
    display: none;
}

.event-info-table {
    display: none;
}

/* Event Info Modal - Dark Theme */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.85);
    backdrop-filter: blur(12px);
    z-index: 9998;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.modal-content {
    background: linear-gradient(145deg, #1e293b 0%, #0f172a 100%);
    border: 1px solid rgba(139, 92, 246, 0.3);
    border-radius: 20px;
    padding: 0;
    max-width: 500px;
    width: 90%;
    max-height: 90vh;
    overflow: hidden;
    position: relative;
    box-shadow: 0 25px 70px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(139, 92, 246, 0.2);
    animation: slideUp 0.4s ease;
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

.modal-event-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
    display: block;
}

.modal-event-image-placeholder {
    width: 100%;
    height: 200px;
    background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-event-image-placeholder i {
    font-size: 4rem;
    color: rgba(255, 255, 255, 0.5);
}

.modal-header {
    padding: 1.75rem 2rem 1rem 2rem;
    position: relative;
    background: transparent;
}

.modal-header h2 {
    font-size: 1.75rem;
    font-weight: 700;
    color: white;
    margin: 0;
    padding-right: 3rem;
    line-height: 1.3;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

.modal-close {
    position: absolute;
    top: 1.5rem;
    right: 1.5rem;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.2);
    width: 40px;
    height: 40px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    z-index: 10;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.modal-close:hover {
    background: rgba(239, 68, 68, 0.9);
    border-color: rgba(239, 68, 68, 1);
    transform: scale(1.1) rotate(90deg);
}

.modal-close i {
    color: white;
    font-size: 1.1rem;
}

.modal-body {
    padding: 0 2rem 2rem 2rem;
    overflow-y: auto;
    max-height: calc(90vh - 280px);
    background: transparent;
}

.modal-body::-webkit-scrollbar {
    width: 8px;
}

.modal-body::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
}

.modal-body::-webkit-scrollbar-thumb {
    background: rgba(139, 92, 246, 0.8);
    border-radius: 10px;
}

.modal-body::-webkit-scrollbar-thumb:hover {
    background: rgba(139, 92, 246, 1);
}

.info-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1.25rem;
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.info-item:hover {
    background: rgba(139, 92, 246, 0.15);
    border-color: rgba(139, 92, 246, 0.4);
    transform: translateX(4px);
}

.info-icon {
    width: 52px;
    height: 52px;
    min-width: 52px;
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(139, 92, 246, 0.4);
}

.info-icon i {
    font-size: 1.3rem;
    color: white;
}

.info-content {
    flex: 1;
}

.info-label {
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.6);
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 0.35rem;
}

.info-text {
    font-size: 1.05rem;
    color: white;
    font-weight: 600;
    line-height: 1.5;
}

.description-box {
    margin-top: 1rem;
    padding: 1.75rem;
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border-left: 5px solid #8b5cf6;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    border: 1px solid rgba(139, 92, 246, 0.3);
}

.description-box .info-label {
    color: rgba(139, 92, 246, 1);
    margin-bottom: 0.75rem;
    font-size: 0.8rem;
}

.description-box .info-text {
    color: rgba(255, 255, 255, 0.9);
    font-weight: 500;
    line-height: 1.7;
}

/* Register Button */
.register-btn {
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

.register-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s ease;
}

.register-btn:hover::before {
    left: 100%;
}

.register-btn:hover {
    background: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 18px rgba(0, 0, 0, 0.4);
}

.register-btn:active {
    transform: translateY(0);
    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.3);
}

.register-btn.joined {
    background: rgba(239, 68, 68, 0.95);
    color: white;
    box-shadow: 0 3px 12px rgba(239, 68, 68, 0.4);
}

.register-btn.joined:hover {
    background: rgba(220, 38, 38, 0.95);
    box-shadow: 0 5px 18px rgba(239, 68, 68, 0.5);
}

.register-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}

.register-btn:disabled:hover {
    transform: none;
    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.3);
}

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

/* Footer Styles */
.site-footer {
    background: linear-gradient(145deg, #1e293b 0%, #0f172a 100%);
    border-top: 1px solid rgba(139, 92, 246, 0.3);
    padding: 3rem 0 2rem 0;
    margin-top: 5rem;
    position: relative;
}

.footer-content {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 2rem;
}

.footer-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 3rem;
    margin-bottom: 2rem;
}

.footer-section h3 {
    color: white;
    font-size: 1.2rem;
    font-weight: 700;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.footer-section p,
.footer-section a {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.9rem;
    line-height: 1.8;
    text-decoration: none;
    transition: all 0.3s ease;
}

.footer-section a:hover {
    color: #8b5cf6;
    padding-left: 0.5rem;
}

.footer-links {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.footer-bottom {
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding-top: 2rem;
    text-align: center;
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.85rem;
}

.social-links {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
}

.social-link {
    width: 40px;
    height: 40px;
    background: rgba(139, 92, 246, 0.1);
    border: 2px solid rgba(139, 92, 246, 0.3);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    transition: all 0.3s ease;
}

.social-link:hover {
    background: rgba(139, 92, 246, 0.9);
    border-color: #8b5cf6;
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(139, 92, 246, 0.4);
}

/* Custom Pagination Styles - No Container, Square Buttons */
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
    gap: 0.5rem;
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
    border-radius: 0;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    font-size: 0.9rem;
    font-weight: 600;
    text-decoration: none;
    position: relative;
    overflow: hidden;
    padding: 0 1rem;
}

.pagination-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    transition: left 0.3s ease;
    z-index: 0;
}

.pagination-btn:hover::before {
    left: 0;
}

.pagination-btn:hover {
    border-color: #8b5cf6;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(139, 92, 246, 0.5);
    text-decoration: none;
}

.pagination-btn span {
    position: relative;
    z-index: 1;
}

.pagination-btn i {
    position: relative;
    z-index: 1;
}

.pagination-btn.active {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    border-color: #8b5cf6;
    color: white;
    box-shadow: 0 4px 15px rgba(139, 92, 246, 0.5);
}

.pagination-btn.active::before {
    left: 0;
}

.pagination-btn.disabled {
    background: rgba(255, 255, 255, 0.03);
    border-color: rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.3);
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.pagination-btn.disabled:hover {
    background: rgba(255, 255, 255, 0.03);
    border-color: rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.3);
    transform: none;
    box-shadow: none;
}

.pagination-btn.disabled::before {
    display: none;
}

.pagination-btn.prev-next {
    min-width: 120px;
    gap: 0.5rem;
    padding: 0 1.5rem;
    font-weight: 700;
}

.pagination-dots {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    color: rgba(255, 255, 255, 0.4);
    font-weight: 600;
}

/* Hide default Laravel pagination */
.pagination {
    display: none !important;
}

/* Toast */
.toast {
    position: fixed;
    top: 1rem;
    right: 1rem;
    background: linear-gradient(145deg, #1e293b 0%, #0f172a 100%);
    padding: 1rem 1.5rem;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    border-left: 4px solid #48bb78;
    border: 1px solid rgba(72, 187, 120, 0.3);
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
    border-color: rgba(245, 101, 101, 0.3);
}

.toast i {
    color: #48bb78;
}

.toast.error i {
    color: #f56565;
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

/* Animation for cards */
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
    animation-play-state: paused;
}

.event-card.animate {
    animation-play-state: running;
}

/* Ripple effect */
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

/* Large Desktop (3 cards) */
@media (max-width: 1400px) {
    .events-grid { 
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
    }
    
    .event-card {
        height: 420px;
    }
}

/* Tablet (2 cards) */
@media (max-width: 1024px) {
    .events-grid { 
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }
    
    .event-card {
        height: 440px;
    }
    
    .event-title {
        font-size: 1.6rem;
    }
}

/* Mobile (1 card) */
@media (max-width: 768px) {
    .events-grid { 
        grid-template-columns: 1fr; 
        gap: 1.5rem; 
    }
    
    .section-header { 
        flex-direction: column; 
        align-items: stretch; 
    }
    
    .section-title {
        font-size: 1.5rem;
    }
    
    .search-container { 
        max-width: 100%; 
    }
    
    .event-card {
        height: 450px;
    }
    
    .event-title {
        font-size: 1.8rem;
    }
    
    .pagination-container {
        margin-top: 2rem;
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
        flex-wrap: wrap;
        justify-content: center;
        gap: 0.5rem;
    }
    
    .pagination-btn.prev-next {
        min-width: 100px;
        font-size: 0.8rem;
    }
    
    .pagination-btn {
        min-width: 40px;
        height: 40px;
        font-size: 0.8rem;
    }
    
    .toast {
        right: 0.5rem;
        left: 0.5rem;
        max-width: calc(100% - 1rem);
    }
    
    .footer-grid {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .footer-content {
        padding: 0 1rem;
    }
    
    .site-footer {
        padding: 2rem 0 1.5rem 0;
        margin-top: 3rem;
    }
}

@media (max-width: 480px) {
    .events-section {
        padding: 1rem 0;
    }
    
    .section-title {
        font-size: 1.25rem;
    }
    
    .event-card {
        height: 400px;
    }
    
    .event-title {
        font-size: 1.4rem;
    }
    
    .register-btn {
        font-size: 0.8rem;
        padding: 0.65rem 1rem;
    }
    
    .event-content {
        padding: 1.25rem;
    }
}
    </style>
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