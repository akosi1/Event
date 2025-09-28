<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'EventAP') }} - Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('user/nav/css/navbar.css') }}" rel="stylesheet">
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
                                        <i class="fas fa-image" style="font-size: 3rem; color: #a0aec0;"></i>
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
                                        <i class="fas fa-calendar" style="width: 16px; color: #667eea;"></i>
                                        <span>{{ $event->date->format('F d, Y') }}</span>
                                    </div>
                                    @if($event->start_time)
                                    <div class="event-detail-item">
                                        <i class="fas fa-clock" style="width: 16px; color: #667eea;"></i>
                                        <span>{{ $event->start_time->format('g:i A') }}@if($event->end_time) - {{ $event->end_time->format('g:i A') }}@endif</span>
                                    </div>
                                    @endif
                                    <div class="event-detail-item">
                                        <i class="fas fa-map-marker-alt" style="width: 16px; color: #667eea;"></i>
                                        <span>{{ $event->location }}</span>
                                    </div>
                                    <div class="event-detail-item">
                                        <i class="fas fa-graduation-cap" style="width: 16px; color: #667eea;"></i>
                                        <span>{{ $event->department_display }}</span>
                                    </div>
                                    @if($event->is_recurring)
                                    <div class="event-detail-item">
                                        <i class="fas fa-repeat" style="width: 16px; color: #667eea;"></i>
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
                                            {{ $event->is_joined ? 'Leave Event' : 'Join Event' }}
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
                        <h3 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 0.5rem;">No events available</h3>
                        <p style="opacity: 0.8;">
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
</body>
</html>