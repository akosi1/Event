<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events Summary Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'Times New Roman', Times, serif; 
            padding: 20px; 
            background: #fff; 
            color: #000;
        }
        
        .print-container { max-width: 1200px; margin: 0 auto; }
        
        @page { 
            margin: 15mm; 
            size: auto;
        }
        
        @media print {
            body { padding: 0 !important; }
            .no-print { display: none !important; }
            .events-table thead { display: table-header-group; }
            .events-table tr { page-break-inside: avoid; }
            
            /* Remove browser print headers/footers */
            @page { 
                margin-top: 0;
                margin-bottom: 0;
            }
            
            body {
                margin: 15mm;
            }
        }
        
        /* Hide browser default headers and footers */
        body::before,
        body::after {
            display: none !important;
        }

        /* Header */
        .official-header { 
            text-align: center; 
            border-bottom: 2px solid #000; 
            padding-bottom: 15px; 
            margin-bottom: 20px; 
        }
        
        .header-logos { 
            display: flex; 
            justify-content: space-between; 
            align-items: flex-start; 
            margin-bottom: 10px; 
        }
        
        .logo-left, .logo-right { 
            width: 80px; 
            height: 80px; 
        }
        
        .logo-left img, .logo-right img { 
            width: 100%; 
            height: 100%; 
            object-fit: contain; 
        }
        
        .header-text { 
            flex: 1; 
            padding: 0 20px; 
        }
        
        .header-text p { 
            font-size: 11px; 
            line-height: 1.3; 
            margin: 2px 0; 
        }
        
        .header-text h1 { 
            font-size: 13px; 
            font-weight: bold; 
            margin: 5px 0; 
            text-transform: uppercase; 
        }
        
        .header-text .address { 
            font-size: 10px; 
            font-style: italic; 
        }
        
        .office-title { 
            text-align: center; 
            font-size: 12px; 
            font-weight: bold; 
            margin: 15px 0 10px 0; 
            text-transform: uppercase; 
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }

        .document-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin: 20px 0;
            text-transform: uppercase;
        }

        /* Summary Box */
        .summary-box {
            border: 2px solid #000;
            padding: 15px;
            margin: 20px 0;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            text-align: center;
        }

        .summary-item {
            border: 1px solid #000;
            padding: 10px;
        }

        .summary-label {
            font-size: 10px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 24px;
            font-weight: bold;
        }

        /* Table */
        .table-title { 
            font-size: 13px; 
            font-weight: bold; 
            margin: 20px 0 10px 0; 
            text-transform: uppercase;
        }
        
        .events-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px;
        }
        
        .events-table th, 
        .events-table td { 
            border: 1px solid #000; 
            padding: 8px; 
            text-align: left; 
            font-size: 11px;
        }
        
        .events-table thead { 
            background: #f0f0f0;
        }
        
        .events-table th { 
            font-weight: bold; 
            text-transform: uppercase;
            font-size: 10px;
        }
        
        .events-table tbody tr:nth-child(even) {
            background: #fafafa;
        }

        .event-title { 
            font-weight: bold; 
            margin-bottom: 2px; 
        }
        
        .event-description { 
            font-size: 9px; 
            color: #666;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .status-active { 
            font-weight: bold; 
        }
        
        .status-postponed { 
            font-style: italic;
            color: #856404;
        }
        
        .status-cancelled { 
            font-style: italic;
            color: #721c24;
            text-decoration: line-through;
        }

        /* Signature Section */
        .signature-section { 
            margin-top: 40px; 
            page-break-inside: avoid; 
        }
        
        .signature-label { 
            font-size: 11px; 
            font-weight: bold; 
            margin-bottom: 30px; 
        }
        
        .signature-line { 
            border-top: 1px solid #000; 
            width: 250px; 
            margin-bottom: 5px; 
        }
        
        .signature-name { 
            font-weight: bold; 
            font-size: 12px; 
        }
        
        .signature-title { 
            font-size: 11px; 
            margin-top: 2px;
        }

        /* Footer */
        .print-footer { 
            margin-top: 30px; 
            padding-top: 15px; 
            border-top: 1px solid #000; 
            text-align: center; 
            font-size: 9px; 
            page-break-inside: avoid; 
        }

        /* Print Button */
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #000;
            color: #fff;
            border: none;
            cursor: pointer;
            font-size: 14px;
            border-radius: 5px;
            z-index: 1000;
        }

        .print-btn:hover {
            background: #333;
        }
        
        /* Print Instructions */
        .print-instructions {
            position: fixed;
            top: 70px;
            right: 20px;
            background: #fff3cd;
            border: 2px solid #ffc107;
            padding: 15px;
            border-radius: 8px;
            max-width: 300px;
            font-size: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .print-instructions h4 {
            font-size: 14px;
            margin-bottom: 10px;
            color: #856404;
        }
        
        .print-instructions ol {
            margin: 0;
            padding-left: 20px;
        }
        
        .print-instructions li {
            margin-bottom: 5px;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="printDocument()">🖨️ Print Document</button>
    
    <!-- <div class="print-instructions no-print">
        <h4>📋 Before Printing:</h4>
        <ol>
            <li>Click "More settings"</li>
            <li><strong>Uncheck</strong> "Headers and footers"</li>
            <li>Click Print</li>
        </ol>
    </div> -->
    
    <script>
        function printDocument() {
            // Try to hide headers/footers programmatically
            const style = document.createElement('style');
            style.textContent = `
                @page { 
                    margin: 0;
                    size: auto;
                }
                body { 
                    margin: 15mm;
                }
            `;
            document.head.appendChild(style);
            
            // Trigger print
            window.print();
        }
    </script>

    <div class="print-container">
        <!-- Official Header -->
        <div class="official-header">
            <div class="header-logos">
                <div class="logo-left">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo">
                </div>
                <div class="header-text">
                    <p>Republic of the Philippines</p>
                    <p>Region VII, Central Visayas</p>
                    <p>Municipality of Madridejos</p>
                    <h1>MADRIDEJOS COMMUNITY COLLEGE</h1>
                    <p class="address">Crossing Bunakan, Madridejos, Cebu</p>
                </div>
                <div class="logo-right">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo">
                </div>
            </div>
        </div>

        <div class="office-title">OFFICE OF THE COLLEGE PRESIDENT</div>

        <div class="document-title">Events Summary Report</div>

        <!-- Summary Box -->
        <div class="summary-box">
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="summary-label">Total Events</div>
                    <div class="summary-value">{{ $summaryData['total_events'] }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Active</div>
                    <div class="summary-value">{{ $summaryData['active_count'] }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Postponed</div>
                    <div class="summary-value">{{ $summaryData['postponed_count'] }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Cancelled</div>
                    <div class="summary-value">{{ $summaryData['cancelled_count'] }}</div>
                </div>
            </div>
        </div>

        <!-- Events Table -->
        <h2 class="table-title">Detailed Events List</h2>
        <table class="events-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 20%;">Event Title</th>
                    <th style="width: 25%;">Description</th>
                    <th style="width: 15%;">Date & Time</th>
                    <th style="width: 15%;">Location</th>
                    <th style="width: 10%;">Status</th>
                    <th style="width: 10%;">Participants</th>
                </tr>
            </thead>
            <tbody>
                @forelse($summaryData['events'] as $index => $event)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div class="event-title">{{ $event->title }}</div>
                        @if($event->department)
                            <small style="color: #666;">{{ $event->department }}</small>
                        @endif
                    </td>
                    <td>
                        <div class="event-description">{{ $event->description }}</div>
                    </td>
                    <td>
                        <strong>{{ $event->date->format('M d, Y') }}</strong><br>
                        @if($event->start_time)
                            <small>{{ $event->start_time->format('h:i A') }}
                            @if($event->end_time) - {{ $event->end_time->format('h:i A') }}@endif
                            </small>
                        @endif
                    </td>
                    <td>{{ $event->location }}</td>
                    <td>
                        <span class="status-{{ $event->status }}">
                            @if($event->status === 'active')
                                ✓ ACTIVE
                            @elseif($event->status === 'postponed')
                                ⧗ POSTPONED
                            @elseif($event->status === 'cancelled')
                                ✕ CANCELLED
                            @else
                                {{ strtoupper($event->status) }}
                            @endif
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <strong>{{ $event->joinedUsers->count() }}</strong>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="empty-state">
                        No events found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Signature Section -->
        <div class="signature-section">
            <p class="signature-label">Prepared by:</p>
            <div style="margin-top: 50px;">
                <div class="signature-line"></div>
                <p class="signature-name">_____________________</p>
                <p class="signature-title">College President</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="print-footer">
            <p>This is a computer-generated document.</p>
            <p>© {{ date('Y') }} Madridejos Community College Event Management System</p>
            <p style="margin-top: 5px;">Generated: {{ $summaryData['generated_at'] }}</p>
        </div>
    </div>
</body>
</html>