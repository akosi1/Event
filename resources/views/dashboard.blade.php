<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'EventAP') }} - Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('user/nav/css/navbar.css') }}" rel="stylesheet">
        
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* Video Background */
        .video-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
            object-fit: cover;
        }

        /* Video Fallback */
        .video-fallback {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #6b46c1 0%, #9333ea 50%, #c026d3 100%);
            z-index: -3;
        }

        /* Overlay for better text readability */
        .video-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(107, 70, 193, 0.3);
            z-index: -1;
        }

        /* Navigation Styles */
        .navigation-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        /* Main Dashboard Container */
        .py-12 {
            padding: 3rem 0;
        }

        .max-w-7xl {
            max-width: 1280px;
        }

        .mx-auto {
            margin-left: auto;
            margin-right: auto;
        }

        .sm\:px-6 {
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }

        .lg\:px-8 {
            padding-left: 2rem;
            padding-right: 2rem;
        }

        /* Events Section */
        .events-section {
            margin-bottom: 2rem;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 300;
            letter-spacing: 2px;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .section-title i {
            color: #f59e0b;
        }

        /* Search Container */
        .search-container {
            position: relative;
            max-width: 400px;
            width: 100%;
        }

        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
            z-index: 10;
        }

        .search-input {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 2.5rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            color: white;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .search-input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .search-input:focus {
            outline: none;
            border-color: rgba(255, 255, 255, 0.4);
            background: rgba(255, 255, 255, 0.15);
        }

        /* Events Grid */
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 3rem;
        }

        .event-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 0;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .event-card:hover {
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
            transform: translateY(-4px);
            background: rgba(255, 255, 255, 0.98);
        }

        /* Event Image Container */
        .event-image-container {
            position: relative;
            width: 100%;
            height: 200px;
            background: linear-gradient(45deg, #f59e0b, #ef4444);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .event-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .no-image-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            color: rgba(255, 255, 255, 0.8);
        }

        /* Event Badges */
        .event-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255, 255, 255, 0.95);
            color: #333;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .exclusivity-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .exclusivity-badge.exclusive {
            background: rgba(239, 68, 68, 0.9);
            color: white;
        }

        .exclusivity-badge.open {
            background: rgba(34, 197, 94, 0.9);
            color: white;
        }

        /* Event Content */
        .event-content {
            padding: 25px;
        }

        .event-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 8px;
            line-height: 1.3;
        }

        .event-description {
            color: #6b7280;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .event-details {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 20px;
        }

        .event-detail-item {
            display: flex;
            align-items: center;
            color: #4b5563;
            font-size: 0.9rem;
            gap: 0.5rem;
        }

        .event-detail-item i {
            width: 20px;
            color: #6b46c1;
        }

        /* Event Footer */
        .event-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }

        .event-date-badge {
            background: linear-gradient(135deg, #6b46c1, #9333ea);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Join Event Button */
        .join-event-btn {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 50px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(34, 197, 94, 0.3);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .join-event-btn:hover {
            background: linear-gradient(135deg, #16a34a, #15803d);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(34, 197, 94, 0.4);
        }

        .join-event-btn.joined {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
        }

        .join-event-btn.joined:hover {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
        }

        /* Pagination Styles */
        .pagination-container {
            margin-top: 3rem;
        }

        .pagination-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }

        .pagination-nav {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .pagination-btn {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .pagination-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .pagination-btn.active {
            background: linear-gradient(135deg, #6b46c1, #9333ea);
            box-shadow: 0 4px 15px rgba(107, 70, 193, 0.3);
        }

        .pagination-btn.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .pagination-btn.disabled:hover {
            transform: none;
        }

        .pagination-dots {
            color: white;
            padding: 0.75rem;
        }

        .pagination-info {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            text-align: center;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            color: white;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            opacity: 0.8;
        }

        /* Toast Container */
        #toastContainer {
            position: fixed;
            top: 2rem;
            right: 2rem;
            z-index: 9999;
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .events-grid {
                grid-template-columns: 1fr;
            }
            
            .section-title {
                font-size: 2rem;
            }

            .section-header {
                flex-direction: column;
                align-items: stretch;
            }

            .search-container {
                max-width: none;
            }

            .lg\:px-8 {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }

        @media (min-width: 640px) {
            .sm\:px-6 {
                padding-left: 1.5rem;
                padding-right: 1.5rem;
            }
        }

        @media (min-width: 1024px) {
            .lg\:px-8 {
                padding-left: 2rem;
                padding-right: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Video Background -->
    <div class="video-fallback"></div>
    <video class="video-background" autoplay muted loop>
        <source src="Silky_Blue_720P_Motion_Background_Loop.mp4" type="video/mp4">
        <!-- Fallback for browsers that don't support video -->
    </video>
    <div class="video-overlay"></div>

    <!-- Include Navigation -->
    <div class="navigation-container">
        @include('layouts.navigation')
    </div>

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
                        <i class="fas fa-search search-icon"></i>
                        <form method="GET" action="{{ route('dashboard') }}">
                            @foreach(request()->except('search') as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <input type="text" 
                                   name="search" 
                                   class="search-input" 
                                   placeholder="Search events..." 
                                   value="{{ request('search') }}"
                                   onchange="this.form.submit()">
                        </form>
                    </div>
                </div>

                @if($events->count() > 0)
                    <!-- Events Grid -->
                    <div class="events-grid">
                        @foreach($events as $event)
                        <div class="event-card">
                            <div class="event-image-container">
                                @if($event->image && Storage::disk('public')->exists($event->image))
                                    <img src="{{ Storage::url($event->image) }}" 
                                         alt="{{ $event->title }}" 
                                         class="event-image">
                                @else
                                    <div class="no-image-placeholder">
                                        <i class="fas fa-image" style="font-size: 3rem; color: rgba(255, 255, 255, 0.8);"></i>
                                    </div>
                                @endif
                                <div class="event-badge">
                                    @if($event->created_at >= now()->subWeek())
                                        NEW
                                    @elseif($event->date >= now() && $event->date <= now()->addWeek())
                                        UPCOMING
                                    @elseif($event->is_recurring)
                                        RECURRING
                                    @else
                                        EVENT
                                    @endif
                                </div>
                                
                                <!-- Exclusivity Badge -->
                                @if($event->is_exclusive)
                                    <div class="exclusivity-badge exclusive">
                                        <i class="fas fa-lock"></i>
                                        EXCLUSIVE
                                    </div>
                                @else
                                    <div class="exclusivity-badge open">
                                        <i class="fas fa-globe"></i>
                                        OPEN
                                    </div>
                                @endif
                            </div>
                            <div class="event-content">
                                <h3 class="event-title">{{ $event->title }}</h3>
                                <p class="event-description">{{ Str::limit($event->description, 120) }}</p>
                                
                                <div class="event-details">
                                    <div class="event-detail-item">
                                        <i class="fas fa-calendar"></i>
                                        <span>{{ $event->date->format('F d, Y') }}</span>
                                    </div>
                                    @if($event->start_time)
                                    <div class="event-detail-item">
                                        <i class="fas fa-clock"></i>
                                        <span>{{ $event->start_time->format('g:i A') }}@if($event->end_time) - {{ $event->end_time->format('g:i A') }}@endif</span>
                                    </div>
                                    @endif
                                    <div class="event-detail-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>{{ $event->location }}</span>
                                    </div>
                                    <div class="event-detail-item">
                                        <i class="fas fa-graduation-cap"></i>
                                        <span>{{ $event->department_display }}</span>
                                    </div>
                                    @if($event->is_recurring)
                                    <div class="event-detail-item">
                                        <i class="fas fa-repeat"></i>
                                        <span>{{ $event->recurrence_display }}</span>
                                    </div>
                                    @endif
                                </div>
                                
                                <div class="event-footer">
                                    <div class="event-date-badge">{{ $event->date->format('M d') }}</div>
                                    <button class="join-event-btn {{ $event->is_joined ? 'joined' : '' }}" 
                                            data-event-id="{{ $event->id }}" 
                                            data-joined="{{ $event->is_joined ? 'true' : 'false' }}"
                                            onclick="toggleEventJoin(this)">
                                        <span class="btn-icon">
                                            @if($event->is_joined)
                                                <i class="fas fa-minus"></i>
                                            @else
                                                <i class="fas fa-plus"></i>
                                            @endif
                                        </span>
                                        <span class="btn-text">
                                            {{ $event->is_joined ? 'Leave' : 'Join' }}
                                        </span>
                                    </button>
                                </div>
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
                                            Previous
                                        </span>
                                    @else
                                        <a href="{{ $events->previousPageUrl() }}" class="pagination-btn prev-next">
                                            <i class="fas fa-chevron-left"></i>
                                            Previous
                                        </a>
                                    @endif

                                    {{-- Pagination Elements --}}
                                    @foreach ($events->getUrlRange(1, $events->lastPage()) as $page => $url)
                                        @if ($page == $events->currentPage())
                                            <span class="pagination-btn active">{{ $page }}</span>
                                        @elseif ($page == 1 || $page == $events->lastPage() || ($page >= $events->currentPage() - 2 && $page <= $events->currentPage() + 2))
                                            <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
                                        @elseif ($page == $events->currentPage() - 3 || $page == $events->currentPage() + 3)
                                            <span class="pagination-dots">...</span>
                                        @endif
                                    @endforeach

                                    {{-- Next Page Link --}}
                                    @if ($events->hasMorePages())
                                        <a href="{{ $events->nextPageUrl() }}" class="pagination-btn prev-next">
                                            Next
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    @else
                                        <span class="pagination-btn prev-next disabled">
                                            Next
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
                        <i class="fas fa-calendar-times" style="font-size: 4rem; margin-bottom: 1rem; opacity: 0.5;"></i>
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

    <!-- Toast container -->
    <div id="toastContainer"></div>

    <script src="{{ asset('user/nav/js/navbar.js') }}"></script>
    <script src="{{ asset('user/js/dashboard.js') }}"></script>

    <!-- Handle video loading error -->
    <script>
        const video = document.querySelector('.video-background');
        if (video) {
            video.addEventListener('error', function() {
                console.log('Video failed to load, using fallback background');
                this.style.display = 'none';
            });
        }

        // Placeholder for toggleEventJoin function if not already defined
        if (typeof toggleEventJoin !== 'function') {
            function toggleEventJoin(button) {
                // This function should be implemented in your dashboard.js file
                console.log('toggleEventJoin called', button);
            }
        }
    </script>
</body>
</html>