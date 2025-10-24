@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Stats Cards -->
<div class="row">
    @php
        $stats = [
            ['count' => $totalEvents, 'label' => 'Total Events', 'icon' => 'fas fa-calendar', 'color' => 'primary'],
            ['count' => $totalUsers, 'label' => 'Total Users', 'icon' => 'fas fa-users', 'color' => 'success'],
            ['count' => $totalAdmins, 'label' => 'Total Admins', 'icon' => 'fas fa-user-shield', 'color' => 'warning'],
            ['count' => $currentYear, 'label' => 'Current Year', 'icon' => 'fas fa-chart-bar', 'color' => 'info']
        ];
    @endphp
    @foreach($stats as $stat)
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3 class="card-title text-dark">{{ $stat['count'] }}</h3>
                        <p class="card-text text-dark">{{ $stat['label'] }}</p>
                    </div>
                    <div class="align-self-center">
                        <i class="{{ $stat['icon'] }} fa-2x text-{{ $stat['color'] }}"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Charts Row -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title text-dark">Events by Month ({{ $currentYear }})</h5>
            </div>
            <div class="card-body">
                <canvas id="eventsChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title text-dark">Events by Location</h5>
            </div>
            <div class="card-body">
                <canvas id="locationChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Top Event Names Chart -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card" style="min-height: 500px;">
            <div class="card-header bg-gradient-primary">
                <h5 class="card-title text-dark mb-0 d-flex align-items-center">
                    <i class="fas fa-chart-line me-2"></i>Top Event Names
                </h5>
            </div>
            <div class="card-body" style="height: 450px; padding: 30px;">
                <canvas id="eventNamesChart" style="width: 100%; height: 100%;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Events Section -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title text-dark d-flex justify-content-between align-items-center">
                    Recent Events
                    <div class="d-flex align-items-center">
                        <form method="GET" action="{{ route('admin.dashboard') }}" class="d-flex align-items-center me-3">
                            <label for="per_page" class="form-label me-2 mb-0 text-dark">Show:</label>
                            <select class="form-select form-select-sm" id="per_page" name="per_page" onchange="this.form.submit()" style="width: 80px;">
                                @foreach([5, 10, 20, 50, 100] as $option)
                                    <option value="{{ $option }}" {{ request('per_page', 5) == $option ? 'selected' : '' }}>{{ $option }}</option>
                                @endforeach
                            </select>
                        </form>
                        <input type="text" id="searchEvents" class="form-control form-control-sm me-2" placeholder="Search events..." style="width: 200px;">
                        <button class="btn btn-outline-secondary btn-sm" onclick="toggleView()">
                            <i class="fas fa-th-list" id="viewIcon"></i>
                        </button>
                    </div>
                </h5>
            </div>
            <div class="card-body">
                @if($allEvents->count() > 0)
                    <!-- Results Info -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <small class="text-muted">Showing {{ $allEvents->firstItem() }} to {{ $allEvents->lastItem() }} of {{ $allEvents->total() }} results</small>
                        <small class="text-muted">Page {{ $allEvents->currentPage() }} of {{ $allEvents->lastPage() }}</small>
                    </div>

                    <!-- Table View -->
                    <div id="tableView" class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="text-dark">Image</th>
                                    <th class="text-dark">Title</th>
                                    <th class="text-dark">Date</th>
                                    <th class="text-dark">Location</th>
                                    <th class="text-dark">Created</th>
                                    <th class="text-dark">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allEvents as $event)
                                <tr class="event-row" data-search="{{ strtolower($event->title . ' ' . $event->location . ' ' . $event->description) }}">
                                    <td>
                                        @if($event->hasImage())
                                            <img src="{{ $event->image_url }}" alt="{{ $event->title }}" class="img-thumbnail" 
                                                 style="width: 50px; height: 50px; object-fit: cover; cursor: pointer;"
                                                 onclick="showImage('{{ $event->image_url }}', '{{ $event->title }}')">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center rounded" style="width: 50px; height: 50px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong class="text-dark">{{ $event->title }}</strong><br>
                                        <small class="text-dark">{{ Str::limit($event->description, 50) }}</small>
                                    </td>
                                    <td class="text-dark">{{ $event->date->format('M d, Y') }}</td>
                                    <td class="text-dark">{{ $event->location }}</td>
                                    <td class="text-dark">{{ $event->created_at->diffForHumans() }}</td>
                                    <td>
                                        <button class="btn btn-outline-primary btn-sm" onclick="viewDetails({{ $event->id }})">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Card View - 3 Cards Per Row -->
                    <div id="cardView" class="row" style="display: none;">
                        @foreach($allEvents as $event)
                        <div class="col-lg-4 col-md-6 mb-4 event-card" data-search="{{ strtolower($event->title . ' ' . $event->location . ' ' . $event->description) }}">
                            <div class="card h-100 shadow-sm">
                                @if($event->hasImage())
                                    <img src="{{ $event->image_url }}" class="card-img-top" alt="{{ $event->title }}" 
                                         style="height: 200px; object-fit: cover; cursor: pointer;"
                                         onclick="showImage('{{ $event->image_url }}', '{{ $event->title }}')">
                                @else
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                    </div>
                                @endif
                                <div class="card-body d-flex flex-column">
                                    <h6 class="card-title text-dark">{{ $event->title }}</h6>
                                    <p class="card-text text-muted flex-grow-1">{{ Str::limit($event->description, 80) }}</p>
                                    <div class="mt-auto">
                                        <p class="card-text mb-2">
                                            <small class="text-dark">
                                                <i class="fas fa-calendar me-1"></i>{{ $event->date->format('M d, Y') }}
                                            </small>
                                        </p>
                                        <p class="card-text mb-3">
                                            <small class="text-dark">
                                                <i class="fas fa-map-marker-alt me-1"></i>{{ $event->location }}
                                            </small>
                                        </p>
                                        <button class="btn btn-outline-primary btn-sm w-100" onclick="viewDetails({{ $event->id }})">
                                            <i class="fas fa-eye"></i> View Details
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <small class="text-muted">Showing {{ $allEvents->firstItem() }} to {{ $allEvents->lastItem() }} of {{ $allEvents->total() }} entries</small>
                        {{ $allEvents->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                        <h5 class="text-dark">No events found</h5>
                        <p class="text-muted">There are no events available at the moment</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-dark">Event Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="eventContent"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-dark" id="imageTitle">Event Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="" class="img-fluid">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Charts
    initCharts();
    
    // Search functionality
    document.getElementById('searchEvents').addEventListener('input', function() {
        const term = this.value.toLowerCase();
        const isCard = document.getElementById('cardView').style.display !== 'none';
        const items = document.querySelectorAll(isCard ? '.event-card' : '.event-row');
        
        items.forEach(item => {
            const searchText = item.dataset.search;
            item.style.display = searchText.includes(term) ? (isCard ? 'block' : 'table-row') : 'none';
        });
    });
});

