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
            ['count' => $eventJoinsStatusData['pending'] + $eventJoinsStatusData['approved'], 'label' => 'Total Joins', 'icon' => 'fas fa-user-check', 'color' => 'info']
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

<!-- Charts Row 1: Event Joins Status & Events by Month (Line Chart) -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="card-title text-dark mb-0">
                    <i class="fas fa-user-check me-2"></i>Event participants Status
                </h5>
            </div>
            <div class="card-body" style="height: 400px;">
                <canvas id="eventJoinsStatusChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="card-title text-dark mb-0">
                    <i class="fas fa-chart-line me-2"></i>Events by Month ({{ $currentYear }})
                </h5>
            </div>
            <div class="card-body" style="height: 400px;">
                <canvas id="eventsChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 2: Events by Location & Top Events by Join Count (Gray) -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="card-title text-dark mb-0">
                    <i class="fas fa-map-marker-alt me-2"></i>Events by Location
                </h5>
            </div>
            <div class="card-body" style="height: 400px;">
                <canvas id="locationChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-gradient-primary">
                <h5 class="card-title text-dark mb-0 d-flex align-items-center">
                    <i class="fas fa-trophy me-2"></i>Top Events by Participants Count
                </h5>
            </div>
            <div class="card-body" style="height: 400px;">
                <canvas id="topEventsByJoinsChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 3: Active vs Cancelled Events - Bar Chart -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-gradient-success">
                <h5 class="card-title text-dark mb-0 d-flex align-items-center">
                    <i class="fas fa-chart-bar me-2"></i>Active vs Cancelled Events
                </h5>
            </div>
            <div class="card-body" style="height: 400px;">
                <canvas id="activeCancelledEventsChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Event Names List Section -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="card-title text-dark mb-0 d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-list me-2"></i>Event Names List</span>
                    <div class="d-flex align-items-center">
                        <label for="event_names_per_page" class="form-label me-2 mb-0 text-dark">Show:</label>
                        <select class="form-select form-select-sm" id="event_names_per_page" style="width: 80px;">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="15">15</option>
                            <option value="20">20</option>
                        </select>
                    </div>
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th class="text-dark">#</th>
                                <th class="text-dark">Event Name</th>
                                <th class="text-dark">Frequency</th>
                                <th class="text-dark">Percentage</th>
                            </tr>
                        </thead>
                        <tbody id="eventNamesTableBody">
                            <!-- Populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <small class="text-muted" id="eventNamesInfo"></small>
                    <nav>
                        <ul class="pagination pagination-sm mb-0" id="eventNamesPagination">
                            <!-- Populated by JavaScript -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Events Section -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="card-title text-dark d-flex justify-content-between align-items-center mb-0">
                    <span><i class="fas fa-calendar-alt me-2"></i>Recent Events</span>
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
                                            @php
                                                $imageSource = $event->image_base64 ?? $event->image_url;
                                            @endphp
                                            <img src="{{ $imageSource }}" 
                                                 alt="{{ $event->title }}" 
                                                 class="img-thumbnail" 
                                                 style="width: 50px; height: 50px; object-fit: cover; cursor: pointer;"
                                                 onclick="showImage('{{ addslashes($imageSource) }}', '{{ addslashes($event->title) }}')">
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
                                    @php
                                        $imageSource = $event->image_base64 ?? $event->image_url;
                                    @endphp
                                    <img src="{{ $imageSource }}" 
                                         class="card-img-top" 
                                         alt="{{ $event->title }}" 
                                         style="height: 200px; object-fit: cover; cursor: pointer;"
                                         onclick="showImage('{{ addslashes($imageSource) }}', '{{ addslashes($event->title) }}')">
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
let eventNamesPaginationState = {
    currentPage: 1,
    perPage: 10,
    data: []
};

// Store events data with base64 images
const eventsData = @json($allEvents->items());

