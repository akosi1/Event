<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Join Requests Summary</title>
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
            .records-table thead { display: table-header-group; }
            .records-table tr { page-break-inside: avoid; }
            
            @page { 
                margin-top: 0;
                margin-bottom: 0;
            }
            
            body {
                margin: 15mm;
            }
        }
        
        body::before,
        body::after {
            display: none !important;
        }

        /* Header */
        .official-header { 
            text-align: center; 
            padding-bottom: 10px; 
            margin-bottom: 15px; 
        }
        
        .header-logos { 
            display: flex; 
            justify-content: center;
            align-items: center;
            gap: 15px;
            margin-bottom: 5px; 
        }
        
        .logo-left, .logo-right { 
            width: 60px; 
            height: 60px;
            flex-shrink: 0;
        }
        
        .logo-left img, .logo-right img { 
            width: 100%; 
            height: 100%; 
            object-fit: contain; 
        }
        
        .header-text { 
            text-align: center;
        }
        
        .header-text p { 
            font-size: 11px; 
            line-height: 1.2; 
            margin: 1px 0; 
        }
        
        .header-text h1 { 
            font-size: 13px; 
            font-weight: bold; 
            margin: 3px 0; 
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
            margin: 10px 0; 
            text-transform: uppercase; 
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }

        .document-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin: 15px 0;
            text-transform: uppercase;
        }

        /* Table */
        .table-title { 
            font-size: 13px; 
            font-weight: bold; 
            margin: 20px 0 10px 0; 
            text-transform: uppercase;
        }
        
        .records-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 0;
        }
        
        .records-table th, 
        .records-table td { 
            border: 1px solid #000; 
            padding: 8px; 
            text-align: left; 
            font-size: 11px;
        }
        
        .records-table thead { 
            background: #f0f0f0;
        }
        
        .records-table th { 
            font-weight: bold; 
            text-transform: uppercase;
            font-size: 10px;
        }
        
        .records-table tbody tr:nth-child(even) {
            background: #fafafa;
        }

        .records-table tfoot {
            background: #f0f0f0;
            font-weight: bold;
        }

        .records-table tfoot td {
            font-size: 11px;
            font-weight: bold;
        }

        .user-info, .event-info { 
            display: flex; 
            flex-direction: column; 
        }
        
        .user-name, .event-title { 
            font-weight: bold; 
            margin-bottom: 2px; 
        }
        
        .user-email, .event-date { 
            font-size: 9px; 
            color: #666; 
        }
        
        .status-approved { 
            font-weight: bold; 
        }
        
        .status-pending { 
            font-style: italic; 
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
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="printDocument()">🖨️ Print Document</button>
    
    <script>
        function printDocument() {
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
            window.print();
        }
    </script>

    <div class="print-container">
        <!-- Official Header -->
        <div class="official-header">
            <div class="header-logos">
                <div class="logo-left">
                    <img src="{{ $summaryData['logo_left_path'] }}" alt="MCC Logo">
                </div>
                <div class="header-text">
                    <p>Republic of the Philippines</p>
                    <p>Region VII, Central Visayas</p>
                    <p>Commission on Higher Education</p>
                    <h1>MADRIDEJOS COMMUNITY COLLEGE</h1>
                    <p class="address">Crossing Bunakan, Madridejos, Cebu</p>
                </div>
                <div class="logo-right">
                    <img src="{{ $summaryData['logo_right_path'] }}" alt="Madridejos Seal">
                </div>
            </div>
        </div>

        <div class="document-title">Event Join Requests Summary</div>

        <!-- Records Table -->
        <table class="records-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 25%;">User Information</th>
                    <th style="width: 25%;">Event Details</th>
                    <th style="width: 20%;">Joined Date</th>
                    <th style="width: 15%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($summaryData['event_joins'] as $index => $join)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div class="user-info">
                            <span class="user-name">{{ $join->user->first_name }} {{ $join->user->last_name }}</span>
                            <span class="user-email">{{ $join->user->email }}</span>
                        </div>
                    </td>
                    <td>
                        <div class="event-info">
                            <span class="event-title">{{ $join->event->title ?? 'N/A' }}</span>
                            <span class="event-date">{{ $join->event->date?->format('M d, Y') ?? 'No date' }}</span>
                        </div>
                    </td>
                    <td>{{ $join->joined_at->format('M d, Y h:i A') }}</td>
                    <td>
                        @if($join->approved)
                            <span class="status-approved">✓ APPROVED</span>
                        @else
                            <span class="status-pending">⧗ PENDING</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 30px;">
                        No records found
                    </td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align: right; padding-right: 15px;">TOTAL REQUESTS:</td>
                    <td style="text-align: center;">{{ $summaryData['total_records'] }}</td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: right; padding-right: 15px;">APPROVED:</td>
                    <td style="text-align: center;">{{ $summaryData['approved_count'] }}</td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: right; padding-right: 15px;">PENDING:</td>
                    <td style="text-align: center;">{{ $summaryData['pending_count'] }}</td>
                </tr>
            </tfoot>
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