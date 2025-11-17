@extends('admin.layouts.app')

@section('title', 'Login Activity')
@section('page-title', 'Login Activity & Security Monitoring')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f8fafc;
    }

    /* Filters */
    .filters {
        background: white;
        padding: 16px;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        margin-bottom: 16px;
    }

    .filters h3 {
        margin-bottom: 12px;
        color: #1e293b;
        font-size: 14px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .filter-row {
        display: flex;
        gap: 12px;
        align-items: flex-end;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .form-group label {
        margin-bottom: 4px;
        color: #475569;
        font-size: 12px;
        font-weight: 600;
    }

    .form-group input {
        padding: 8px 10px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 13px;
        transition: all 0.2s ease;
        background: white;
    }

    .form-group input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .btn {
        padding: 8px 16px;
        border: none;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }

    .btn-primary {
        background: #667eea;
        color: white;
    }

    .btn-primary:hover {
        background: #5568d3;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .btn-map {
        background: #3b82f6;
        color: white;
        padding: 8px 14px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 16px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: none;
        cursor: pointer;
    }

    .btn-map:hover {
        background: #2563eb;
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(59, 130, 246, 0.3);
    }

    /* Plain Table */
    .table-container {
        background: white;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        padding: 16px;
        overflow-x: auto;
    }

    .table-container h2 {
        margin-bottom: 16px;
        color: #1e293b;
        font-size: 16px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .plain-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        min-width: 1200px;
    }

    .plain-table thead th {
        background: #f8fafc;
        padding: 12px;
        text-align: left;
        font-weight: 600;
        color: #475569;
        border-bottom: 2px solid #e2e8f0;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .plain-table tbody td {
        padding: 12px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        color: #334155;
    }

    .plain-table tbody tr:hover {
        background-color: #fafcfe;
    }

    .cell-email {
        font-weight: 600;
        color: #1e40af;
    }

    .cell-code {
        background: #f1f5f9;
        padding: 4px 8px;
        border-radius: 4px;
        font-family: 'Courier New', monospace;
        font-size: 12px;
        color: #475569;
    }

    .cell-location {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    .badge-success { 
        background: #dcfce7;
        color: #166534;
    }

    .badge-warning { 
        background: #fef9c3;
        color: #854d0e;
    }

    .badge-danger { 
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-active {
        background: #dcfce7;
        color: #166534;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    .action-btn {
        padding: 6px 10px !important;
        font-size: 11px !important;
    }

    /* Map Modal */
    .modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.7);
        animation: fadeIn 0.3s ease;
    }

    .modal.active {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .modal-content {
        background-color: white;
        border-radius: 12px;
        width: 90%;
        max-width: 1200px;
        height: 80vh;
        display: flex;
        flex-direction: column;
        animation: slideUp 0.3s ease;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }

    @keyframes slideUp {
        from { 
            opacity: 0;
            transform: translateY(50px);
        }
        to { 
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modal-header {
        padding: 20px 24px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 {
        margin: 0;
        color: #1e293b;
        font-size: 18px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .close-btn {
        background: none;
        border: none;
        font-size: 28px;
        color: #64748b;
        cursor: pointer;
        transition: color 0.2s;
        line-height: 1;
        padding: 0;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
    }

    .close-btn:hover {
        color: #1e293b;
        background: #f1f5f9;
    }

    .modal-body {
        flex: 1;
        padding: 24px;
        overflow: hidden;
    }

    #loginMap {
        height: 100%;
        border-radius: 8px;
    }

    /* Pagination */
    .pagination {
        margin-top: 16px;
        display: flex;
        justify-content: center;
        gap: 4px;
        flex-wrap: wrap;
    }

    .pagination a,
    .pagination span {
        padding: 6px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        text-decoration: none;
        color: #475569;
        font-size: 12px;
        font-weight: 600;
    }

    .pagination a:hover {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }

    .pagination .active {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }

    /* Responsive */
    @media (max-width: 1199px) {
        .col-logout-info,
        .col-device-info { display: none; }
    }

    @media (max-width: 991px) {
        .col-user,
        .col-login-location { display: none; }
    }

    @media (max-width: 768px) {
        .filter-row {
            flex-direction: column;
        }
    }
</style>
@endpush

@section('content')
    <!-- View Map Button -->
    <button type="button" class="btn btn-map" onclick="openMapModal()">
        <i class="fas fa-map-marked-alt"></i> View Location Map
    </button>

    <!-- Filters - Search Only -->
    <div class="filters">
        <form method="GET" action="{{ route('admin.login-logs.index') }}">
            <div class="filter-row">
                <div class="form-group">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Email, IP, Location...">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>
        </form>
    </div>

    <!-- Plain Table -->
    <div class="table-container">
        <h2><i class="fas fa-list"></i> Login Activity Records</h2>
        
        <table class="plain-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th class="col-user">User</th>
                    <th>Status</th>
                    <th>Login IP</th>
                    <th class="col-login-location">Login Location</th>
                    <th>Login Time</th>
                    <th>Logout Time</th>
                    <th>Duration</th>
                    <th class="col-logout-info">Logout Location</th>
                    <th class="col-device-info">Device</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td><strong>#{{ $log->id }}</strong></td>
                    <td>
                        <div class="cell-email">{{ $log->email_attempted }}</div>
                    </td>
                    <td class="col-user">
                        {{ $log->user ? $log->user->full_name : '—' }}
                    </td>
                    <td>
                        <span class="badge badge-{{ $log->status_badge_class }}">
                            @if($log->status === 'success')
                                <i class="fas fa-check-circle"></i>
                            @elseif($log->status === 'failed')
                                <i class="fas fa-times-circle"></i>
                            @else
                                <i class="fas fa-lock"></i>
                            @endif
                            {{ $log->status_label }}
                        </span>
                    </td>
                    <td>
                        <span class="cell-code">{{ $log->ip_address }}</span>
                    </td>
                    <td class="col-login-location">
                        <div class="cell-location">
                            @if($log->country_code)
                                <img src="https://flagcdn.com/16x12/{{ strtolower($log->country_code) }}.png" alt="{{ $log->country }}">
                            @endif
                            {{ $log->full_location ?? '—' }}
                        </div>
                    </td>
                    <td>
                        @if($log->login_at)
                            <div style="font-size: 12px;">
                                <div style="font-weight: 600;">{{ $log->login_at->format('M d, Y') }}</div>
                                <div style="color: #64748b;">{{ $log->login_at->format('H:i:s') }}</div>
                            </div>
                        @else
                            <span style="color: #94a3b8;">—</span>
                        @endif
                    </td>
                    <td>
                        @if($log->logout_at)
                            <div style="font-size: 12px;">
                                <div style="font-weight: 600;">{{ $log->logout_at->format('M d, Y') }}</div>
                                <div style="color: #64748b;">{{ $log->logout_at->format('H:i:s') }}</div>
                            </div>
                        @else
                            <span class="badge badge-active">
                                <i class="fas fa-circle" style="font-size: 6px;"></i> Active
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($log->formatted_duration)
                            <strong>{{ $log->formatted_duration }}</strong>
                        @else
                            <span style="color: #94a3b8;">—</span>
                        @endif
                    </td>
                    <td class="col-logout-info">
                        @if($log->logout_location)
                            <div class="cell-location">
                                @if($log->logout_country_code)
                                    <img src="https://flagcdn.com/16x12/{{ strtolower($log->logout_country_code) }}.png" alt="{{ $log->logout_country }}">
                                @endif
                                {{ $log->logout_location }}
                            </div>
                        @else
                            <span style="color: #94a3b8;">—</span>
                        @endif
                    </td>
                    <td class="col-device-info">
                        <div style="font-size: 11px; line-height: 1.5;">
                            <div>{{ $log->device_type ?? '—' }}</div>
                            <div style="color: #64748b;">{{ $log->browser ?? '—' }}</div>
                        </div>
                    </td>
                    <td>
                        <a href="{{ route('admin.login-logs.show', $log->id) }}" class="btn btn-primary action-btn">
                            <i class="fas fa-eye"></i> View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="12" style="text-align: center; padding: 40px; color: #94a3b8;">
                        <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 12px; display: block;"></i>
                        <strong>No login logs found</strong>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="pagination">
            {{ $logs->links() }}
        </div>
    </div>

    <!-- Map Modal -->
    <div id="mapModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-map-marked-alt"></i> Login Locations Map</h3>
                <button type="button" class="close-btn" onclick="closeMapModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div id="loginMap"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<script>
let map = null;
let markersLayer = null;

function openMapModal() {
    const modal = document.getElementById('mapModal');
    modal.classList.add('active');
    
    // Initialize map only once
    if (!map) {
        setTimeout(() => {
            initMap();
        }, 100);
    }
}

function closeMapModal() {
    const modal = document.getElementById('mapModal');
    modal.classList.remove('active');
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('mapModal');
    if (event.target === modal) {
        closeMapModal();
    }
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeMapModal();
    }
});

function initMap() {
    // Initialize map
    map = L.map('loginMap').setView([12.8797, 121.7740], 5); // Philippines center

    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 18,
    }).addTo(map);

    // Marker cluster group
    markersLayer = L.markerClusterGroup({
        spiderfyOnMaxZoom: true,
        showCoverageOnHover: false,
        zoomToBoundsOnClick: true
    });

    // Map data from PHP
    const mapData = @json($mapData);

    // Add markers
    mapData.forEach(function(log) {
        if (log.lat && log.lng) {
            // Custom icon based on status
            const iconColor = log.status === 'success' ? '#10b981' : (log.status === 'failed' ? '#f59e0b' : '#ef4444');
            const icon = L.divIcon({
                className: 'custom-marker',
                html: `<div style="background-color: ${iconColor}; width: 12px; height: 12px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>`,
                iconSize: [12, 12]
            });

            const marker = L.marker([log.lat, log.lng], { icon: icon });
            
            // Popup content
            let popupContent = `
                <div style="font-family: 'Segoe UI', sans-serif; min-width: 200px;">
                    <h4 style="margin: 0 0 8px 0; color: #1e293b; font-size: 14px;"><i class="fas fa-user"></i> ${log.userName}</h4>
                    <p style="margin: 4px 0; font-size: 12px;"><strong>Email:</strong> ${log.email}</p>
                    <p style="margin: 4px 0; font-size: 12px;"><strong>IP:</strong> ${log.ip}</p>
                    <p style="margin: 4px 0; font-size: 12px;"><strong>Location:</strong> ${log.location || 'Unknown'}</p>
                    <p style="margin: 4px 0; font-size: 12px;"><strong>Status:</strong> <span style="background: ${iconColor}; color: white; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: 600;">${log.statusLabel}</span></p>
                    ${log.loginTime ? `<p style="margin: 4px 0; font-size: 12px;"><strong>Login:</strong> ${log.loginTime}</p>` : ''}
                    ${log.logoutTime ? `<p style="margin: 4px 0; font-size: 12px;"><strong>Logout:</strong> ${log.logoutTime}</p>` : '<p style="margin: 4px 0; font-size: 12px;"><strong>Status:</strong> <span style="background: #10b981; color: white; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: 600;">Active</span></p>'}
                    ${log.duration ? `<p style="margin: 4px 0; font-size: 12px;"><strong>Duration:</strong> ${log.duration}</p>` : ''}
                    ${log.device ? `<p style="margin: 4px 0; font-size: 12px;"><strong>Device:</strong> ${log.device}</p>` : ''}
                </div>
            `;

            marker.bindPopup(popupContent);
            markersLayer.addLayer(marker);
        }
    });

    map.addLayer(markersLayer);

    // Auto-fit bounds if there are markers
    if (mapData.length > 0) {
        map.fitBounds(markersLayer.getBounds(), { padding: [50, 50] });
    }
}
</script>
@endpush