function initCharts() {
    // Monthly Events Chart
    const monthlyCtx = document.getElementById('eventsChart').getContext('2d');
    const monthlyData = @json($monthlyEvents);
    new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: monthlyData.map(item => item.month),
            datasets: [{
                label: 'Number of Events',
                data: monthlyData.map(item => item.count),
                backgroundColor: 'rgba(54, 162, 235, 0.8)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
            plugins: { legend: { position: 'top' }, title: { display: true, text: 'Monthly Events Distribution' } }
        }
    });

    // Location Chart
    const locationCtx = document.getElementById('locationChart').getContext('2d');
    const locationData = @json($locationData);
    const colors = ['#FF6384', '#36A2EB', '#FFCE56', '#FF9F40', '#4BC0C0', '#9966FF', '#C9CBCF'];
    new Chart(locationCtx, {
        type: 'pie',
        data: {
            labels: locationData.map(item => item.location),
            datasets: [{
                data: locationData.map(item => item.count),
                backgroundColor: colors.slice(0, locationData.length),
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { padding: 15, usePointStyle: true } },
                title: { display: true, text: 'Events by Location' }
            }
        }
    });

    // Event Names Chart
    const eventNamesCtx = document.getElementById('eventNamesChart').getContext('2d');
    const eventNamesData = @json($eventNamesData);
    new Chart(eventNamesCtx, {
        type: 'line',
        data: {
            labels: eventNamesData.map(item => item.title.length > 25 ? item.title.substring(0, 25) + '...' : item.title),
            datasets: [{
                label: 'Event Frequency',
                data: eventNamesData.map(item => item.count),
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderWidth: 4,
                fill: true,
                tension: 0.4,
                pointRadius: 8,
                pointHoverRadius: 12
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } },
                x: { ticks: { maxRotation: 45 } }
            },
            plugins: {
                legend: { position: 'top' },
                title: { display: true, text: 'Most Popular Event Names', font: { size: 18 } }
            }
        }
    });
}

function toggleView() {
    const tableView = document.getElementById('tableView');
    const cardView = document.getElementById('cardView');
    const icon = document.getElementById('viewIcon');
    
    if (tableView.style.display === 'none') {
        tableView.style.display = 'block';
        cardView.style.display = 'none';
        icon.className = 'fas fa-th-list';
    } else {
        tableView.style.display = 'none';
        cardView.style.display = 'block';
        icon.className = 'fas fa-table';
    }
}

function viewDetails(eventId) {
    // Get event data from the current page
    const eventData = @json($allEvents->items());
    const event = eventData.find(e => e.id === eventId);
    
    if (event) {
        const modal = new bootstrap.Modal(document.getElementById('eventModal'));
        const content = document.getElementById('eventContent');
        
        let imageHtml = '';
        if (event.image_url) {
            imageHtml = `
                <div class="text-center mb-3">
                    <img src="${event.image_url}" alt="${event.title}" class="img-fluid rounded" 
                         style="max-height: 300px; cursor: pointer;" onclick="showImage('${event.image_url}', '${event.title}')">
                </div>
            `;
        }
        
        content.innerHTML = `
            ${imageHtml}
            <h4 class="text-dark">${event.title}</h4>
            <div class="row">
                <div class="col-md-6">
                    <p><strong class="text-dark">Date:</strong> <span class="text-dark">${new Date(event.date).toLocaleDateString()}</span></p>
                </div>
                <div class="col-md-6">
                    <p><strong class="text-dark">Location:</strong> <span class="text-dark">${event.location}</span></p>
                </div>
            </div>
            <div class="mt-3">
                <p><strong class="text-dark">Description:</strong></p>
                <p class="text-dark">${event.description}</p>
            </div>
        `;
        
        modal.show();
    }
}

function showImage(imageUrl, title) {
    const modal = new bootstrap.Modal(document.getElementById('imageModal'));
    document.getElementById('modalImage').src = imageUrl;
    document.getElementById('modalImage').alt = title;
    document.getElementById('imageTitle').textContent = title;
    modal.show();
}
</script>
@endpush