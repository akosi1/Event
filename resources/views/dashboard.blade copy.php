<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User Dashboard') }}
        </h2>
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </x-slot>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: #2d3748;
            line-height: 1.6;
            min-height: 100vh;
        }

        /* Events Section */
        .events-section {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .section-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2d3748;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        /* Search Input */
        .search-container {
            position: relative;
            max-width: 400px;
        }

        .search-input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 3rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: white;
        }

        .search-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
        }

        /* Events Grid */
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
        }

        .event-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .event-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .event-image-container {
            position: relative;
            width: 100%;
            height: 200px;
            overflow: hidden;
            background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
        }

        .event-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .event-image:hover {
            transform: scale(1.05);
        }

        .no-image-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e0 100%);
        }

        .no-image-icon {
            font-size: 3rem;
            color: #a0aec0;
        }

        .event-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(102, 126, 234, 0.9);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }

        .event-content {
            padding: 1.5rem;
        }

        .event-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }

        .event-description {
            color: #4a5568;
            font-size: 0.95rem;
            margin-bottom: 1rem;
            line-height: 1.5;
        }

        .event-details {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .event-detail-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #4a5568;
            font-size: 0.9rem;
        }

        .event-detail-icon {
            width: 16px;
            color: #667eea;
            flex-shrink: 0;
        }

        .event-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid #e2e8f0;
            gap: 1rem;
        }

        .event-date-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .join-event-btn {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: white;
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 10px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            text-decoration: none;
        }

        .join-event-btn:hover {
            background: linear-gradient(135deg, #38a169 0%, #2f855a 100%);
            transform: translateY(-1px);
            color: white;
        }

        .join-event-btn.joined {
            background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
        }

        .join-event-btn.joined:hover {
            background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
        }

        .join-event-btn:disabled {
            background: #a0aec0;
            cursor: not-allowed;
            opacity: 0.6;
        }

        /* Loading spinner */
        .spinner {
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Toast notifications */
        .toast {
            position: fixed;
            top: 1rem;
            right: 1rem;
            background: white;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #48bb78;
            z-index: 1000;
            transform: translateX(400px);
            transition: transform 0.3s ease;
        }

        .toast.show {
            transform: translateX(0);
        }

        .toast.error {
            border-left-color: #f56565;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #4a5568;
        }

        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .empty-state-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .empty-state-description {
            opacity: 0.8;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .events-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .section-header {
                flex-direction: column;
                align-items: stretch;
            }

            .search-container {
                max-width: 100%;
            }

            .event-footer {
                flex-direction: column;
                align-items: stretch;
                gap: 0.5rem;
            }
        }

        /* Search Results Info */
        .search-results-info {
            background: #48bb78;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-top: 1rem;
        }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Events Section -->
            <div class="events-section slide-up">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-fire"></i>
                        Latest Events
                    </h2>
                    
                    <div class="search-container">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" id="searchEvents" class="search-input" placeholder="Search events..." autocomplete="off">
                    </div>
                </div>

                <!-- Search Results Info -->
                <div id="searchResultsInfo" class="search-results-info" style="display: none;">
                    <span id="searchResultsCount">0</span> events found
                </div>

                @if(isset($events) && $events->count() > 0)
                    <!-- Events Grid -->
                    <div class="events-grid" style="margin-top: 2rem;">
                        @foreach($events as $event)
                        <div class="event-card" data-search="{{ strtolower($event->title . ' ' . $event->location . ' ' . $event->description) }}">
                            <div class="event-image-container">
                                @if($event->image && Storage::disk('public')->exists($event->image))
                                    <img src="{{ Storage::url($event->image) }}" 
                                         alt="{{ $event->title }}" 
                                         class="event-image">
                                @else
                                    <div class="no-image-placeholder">
                                        <i class="fas fa-image no-image-icon"></i>
                                    </div>
                                @endif
                                <div class="event-badge">
                                    @if($event->created_at >= now()->subWeek())
                                        NEW
                                    @elseif($event->date >= now() && $event->date <= now()->addWeek())
                                        UPCOMING
                                    @else
                                        EVENT
                                    @endif
                                </div>
                            </div>
                            <div class="event-content">
                                <h3 class="event-title">{{ $event->title }}</h3>
                                <p class="event-description">{{ Str::limit($event->description, 120) }}</p>
                                
                                <div class="event-details">
                                    <div class="event-detail-item">
                                        <i class="fas fa-calendar event-detail-icon"></i>
                                        <span>{{ $event->date->format('F d, Y') }}</span>
                                    </div>
                                    <div class="event-detail-item">
                                        <i class="fas fa-map-marker-alt event-detail-icon"></i>
                                        <span>{{ $event->location }}</span>
                                    </div>
                                    @if($event->department)
                                    <div class="event-detail-item">
                                        <i class="fas fa-graduation-cap event-detail-icon"></i>
                                        <span>{{ $event->department }} Department</span>
                                    </div>
                                    @endif
                                </div>
                                
                                <div class="event-footer">
                                    <div class="event-date-badge">{{ $event->date->format('d M') }}</div>
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
                    
                    <!-- Pagination -->
                    <div style="margin-top: 2rem; display: flex; justify-content: center;">
                        {{ $events->appends(request()->query())->links() }}
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="empty-state">
                        <i class="fas fa-calendar-times empty-state-icon"></i>
                        <h3 class="empty-state-title">No events available</h3>
                        <p class="empty-state-description">There are no events to display at the moment. Please check back later.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Toast container -->
    <div id="toastContainer"></div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <script>
        // CSRF token setup
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Search functionality
        let searchTimeout;
        const searchInput = document.getElementById('searchEvents');
        const searchResultsInfo = document.getElementById('searchResultsInfo');
        const searchResultsCount = document.getElementById('searchResultsCount');
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const searchTerm = this.value.toLowerCase().trim();
            
            // Debounce search
            searchTimeout = setTimeout(() => {
                performSearch(searchTerm);
            }, 300);
        });
        
        function performSearch(searchTerm) {
            const eventCards = document.querySelectorAll('.event-card');
            let visibleCount = 0;
            
            eventCards.forEach(card => {
                const searchData = card.getAttribute('data-search');
                if (!searchTerm || searchData.includes(searchTerm)) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Update search results info
            if (searchTerm) {
                searchResultsCount.textContent = visibleCount;
                searchResultsInfo.style.display = 'block';
            } else {
                searchResultsInfo.style.display = 'none';
            }
        }

        // Toast notification system
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                    <span>${message}</span>
                </div>
            `;
            
            const container = document.getElementById('toastContainer');
            container.appendChild(toast);
            
            // Show toast
            setTimeout(() => toast.classList.add('show'), 100);
            
            // Hide and remove toast
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => container.removeChild(toast), 300);
            }, 3000);
        }

        // Toggle event join/leave functionality
        async function toggleEventJoin(button) {
            const eventId = button.getAttribute('data-event-id');
            const isJoined = button.getAttribute('data-joined') === 'true';
            const btnIcon = button.querySelector('.btn-icon i');
            const btnText = button.querySelector('.btn-text');
            
            // Disable button and show loading
            button.disabled = true;
            btnIcon.className = 'spinner';
            btnText.textContent = isJoined ? 'Leaving...' : 'Joining...';
            
            try {
                const url = `/events/${eventId}/${isJoined ? 'leave' : 'join'}`;
                const method = isJoined ? 'DELETE' : 'POST';
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Update button state
                    const newJoinedState = !isJoined;
                    button.setAttribute('data-joined', newJoinedState ? 'true' : 'false');
                    button.className = `join-event-btn ${newJoinedState ? 'joined' : ''}`;
                    
                    // Update icon and text
                    btnIcon.className = newJoinedState ? 'fas fa-minus' : 'fas fa-plus';
                    btnText.textContent = newJoinedState ? 'Leave Event' : 'Join Event';
                    
                    // Show success message
                    showToast(data.message, 'success');
                } else {
                    // Show error message
                    showToast(data.message, 'error');
                    
                    // Reset button state
                    btnIcon.className = isJoined ? 'fas fa-minus' : 'fas fa-plus';
                    btnText.textContent = isJoined ? 'Leave Event' : 'Join Event';
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('An error occurred. Please try again.', 'error');
                
                // Reset button state
                btnIcon.className = isJoined ? 'fas fa-minus' : 'fas fa-plus';
                btnText.textContent = isJoined ? 'Leave Event' : 'Join Event';
            } finally {
                // Re-enable button
                button.disabled = false;
            }
        }

        // Add keyboard shortcut for search
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                searchInput.focus();
            }
        });
    </script>
</x-app-layout>