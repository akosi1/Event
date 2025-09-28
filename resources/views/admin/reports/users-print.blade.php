<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Report - {{ now()->format('M d, Y') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            background: #fff;
        }
        
        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Header Section */
        .report-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #333;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        
        .report-title {
            font-size: 18px;
            font-weight: bold;
            color: #555;
            margin: 10px 0;
        }
        
        .report-date {
            font-size: 12px;
            color: #666;
        }
        
        /* Applied Filters Section */
        .filters-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 25px;
            border: 1px solid #e9ecef;
        }
        
        .filters-title {
            font-size: 12px;
            font-weight: bold;
            color: #495057;
            margin-bottom: 10px;
        }
        
        .filter-item {
            display: inline-block;
            margin-right: 20px;
            margin-bottom: 5px;
            font-size: 10px;
        }
        
        .filter-label {
            font-weight: bold;
            color: #495057;
        }
        
        .filter-value {
            color: #007bff;
        }
        
        /* Users Table */
        .users-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .users-table th {
            background: #f8f9fa;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
            color: #495057;
            border: 1px solid #dee2e6;
        }
        
        .users-table td {
            padding: 10px 8px;
            border: 1px solid #dee2e6;
            font-size: 10px;
        }
        
        .users-table tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .user-info {
            display: flex;
            align-items: center;
        }
        
        .user-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #6c757d;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            margin-right: 10px;
        }
        
        .user-name {
            font-weight: bold;
            color: #333;
        }
        
        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: 500;
            text-transform: uppercase;
        }
        
        .badge.admin {
            background: #e3f2fd;
            color: #1976d2;
            border: 1px solid #bbdefb;
        }
        
        .badge.user {
            background: #f5f5f5;
            color: #616161;
            border: 1px solid #e0e0e0;
        }
        
        .badge.active {
            background: #e8f5e8;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }
        
        .badge.inactive {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }
        
        .date-info {
            text-align: center;
        }
        
        .date-main {
            font-weight: 500;
            color: #333;
        }
        
        .date-time {
            font-size: 9px;
            color: #666;
        }
        
        /* Summary Section */
        .summary-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 25px;
            border: 1px solid #e9ecef;
        }
        
        .summary-title {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .summary-stats {
            display: flex;
            justify-content: space-around;
            text-align: center;
        }
        
        .stat-item {
            flex: 1;
            padding: 10px;
        }
        
        .stat-number {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
        }
        
        /* Footer */
        .report-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #666;
        }
        
        .footer-left, .footer-right {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }
        
        .footer-label {
            font-weight: bold;
            color: #495057;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: #666;
        }
        
        .empty-icon {
            font-size: 48px;
            margin-bottom: 15px;
            color: #ccc;
        }
        
        .empty-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .empty-subtitle {
            font-size: 12px;
        }
        
        /* Print Styles */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                font-size: 10px;
            }
            
            .container {
                padding: 10px;
            }
            
            .report-header {
                margin-bottom: 20px;
                padding-bottom: 15px;
            }
            
            .users-table th,
            .users-table td {
                padding: 8px 6px;
            }
            
            .page-break {
                page-break-before: always;
            }
            
            .user-avatar {
                width: 25px;
                height: 25px;
                font-size: 10px;
            }
            
            /* Hide URL watermark and clean print */
            @page {
                margin: 0.5in;
                size: auto;
            }
            
            /* Remove URL from print */
            body::after {
                content: none !important;
            }
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .users-table {
                font-size: 9px;
            }
            
            .user-avatar {
                width: 25px;
                height: 25px;
                font-size: 10px;
            }
            
            .summary-stats {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Report Header -->
        <div class="report-header">
            <div class="company-name">Event & Portfolio Organizer</div>
            <div class="report-title">USERS REPORT</div>
            <div class="report-date">Generated on {{ now()->format('F d, Y \a\t g:i A') }}</div>
        </div>
        
        <!-- Applied Filters (if any) -->
        @if(array_filter($filters))
        <div class="filters-section">
            <div class="filters-title">📋 Applied Filters</div>
            @if($filters['search'])
                <span class="filter-item">
                    <span class="filter-label">Search:</span> 
                    <span class="filter-value">{{ $filters['search'] }}</span>
                </span>
            @endif
            @if($filters['role'])
                <span class="filter-item">
                    <span class="filter-label">Role:</span> 
                    <span class="filter-value">{{ ucfirst($filters['role']) }}</span>
                </span>
            @endif
            @if($filters['status'])
                <span class="filter-item">
                    <span class="filter-label">Status:</span> 
                    <span class="filter-value">{{ ucfirst($filters['status']) }}</span>
                </span>
            @endif
            @if($filters['date_from'])
                <span class="filter-item">
                    <span class="filter-label">From:</span> 
                    <span class="filter-value">{{ \Carbon\Carbon::parse($filters['date_from'])->format('M d, Y') }}</span>
                </span>
            @endif
            @if($filters['date_to'])
                <span class="filter-item">
                    <span class="filter-label">To:</span> 
                    <span class="filter-value">{{ \Carbon\Carbon::parse($filters['date_to'])->format('M d, Y') }}</span>
                </span>
            @endif
        </div>
        @endif
        
        <!-- Summary Section -->
        @if($users->count() > 0)
        <div class="summary-section">
            <div class="summary-title">📊 Report Summary</div>
            <div class="summary-stats">
                <div class="stat-item">
                    <div class="stat-number">{{ $users->count() }}</div>
                    <div class="stat-label">Total Users</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ $users->where('status', 'active')->count() }}</div>
                    <div class="stat-label">Active Users</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ $users->where('status', 'inactive')->count() }}</div>
                    <div class="stat-label">Inactive Users</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ $users->where('role', 'admin')->count() }}</div>
                    <div class="stat-label">Admin Users</div>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Users Data -->
        @if($users->count() > 0)
            <table class="users-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">ID</th>
                        <th style="width: 30%;">User</th>
                        <th style="width: 25%;">Email</th>
                        <th style="width: 10%;">Role</th>
                        <th style="width: 10%;">Status</th>
                        <th style="width: 10%;">Created</th>
                        <th style="width: 10%;">Updated</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>
                            <div class="user-info">
                                <div class="user-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                <div class="user-name">{{ $user->name }}</div>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge {{ $user->role }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $user->status }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="date-info">
                                <div class="date-main">{{ $user->created_at->format('M d, Y') }}</div>
                                <div class="date-time">{{ $user->created_at->format('g:i A') }}</div>
                            </div>
                        </td>
                        <td>
                            <div class="date-info">
                                <div class="date-main">{{ $user->updated_at->format('M d, Y') }}</div>
                                <div class="date-time">{{ $user->updated_at->format('g:i A') }}</div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state">
                <div class="empty-icon">👥</div>
                <div class="empty-title">No Users Found</div>
                <div class="empty-subtitle">No users match the specified criteria</div>
            </div>
        @endif
        
        <!-- Report Footer -->
        <div class="report-footer">
            <div class="footer-left">
                <div><span class="footer-label">Generated By:</span> {{ Auth::user()->name }}</div>
                <div><span class="footer-label">Generated On:</span> {{ now()->format('F d, Y \a\t g:i A') }}</div>
            </div>
            <div class="footer-right">
                <div><span class="footer-label">Total Records:</span> {{ $users->count() }}</div>
                <div><span class="footer-label">Report Version:</span> 1.0</div>
            </div>
        </div>
    </div>
    
    <!-- Auto Print Script -->
    <script>
        window.addEventListener('load', function() {
            // Small delay to ensure all content is loaded
            setTimeout(function() {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>