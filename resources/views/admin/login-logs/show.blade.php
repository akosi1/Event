@extends('admin.layouts.app')

@section('title', 'Login Log Details')
@section('page-title', 'Login Log #' . $loginLog->id)

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .detail-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .detail-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        padding: 24px;
        margin-bottom: 24px;
    }

    .detail-card h3 {
        margin-bottom: 20px;
        color: #1e293b;
        font-size: 18px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
        padding-bottom: 12px;
        border-bottom: 2px solid #e2e8f0;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .detail-label {
        color: #64748b;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .detail-value {
        color: #1e293b;
        font-size: 15px;
        font-weight: 500;
    }

    .detail-value code {
        background: #f1f5f9;
        padding: 4px 8px;
        border-radius: 4px;
        font-family: 'Courier New', monospace;
        font-size: 13px;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 16px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-success { 
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        color: #166534;
    }

    .badge-warning { 
        background: linear-gradient(135deg, #fef9c3 0%, #fde047 100%);
        color: #854d0e;
    }

    .badge-danger { 
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #991b1b;
    }

    .badge-active {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        color: #166534;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    .map-container {
        height: 400px;
        border-radius: 8px;
        overflow: hidden;
        margin-top: 20px;
    }

    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-secondary {
        background: #64748b;
        color: white;
    }

    .btn-secondary:hover {
        background: #475569;
        transform: translateY(-2px);
    }

    .timeline {
        position: relative;
        padding-left: 40px;
        margin-top: 20px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 8px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: linear-gradient(to bottom, #667eea, #764ba2);
    }

    .timeline-item {
        position: relative;
        margin-bottom: 30px;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: -35px;
        top: 5px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: white;
        border: 3px solid #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }

    .timeline-time {
        color: #667eea;
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 4px;
    }

    .timeline-content {
        background: #f8fafc;
        padding: 12px 16px;
        border-radius: 8px;
        border-left: 3px solid #667eea;
    }

    .timeline-content p {
        margin: 4px 0;
        font-size: 13px;
        color: #475569;
    }

    .device-info {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        background: #f8fafc;
        border-radius: 8px;
        border-left: 4px solid #667eea;
    }

    .device-icon {
        font-size: 36px;
        color: #667eea;
    }

    .device-details {
        flex: 1;
    }

    .device-details p {
        margin: 4px 0;
        font-size: 13px;
        color: #475569;
    }
</style>
@endpush

@section('content')
<div class="detail-container">
    <!-- Back Button -->
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.login-logs.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Login Logs
        </a>
    </div>

    <!-- Basic Information -->
    <div class="detail-card">
        <h3><i class="fas fa-info-circle"></i> Basic Information</h3>
        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label">Log ID</div>
                <div class="detail-value"><strong>#{{ $loginLog->id }}</strong></div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Email Attempted</div>
                <div class="detail-value" style="color: #1e40af; font-weight: 600;">{{ $loginLog->email_attempted }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">User</div>
                <div class="detail-value">
                    @if($loginLog->user)
                        {{ $loginLog->user->full_name }}
                        <br><small style="color: #64748b;">{{ $loginLog->user->email }}</small>
                    @else
                        <span style="color: #94a3b8;">No user associated</span>
                    @endif
                </div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Status</div>
                <div class="detail-value">
                    <span class="badge badge-{{ $loginLog->status_badge_class }}">
                        @if($loginLog->status === 'success')
                            <i class="fas fa-check-circle"></i>
                        @elseif($loginLog->status === 'failed')
                            <i class="fas fa-times-circle"></i>
                        @else
                            <i class="fas fa-lock"></i>
                        @endif
                        {{ $loginLog->status_label }}
                    </span>
                </div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Session Status</div>
                <div class="detail-value">
                    @if($loginLog->isActive())
                        <span class="badge badge-active">
                            <i class="fas fa-circle" style="font-size: 6px;"></i> Active Session
                        </span>
                    @else
                        <span class="badge badge-success">Session Ended</span>
                    @endif
                </div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Attempt Date</div>
                <div class="detail-value">
                    {{ $loginLog->created_at->format('F d, Y') }}<br>
                    <small style="color: #64748b;">{{ $loginLog->created_at->format('h:i:s A') }} ({{ $loginLog->created_at->diffForHumans() }})</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Login Information -->
    @if($loginLog->status === 'success')
    <div class="detail-card">
        <h3><i class="fas fa-sign-in-alt"></i> Login Information</h3>
        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label">IP Address</div>
                <div class="detail-value"><code>{{ $loginLog->ip_address }}</code></div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Location</div>
                <div class="detail-value">
                    @if($loginLog->country_code)
                        <img src="https://flagcdn.com/24x18/{{ strtolower($loginLog->country_code) }}.png" 
                             alt="{{ $loginLog->country }}" 
                             style="vertical-align: middle; margin-right: 8px;">
                    @endif
                    {{ $loginLog->full_location ?? 'Unknown Location' }}
                </div>
            </div>

            <div class="detail-item">
                <div class="detail-label">ISP</div>
                <div class="detail-value">{{ $loginLog->isp ?? 'Unknown' }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Timezone</div>
                <div class="detail-value">{{ $loginLog->timezone ?? 'Unknown' }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Coordinates</div>
                <div class="detail-value">
                    @if($loginLog->latitude && $loginLog->longitude)
                        <code>{{ number_format($loginLog->latitude, 6) }}, {{ number_format($loginLog->longitude, 6) }}</code>
                    @else
                        <span style="color: #94a3b8;">Not available</span>
                    @endif
                </div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Login Time</div>
                <div class="detail-value">
                    {{ $loginLog->login_at->format('F d, Y') }}<br>
                    <small style="color: #64748b;">{{ $loginLog->login_at->format('h:i:s A') }}</small>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Logout Information -->
    @if($loginLog->logout_at)
    <div class="detail-card">
        <h3><i class="fas fa-sign-out-alt"></i> Logout Information</h3>
        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label">Logout IP Address</div>
                <div class="detail-value"><code>{{ $loginLog->logout_ip_address }}</code></div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Logout Location</div>
                <div class="detail-value">
                    @if($loginLog->logout_country_code)
                        <img src="https://flagcdn.com/24x18/{{ strtolower($loginLog->logout_country_code) }}.png" 
                             alt="{{ $loginLog->logout_country }}" 
                             style="vertical-align: middle; margin-right: 8px;">
                    @endif
                    {{ $loginLog->logout_location ?? 'Unknown Location' }}
                </div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Logout Coordinates</div>
                <div class="detail-value">
                    @if($loginLog->logout_latitude && $loginLog->logout_longitude)
                        <code>{{ number_format($loginLog->logout_latitude, 6) }}, {{ number_format($loginLog->logout_longitude, 6) }}</code>
                    @else
                        <span style="color: #94a3b8;">Not available</span>
                    @endif
                </div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Logout Time</div>
                <div class="detail-value">
                    {{ $loginLog->logout_at->format('F d, Y') }}<br>
                    <small style="color: #64748b;">{{ $loginLog->logout_at->format('h:i:s A') }}</small>
                </div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Session Duration</div>
                <div class="detail-value">
                    <strong style="color: #667eea; font-size: 18px;">{{ $loginLog->formatted_duration }}</strong>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Device Information -->
    <div class="detail-card">
        <h3><i class="fas fa-laptop"></i> Device Information</h3>
        
        <div class="device-info">
            <div class="device-icon">
                <i class="fas fa-{{ $loginLog->device_type === 'Mobile' ? 'mobile-alt' : ($loginLog->device_type === 'Tablet' ? 'tablet-alt' : 'desktop') }}"></i>
            </div>
            <div class="device-details">
                <p><strong>Device Type:</strong> {{ $loginLog->device_type ?? 'Unknown' }}</p>
                <p><strong>Browser:</strong> {{ $loginLog->browser ?? 'Unknown' }}</p>
                <p><strong>Operating System:</strong> {{ $loginLog->os ?? 'Unknown' }}</p>
            </div>
        </div>

        @if($loginLog->user_agent)
        <div style="margin-top: 16px;">
            <div class="detail-label" style="margin-bottom: 8px;">User Agent String</div>
            <code style="display: block; background: #f1f5f9; padding: 12px; border-radius: 8px; font-size: 12px; word-break: break-all; line-height: 1.6;">
                {{ $loginLog->user_agent }}
            </code>
        </div>
        @endif
    </div>

    <!-- Session Timeline -->
    @if($loginLog->status === 'success')
    <div class="detail-card">
        <h3><i class="fas fa-history"></i> Session Timeline</h3>
        
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-time">
                    <i class="fas fa-sign-in-alt"></i> {{ $loginLog->login_at->format('h:i:s A') }}
                </div>
                <div class="timeline-content">
                    <p><strong>User logged in</strong></p>
                    <p><i class="fas fa-map-marker-alt"></i> From: {{ $loginLog->full_location ?? 'Unknown' }}</p>
                    <p><i class="fas fa-network-wired"></i> IP: {{ $loginLog->ip_address }}</p>
                </div>
            </div>

            @if($loginLog->logout_at)
            <div class="timeline-item">
                <div class="timeline-time">
                    <i class="fas fa-sign-out-alt"></i> {{ $loginLog->logout_at->format('h:i:s A') }}
                </div>
                <div class="timeline-content">
                    <p><strong>User logged out</strong></p>
                    <p><i class="fas fa-map-marker-alt"></i> From: {{ $loginLog->logout_location ?? 'Unknown' }}</p>
                    <p><i class="fas fa-network-wired"></i> IP: {{ $loginLog->logout_ip_address }}</p>
                    <p><i class="fas fa-clock"></i> Session Duration: <strong>{{ $loginLog->formatted_duration }}</strong></p>
                </div>
            </div>
            @else
            <div class="timeline-item">
                <div class="timeline-time">
                    <i class="fas fa-circle" style="font-size: 8px;"></i> Now
                </div>
                <div class="timeline-content">
                    <p><strong>Session is still active</strong></p>
                    <p class="badge badge-active" style="margin-top: 8px;">
                        <i class="fas fa-circle" style="font-size: 6px;"></i> Currently Logged In
                    </p>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Map -->
    @if($loginLog->latitude && $loginLog->longitude)
    <div class="detail-card">
        <h3><i class="fas fa-map-marked-alt"></i> Location Map</h3>
        <div class="map-container" id="detailMap"></div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
@if($loginLog->latitude && $loginLog->longitude)
document.addEventListener('DOMContentLoaded', function() {
    const map = L.map('detailMap').setView([{{ $loginLog->latitude }}, {{ $loginLog->longitude }}], 10);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 18,
    }).addTo(map);

    // Login marker
    const loginIcon = L.divIcon({
        className: 'custom-marker',
        html: '<div style="background-color: #10b981; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.3);"></div>',
        iconSize: [20, 20]
    });

    const loginMarker = L.marker([{{ $loginLog->latitude }}, {{ $loginLog->longitude }}], { icon: loginIcon })
        .bindPopup(`
            <div style="font-family: 'Segoe UI', sans-serif;">
                <h4 style="margin: 0 0 8px 0; color: #1e293b;"><i class="fas fa-sign-in-alt"></i> Login Location</h4>
                <p style="margin: 4px 0; font-size: 12px;"><strong>IP:</strong> {{ $loginLog->ip_address }}</p>
                <p style="margin: 4px 0; font-size: 12px;"><strong>Location:</strong> {{ $loginLog->full_location }}</p>
                <p style="margin: 4px 0; font-size: 12px;"><strong>Time:</strong> {{ $loginLog->login_at ? $loginLog->login_at->format('M d, Y H:i:s') : 'N/A' }}</p>
            </div>
        `)
        .addTo(map);

    @if($loginLog->logout_latitude && $loginLog->logout_longitude)
    // Logout marker
    const logoutIcon = L.divIcon({
        className: 'custom-marker',
        html: '<div style="background-color: #ef4444; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.3);"></div>',
        iconSize: [20, 20]
    });

    const logoutMarker = L.marker([{{ $loginLog->logout_latitude }}, {{ $loginLog->logout_longitude }}], { icon: logoutIcon })
        .bindPopup(`
            <div style="font-family: 'Segoe UI', sans-serif;">
                <h4 style="margin: 0 0 8px 0; color: #1e293b;"><i class="fas fa-sign-out-alt"></i> Logout Location</h4>
                <p style="margin: 4px 0; font-size: 12px;"><strong>IP:</strong> {{ $loginLog->logout_ip_address }}</p>
                <p style="margin: 4px 0; font-size: 12px;"><strong>Location:</strong> {{ $loginLog->logout_location }}</p>
                <p style="margin: 4px 0; font-size: 12px;"><strong>Time:</strong> {{ $loginLog->logout_at->format('M d, Y H:i:s') }}</p>
            </div>
        `)
        .addTo(map);

    // Draw line between login and logout
    const latlngs = [
        [{{ $loginLog->latitude }}, {{ $loginLog->longitude }}],
        [{{ $loginLog->logout_latitude }}, {{ $loginLog->logout_longitude }}]
    ];
    
    L.polyline(latlngs, {
        color: '#667eea',
        weight: 2,
        opacity: 0.7,
        dashArray: '5, 10'
    }).addTo(map);

    // Fit bounds to show both markers
    const group = L.featureGroup([loginMarker, logoutMarker]);
    map.fitBounds(group.getBounds().pad(0.2));
    @endif
});
@endif
</script>
@endpush