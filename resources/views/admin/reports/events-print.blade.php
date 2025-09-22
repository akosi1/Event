<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Events Report - {{ date('Y-m-d') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { 
            font-family: 'Arial', sans-serif; 
            margin: 0; 
            padding: 20px; 
            background: #fff;
            color: #333;
        }
        
        .header { 
            text-align: center; 
            border-bottom: 3px solid #2c3e50; 
            padding-bottom: 25px; 
            margin-bottom: 30px; 
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 30px 20px 25px;
            border-radius: 10px 10px 0 0;
        }
        
        .header .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 15px;
            background: #2c3e50;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
        }
        
        .header h1 { 
            margin: 0; 
            color: #2c3e50; 
            font-size: 28px;
            font-weight: 600;
        }
        
        .header p { 
            margin: 8px 0; 
            color: #6c757d; 
            font-size: 14px;
        }
        
        .filters { 
            background: #f8f9fa; 
            padding: 20px; 
            margin-bottom: 25px; 
            border-radius: 8px; 
            border-left: 4px solid #007bff;
        }
        
        .filters h4 { 
            margin-top: 0; 
            color: #2c3e50;
            font-size: 16px;
        }
        
        .filters p { 
            margin: 8px 0; 
            font-size: 14px;
        }
        
        .summary { 
            margin-bottom: 25px; 
            background: #e3f2fd;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #2196f3;
        }
        
        .summary h3 { 
            color: #1976d2; 
            margin-top: 0;
            font-size: 18px;
        }
        
        .summary-stats {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }
        
        .stat-item {
            flex: 1;
            min-width: 150px;
        }
        
        .stat-item strong {
            color: #2c3e50;
        }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        
        th, td { 
            border: 1px solid #e9ecef; 
            padding: 12px 8px; 
            text-align: left; 
            font-size: 14px;
        }
        
        th { 
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }
        
        tr:nth-child(even) { 
            background-color: #f8f9fa; 
        }
        
        tr:hover {
            background-color: #e3f2fd;
        }
        
        .footer { 
            text-align: center; 
            margin-top: 40px; 
            padding-top: 25px; 
            border-top: 2px solid #e9ecef; 
            color: #6c757d; 
            font-size: 12px;
        }
        
        .no-data { 
            text-align: center; 
            padding: 60px 20px; 
            color: #6c757d; 
            background: #f8f9fa;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .no-data i {
            font-size: 48px;
            margin-bottom: 20px;
            color: #dee2e6;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .badge-primary {
            background: #007bff;
            color: white;
        }
        
        .report-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px;
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
        }
        
        .report-meta .left {
            font-weight: 600;
            color: #2c3e50;
        }
        
        .report-meta .right {
            color: #6c757d;
            font-size: 14px;
        }
        
        /* Event Image Styles */
        .event-image {
            width: 60px;
            height: 60px;
            border-radius: 6px;
            object-fit: cover;
            border: 2px solid #e9ecef;
        }
        
        .event-image-placeholder {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 2px solid #e9ecef;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-size: 20px;
        }
        
        .event-image-container {
            text-align: center;
        }
        
        .image-status {
            font-size: 10px;
            color: #28a745;
            margin-top: 2px;
            display: block;
        }
        
        @media print {
            body { 
                margin: 0; 
                padding: 15px;
            }
            .no-print { 
                display: none !important; 
            }
            .header {
                background: #f8f9fa !important;
                -webkit-print-color-adjust: exact;
            }
            tr:nth-child(even) { 
                background-color: #f8f9fa !important;
                -webkit-print-color-adjust: exact;
            }
            
            /* Hide URL and browser elements */
            @page {
                margin: 0.5in;
                size: auto;
            }
            
            /* Hide any URL text or browser chrome */
            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
        }
        
        /* Hide URL bar and browser elements */
        body {
            -webkit-app-region: no-drag;
        }
        
        /* Additional print settings */
        @media print {
            /* Remove any potential URL display */
            .url-display,
            .browser-url,
            .address-bar,
            .print-url {
                display: none !important;
                visibility: hidden !important;
            }
            
            /* Ensure clean print layout */
            html, body {
                width: 100%;
                height: 100%;
                margin: 0;
                padding: 0;
            }
            
            /* Remove any automatic URL insertion */
            a[href]:after {
                content: none !important;
            }
            
            /* Hide browser-generated content */
            .chrome-print-header,
            .chrome-print-footer {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <h1>EventAP - Events Report</h1>
        <p>Comprehensive Events Management System</p>
        <p>Generated on {{ date('F d, Y g:i A') }}</p>
    </div>

    <div class="report-meta">
        <div class="left">
            <span class="badge badge-primary">
                <i class="fas fa-file-alt"></i> Official Report
            </span>
        </div>
        <div class="right">
            <i class="fas fa-hashtag"></i> Report ID: EVT-{{ date('YmdHis') }}
        </div>
    </div>

    @if(array_filter($filters))
    <div class="filters">
        <h4><i class="fas fa-filter"></i> Applied Filters:</h4>
        @if($filters['search'])
            <p><strong><i class="fas fa-search"></i> Search:</strong> {{ $filters['search'] }}</p>
        @endif
        @if($filters['date_from'])
            <p><strong><i class="fas fa-calendar-day"></i> Date From:</strong> {{ date('M d, Y', strtotime($filters['date_from'])) }}</p>
        @endif
        @if($filters['date_to'])
            <p><strong><i class="fas fa-calendar-day"></i> Date To:</strong> {{ date('M d, Y', strtotime($filters['date_to'])) }}</p>
        @endif
        @if($filters['location'])
            <p><strong><i class="fas fa-map-marker-alt"></i> Location:</strong> {{ $filters['location'] }}</p>
        @endif
    </div>
    @endif

    <div class="summary">
        <h3><i class="fas fa-chart-bar"></i> Report Summary</h3>
        <div class="summary-stats">
            <div class="stat-item">
                <p><strong><i class="fas fa-calendar-check"></i> Total Events:</strong> {{ $events->count() }}</p>
            </div>
            @if($events->count() > 0)
                <div class="stat-item">
                    <p><strong><i class="fas fa-clock"></i> Date Range:</strong> {{ $events->min('date')->format('M d, Y') }} - {{ $events->max('date')->format('M d, Y') }}</p>
                </div>
                <div class="stat-item">
                    <p><strong><i class="fas fa-map-marker-alt"></i> Unique Locations:</strong> {{ $events->pluck('location')->unique()->count() }}</p>
                </div>
                <div class="stat-item">
                    <p><strong><i class="fas fa-calendar-plus"></i> Upcoming Events:</strong> {{ $events->where('date', '>=', now())->count() }}</p>
                </div>
            @endif
        </div>
    </div>

    @if($events->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 10%;"><i class="fas fa-image"></i> Image</th>
                    <th style="width: 25%;"><i class="fas fa-heading"></i> Event Title</th>
                    <th style="width: 15%;"><i class="fas fa-calendar"></i> Date</th>
                    <th style="width: 20%;"><i class="fas fa-map-marker-alt"></i> Location</th>
                    <th style="width: 30%;"><i class="fas fa-align-left"></i> Description</th>
                </tr>
            </thead>
            <tbody>
                @foreach($events as $event)
                <tr>
                    <td>
                        <div class="event-image-container">
                            @if($event->hasImage())
                                <img src="{{ $event->image_url }}" 
                                     alt="{{ $event->title }}" 
                                     class="event-image">
                                <span class="image-status">
                                    <i class="fas fa-check-circle"></i> Available
                                </span>
                            @else
                                <div class="event-image-placeholder">
                                    <i class="fas fa-image"></i>
                                </div>
                                <span class="image-status" style="color: #6c757d;">
                                    <i class="fas fa-times-circle"></i> No Image
                                </span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <strong>{{ $event->title }}</strong>
                        @if($event->hasImage())
                            <br><small style="color: #28a745;">
                                <i class="fas fa-camera"></i> Image Available
                            </small>
                        @endif
                    </td>
                    <td>
                        <i class="fas fa-calendar-day"></i> {{ $event->date->format('M d, Y') }}
                        <br>
                        <small style="color: #6c757d;">
                            <i class="fas fa-clock"></i> {{ $event->date->format('D') }}
                        </small>
                    </td>
                    <td>
                        <i class="fas fa-map-marker-alt"></i> {{ $event->location }}
                    </td>
                    <td>
                        <div style="line-height: 1.4;">
                            {{ Str::limit($event->description, 100, '...') }}
                        </div>
                        <br>
                        <small style="color: #6c757d;">
                            <i class="fas fa-plus-circle"></i> Created: {{ $event->created_at->format('M d, Y') }}
                        </small>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            <i class="fas fa-calendar-times"></i>
            <h4>No Events Found</h4>
            <p>No events found matching the specified criteria.</p>
        </div>
    @endif

    <div class="footer">
        <p><strong><i class="fas fa-copyright"></i> {{ date('Y') }} EventAP Admin Panel</strong></p>
        <p><i class="fas fa-chart-line"></i> This report contains {{ $events->count() }} event(s) | Generated automatically</p>
        <p style="margin-top: 10px; font-size: 11px; color: #adb5bd;">
            <i class="fas fa-question-circle"></i> For questions about this report, contact your system administrator
        </p>
    </div>

    <script>
        // Auto-print when page loads with clean print settings
        window.onload = function() {
            // Hide URL bar and browser elements before printing
            document.body.style.visibility = 'visible';
            
            // Configure print settings
            if (window.matchMedia) {
                var mediaQueryList = window.matchMedia('print');
                mediaQueryList.addListener(function(mql) {
                    if (mql.matches) {
                        // Before printing - hide URL elements
                        document.querySelectorAll('.url-display, .browser-url, .address-bar').forEach(function(el) {
                            el.style.display = 'none';
                        });
                    }
                });
            }
            
            // Delay print to ensure clean layout
            setTimeout(function() {
                window.print();
            }, 500);
        };
        
        // Additional URL hiding for different browsers
        window.addEventListener('beforeprint', function() {
            document.body.classList.add('printing');
        });
        
        window.addEventListener('afterprint', function() {
            document.body.classList.remove('printing');
        });
    </script>
</body>
</html>