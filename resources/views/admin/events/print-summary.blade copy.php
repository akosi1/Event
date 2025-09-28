<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events Summary Report - MCC Event & Portfolio Organizer</title>
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none !important; }
            .page-break { page-break-before: always; }
            .table { font-size: 12px; }
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.4;
            color: #333;
            background: #fff;
        }
        
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 3px solid #007bff;
            margin-bottom: 30px;
        }
        
        .logo-section {
            margin-bottom: 15px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin: 0;
        }
        
        .subtitle {
            font-size: 16px;
            color: #6c757d;
            margin: 5px 0;
        }
        
        .report-title {
            font-size: 20px;
            font-weight: 600;
            margin: 15px 0 5px 0;
        }
        
        .generated-info {
            font-size: 12px;
            color: #888;
        }
        
        .summary-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }
        
        .stat-label {
            font-size: 14px;
            color: #6c757d;
            margin-top: 5px;
        }
        
        .filters-applied {
            background: #e9ecef;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: white;
        }
        
        .table th,
        .table td {
            padding: 12px 8px;
            text-align: left;
            border: 1px solid #dee2e6;
        }
        
        .table th {
            background: #f8f9fa;
            font-weight: 600;
            font-size: 14px;
        }
        
        .table td {
            font-size: 13px;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 500;
            text-transform: uppercase;
        }
        
        .status-active { background: #d4edda; color: #155724; }
        .status-postponed { background: #fff3cd; color: #856404; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
        
        .department-badge {
            background: #e3f2fd;
            color: #1565c0;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: 500;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        .btn {
            display: inline-block;
            padding: 8px 16px;
            font-size: 14px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-primary:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <!-- Print Button -->
    <div class="print-button no-print">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Print Report
        </button>
    </div>

    <!-- Header -->
    <div class="header">
        <div class="logo-section">
            <h1 class="company-name">MCC Event & Portfolio Organizer</h1>
            <p class="subtitle">Comprehensive Event Management System</p>
        </div>
        <h2 class="report-title">Events Summary Report</h2>
        <div class="generated-info">
            Generated on {{ now()->format('F j, Y \a\t g:i A') }} by {{ auth()->user()->full_name }}
        </div>
    </div>

    <!-- Applied Filters -->
    @if($request->hasAny(['search', 'status', 'department']))
    <div class="filters-applied">
        <h4 style="margin: 0 0 10px 0; font-size: 16px;">Applied Filters:</h4>
        <div style="font-size: 14px;">
            @if($request->filled('search'))
                <strong>Search:</strong> "{{ $request->search }}" •
            @endif
            @if($request->filled('status'))
                <strong>Status:</strong> {{ ucfirst($request->status) }} •
            @endif
            @if($request->filled('department'))
                <strong>Department:</strong> {{ $request->department }} •
            @endif
            <em>Results filtered accordingly</em>
        </div>
    </div>
    @endif

    <!-- Summary Statistics -->
    <div class="summary-stats">
        <div class="stat-card">
            <div class="stat-number">{{ $stats['total'] }}</div>
            <div class="stat-label">Total Events</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $stats['active'] }}</div>
            <div class="stat-label">Active Events</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $stats['upcoming'] }}</div>
            <div class="stat-label">Upcoming Events</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $stats['past'] }}</div>
            <div class="stat-label">Past Events</div>
        </div>
    </div>

    <!-- Department Breakdown (if available) -->
    @if($stats['by_department']->isNotEmpty())
    <div style="margin-bottom: 30px;">
        <h3 style="font-size: 18px; margin-bottom: 15px;">Events by Department</h3>
        <div class="summary-stats">
            @foreach($stats['by_department'] as $dept => $count)
            <div class="stat-card">
                <div class="stat-number">{{ $count }}</div>
                <div class="stat-label">{{ $dept }} Events</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Events Table -->
    <div style="margin-bottom: 30px;">
        <h3 style="font-size: 18px; margin-bottom: 15px;">Events List</h3>
        @if($events->count())
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Event Title</th>
                    <th>Date & Time</th>
                    <th>Location</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th>Participants</th>
                </tr>
            </thead>
            <tbody>
                @foreach($events as $event)
                <tr>
                    <td>#{{ $event->id }}</td>
                    <td>
                        <strong>{{ $event->title }}</strong>
                        @if($event->description)
                        <br><small style="color: #6c757d;">{{ Str::limit($event->description, 60) }}</small>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $event->date->format('M j, Y') }}</strong>
                        <br><small>{{ $event->date->format('g:i A') }}</small>
                    </td>
                    <td>{{ $event->location }}</td>
                    <td>
                        @if($event->department)
                            <span class="department-badge">{{ $event->department }}</span>
                        @else
                            <span style="color: #6c757d;">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="status-badge status-{{ $event->status }}">
                            {{ ucfirst($event->status) }}
                        </span>
                        @if($event->cancel_reason && in_array($event->status, ['postponed', 'cancelled']))
                            <br><small style="color: #dc3545;">{{ Str::limit($event->cancel_reason, 40) }}</small>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <strong>{{ $event->joined_count }}</strong>
                        <br><small style="color: #6c757d;">joined</small>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div style="text-align: center; padding: 40px; color: #6c757d;">
            <p>No events found matching the current criteria.</p>
        </div>
        @endif
    </div>

    <!-- Status Summary -->
    @if($events->count())
    <div style="margin-bottom: 30px;">
        <h3 style="font-size: 18px; margin-bottom: 15px;">Status Breakdown</h3>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
            <div style="text-align: center; padding: 15px; background: #d4edda; border-radius: 8px;">
                <div style="font-size: 20px; font-weight: bold; color: #155724;">{{ $stats['active'] }}</div>
                <div style="color: #155724;">Active Events</div>
            </div>
            <div style="text-align: center; padding: 15px; background: #fff3cd; border-radius: 8px;">
                <div style="font-size: 20px; font-weight: bold; color: #856404;">{{ $stats['postponed'] }}</div>
                <div style="color: #856404;">Postponed Events</div>
            </div>
            <div style="text-align: center; padding: 15px; background: #f8d7da; border-radius: 8px;">
                <div style="font-size: 20px; font-weight: bold; color: #721c24;">{{ $stats['cancelled'] }}</div>
                <div style="color: #721c24;">Cancelled Events</div>
            </div>
        </div>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p><strong>MCC Event & Portfolio Organizer</strong></p>
        <p>This report contains {{ $stats['total'] }} events as of {{ now()->format('F j, Y \a\t g:i A') }}</p>
        <p style="margin-top: 20px; font-size: 11px;">
            Report generated by {{ auth()->user()->full_name }} • 
            {{ request()->ip() }} • 
            MCC Events Management System
        </p>
    </div>

    <script>
        // Auto-focus print dialog when page loads
        window.onload = function() {
            // Small delay to ensure page is fully rendered
            setTimeout(() => {
                window.print();
            }, 500);
        }

        // Handle print dialog close - return to events page
        window.onafterprint = function() {
            window.close();
        }
    </script>
</body>
</html>