document.addEventListener('DOMContentLoaded', function() {
    initCharts();
    
    // Initialize event names pagination
    eventNamesPaginationState.data = @json($eventNamesData);
    renderEventNamesTable();
    
    // Event names per page change
    document.getElementById('event_names_per_page').addEventListener('change', function() {
        eventNamesPaginationState.perPage = parseInt(this.value);
        eventNamesPaginationState.currentPage = 1;
        renderEventNamesTable();
    });
    
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

function renderEventNamesTable() {
    const { currentPage, perPage, data } = eventNamesPaginationState;
    const totalItems = data.length;
    const totalPages = Math.ceil(totalItems / perPage);
    const startIndex = (currentPage - 1) * perPage;
    const endIndex = Math.min(startIndex + perPage, totalItems);
    const pageData = data.slice(startIndex, endIndex);
    
    // Calculate total for percentages
    const total = data.reduce((sum, item) => sum + item.count, 0);
    
    // Render table body
    const tbody = document.getElementById('eventNamesTableBody');
    tbody.innerHTML = pageData.map((item, index) => {
        const percentage = ((item.count / total) * 100).toFixed(1);
        return `
            <tr>
                <td class="text-dark">${startIndex + index + 1}</td>
                <td class="text-dark"><strong>${escapeHtml(item.title)}</strong></td>
                <td class="text-dark">${item.count}</td>
                <td class="text-dark">
                    <div class="d-flex align-items-center">
                        <div class="progress flex-grow-1 me-2" style="height: 20px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: ${percentage}%" 
                                 aria-valuenow="${percentage}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                ${percentage}%
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
    
    // Render info
    document.getElementById('eventNamesInfo').textContent = 
        `Showing ${startIndex + 1} to ${endIndex} of ${totalItems} event names`;
    
    // Render pagination
    renderEventNamesPagination(totalPages);
}

function renderEventNamesPagination(totalPages) {
    const { currentPage } = eventNamesPaginationState;
    const pagination = document.getElementById('eventNamesPagination');
    
    let html = '';
    
    // Previous button
    html += `
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changeEventNamesPage(${currentPage - 1}); return false;">
                Previous
            </a>
        </li>
    `;
    
    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
            html += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="changeEventNamesPage(${i}); return false;">
                        ${i}
                    </a>
                </li>
            `;
        } else if (i === currentPage - 2 || i === currentPage + 2) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }
    
    // Next button
    html += `
        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changeEventNamesPage(${currentPage + 1}); return false;">
                Next
            </a>
        </li>
    `;
    
    pagination.innerHTML = html;
}

function changeEventNamesPage(page) {
    const totalPages = Math.ceil(eventNamesPaginationState.data.length / eventNamesPaginationState.perPage);
    if (page >= 1 && page <= totalPages) {
        eventNamesPaginationState.currentPage = page;
        renderEventNamesTable();
    }
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '<',
        '>': '>',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

function initCharts() {
    // Chart 1: Event Joins Status - Bar Chart
    const eventJoinsStatusCtx = document.getElementById('eventJoinsStatusChart').getContext('2d');
    const eventJoinsStatusData = @json($eventJoinsStatusData);
    new Chart(eventJoinsStatusCtx, {
        type: 'bar',
        data: {
            labels: ['Pending Approvals', 'Approved Joins'],
            datasets: [{
                label: 'Number of Joins',
                data: [eventJoinsStatusData.pending, eventJoinsStatusData.approved],
                backgroundColor: [
                    'rgba(255, 193, 7, 0.8)',
                    'rgba(40, 167, 69, 0.8)'
                ],
                borderColor: [
                    'rgba(255, 193, 7, 1)',
                    'rgba(40, 167, 69, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { 
                    beginAtZero: true,
                    ticks: { 
                        stepSize: 1,
                        font: { size: 12 }
                    }
                },
                x: {
                    ticks: { font: { size: 12 } }
                }
            },
            plugins: {
                legend: { 
                    display: false 
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: { size: 14 },
                    bodyFont: { size: 13 },
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + ' joins';
                        }
                    }
                }
            }
        }
    });

    // Chart 2: Monthly Events - Line Chart (UPDATED)
    const monthlyCtx = document.getElementById('eventsChart').getContext('2d');
    const monthlyData = @json($monthlyEvents);
    
    // Create gradient for the line chart
    const gradient = monthlyCtx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(54, 162, 235, 0.6)');
    gradient.addColorStop(1, 'rgba(54, 162, 235, 0.05)');
    
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: monthlyData.map(item => item.month),
            datasets: [{
                label: 'Number of Events',
                data: monthlyData.map(item => item.count),
                backgroundColor: gradient,
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 0,
                pointHoverRadius: 6,
                pointHoverBackgroundColor: 'rgba(54, 162, 235, 1)',
                pointHoverBorderColor: '#fff',
                pointHoverBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            scales: { 
                y: { 
                    beginAtZero: true, 
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false
                    },
                    ticks: { 
                        stepSize: 1,
                        font: { size: 12 },
                        color: '#666',
                        padding: 10
                    },
                    border: {
                        display: false
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: { 
                        font: { size: 12 },
                        color: '#666',
                        maxRotation: 0,
                        minRotation: 0,
                        padding: 10
                    },
                    border: {
                        display: false
                    }
                }
            },
            plugins: { 
                legend: { 
                    position: 'top',
                    labels: { 
                        font: { size: 12 },
                        color: '#666',
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 15,
                        boxWidth: 8,
                        boxHeight: 8
                    }
                },
                tooltip: {
                    enabled: true,
                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                    titleColor: '#333',
                    bodyColor: '#666',
                    borderColor: 'rgba(0, 0, 0, 0.1)',
                    borderWidth: 1,
                    padding: 12,
                    titleFont: { size: 12, weight: 'bold' },
                    bodyFont: { size: 11 },
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y;
                        }
                    }
                }
            }
        }
    });

    // Chart 3: Location - Donut Chart
    const locationCtx = document.getElementById('locationChart').getContext('2d');
    const locationData = @json($locationData);
    
    const backgroundColors = [
        'rgba(255, 99, 132, 0.8)',
        'rgba(54, 162, 235, 0.8)',
        'rgba(255, 206, 86, 0.8)',
        'rgba(75, 192, 192, 0.8)',
        'rgba(153, 102, 255, 0.8)',
        'rgba(255, 159, 64, 0.8)',
        'rgba(199, 199, 199, 0.8)'
    ];
    
    const borderColors = [
        'rgba(255, 99, 132, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)',
        'rgba(199, 199, 199, 1)'
    ];
    
    new Chart(locationCtx, {
        type: 'doughnut',
        data: {
            labels: locationData.map(item => item.location),
            datasets: [{
                label: 'Events by Location',
                data: locationData.map(item => item.count),
                backgroundColor: backgroundColors,
                borderColor: borderColors,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    position: 'right',
                    labels: {
                        padding: 15,
                        font: { size: 11 },
                        generateLabels: function(chart) {
                            const data = chart.data;
                            if (data.labels.length && data.datasets.length) {
                                const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                return data.labels.map((label, i) => {
                                    const value = data.datasets[0].data[i];
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return {
                                        text: `${label} (${value}) - ${percentage}%`,
                                        fillStyle: data.datasets[0].backgroundColor[i],
                                        hidden: false,
                                        index: i
                                    };
                                });
                            }
                            return [];
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: { size: 13 },
                    bodyFont: { size: 12 },
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });

    // Chart 4: Top Events by Joins - Horizontal Bar Chart (GRAY instead of red) (UPDATED)
    const topEventsByJoinsCtx = document.getElementById('topEventsByJoinsChart').getContext('2d');
    const topEventsByJoinsData = @json($topEventsByJoins);
    new Chart(topEventsByJoinsCtx, {
        type: 'bar',
        data: {
            labels: topEventsByJoinsData.map(item => item.title.length > 25 ? item.title.substring(0, 25) + '...' : item.title),
            datasets: [{
                label: 'Join Count',
                data: topEventsByJoinsData.map(item => item.join_count),
                backgroundColor: 'rgba(128, 128, 128, 0.8)', // Gray color
                borderColor: 'rgba(128, 128, 128, 1)', // Gray color
                borderWidth: 2
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { 
                    beginAtZero: true,
                    ticks: { 
                        stepSize: 1,
                        font: { size: 11 }
                    }
                },
                y: {
                    ticks: { font: { size: 11 } }
                }
            },
            plugins: {
                legend: { 
                    position: 'top',
                    labels: { font: { size: 12 } }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: { size: 13 },
                    bodyFont: { size: 12 }
                }
            }
        }
    });

    // Chart 5: Active vs Cancelled Events - Bar Chart (NEW)
    const activeCancelledCtx = document.getElementById('activeCancelledEventsChart').getContext('2d');
    const activeCancelledData = @json($activeCancelledData);
    new Chart(activeCancelledCtx, {
        type: 'bar',
        data: {
            labels: ['Active Events', 'Cancelled Events'],
            datasets: [{
                label: 'Event Status',
                data: [activeCancelledData.active, activeCancelledData.cancelled],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.8)', // Green for Active
                    'rgba(220, 53, 69, 0.8)'  // Red for Cancelled
                ],
                borderColor: [
                    'rgba(40, 167, 69, 1)',
                    'rgba(220, 53, 69, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { 
                    beginAtZero: true,
                    ticks: { 
                        stepSize: 1,
                        font: { size: 12 }
                    }
                },
                x: {
                    ticks: { font: { size: 12 } }
                }
            },
            plugins: {
                legend: { 
                    display: false 
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: { size: 14 },
                    bodyFont: { size: 13 },
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + ' ' + context.label.toLowerCase();
                        }
                    }
                }
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
    const event = eventsData.find(e => e.id === eventId);
    
    if (event) {
        const modal = new bootstrap.Modal(document.getElementById('eventModal'));
        const content = document.getElementById('eventContent');
        
        // Get the image URL - prefer base64
        let imageUrl = null;
        if (event.image_base64) {
            imageUrl = event.image_base64;
        } else if (event.image_url) {
            imageUrl = event.image_url;
        } else if (event.image && event.image.startsWith('data:image/')) {
            imageUrl = event.image;
        }
        
        let imageHtml = '';
        if (imageUrl) {
            imageHtml = `
                <div class="text-center mb-3">
                    <img src="${imageUrl}" alt="${escapeHtml(event.title)}" class="img-fluid rounded" 
                         style="max-height: 300px; cursor: pointer;" 
                         onclick="showImage('${imageUrl}', '${escapeHtml(event.title)}')">
                </div>
            `;
        }
        
        content.innerHTML = `
            ${imageHtml}
            <h4 class="text-dark">${escapeHtml(event.title)}</h4>
            <div class="row">
                <div class="col-md-6">
                    <p><strong class="text-dark">Date:</strong> <span class="text-dark">${new Date(event.date).toLocaleDateString()}</span></p>
                </div>
                <div class="col-md-6">
                    <p><strong class="text-dark">Location:</strong> <span class="text-dark">${escapeHtml(event.location)}</span></p>
                </div>
            </div>
            <div class="mt-3">
                <p><strong class="text-dark">Description:</strong></p>
                <p class="text-dark">${escapeHtml(event.description)}</p>
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