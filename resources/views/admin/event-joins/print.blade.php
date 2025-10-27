<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Join Requests Summary</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { font-family: 'Times New Roman', Times, serif; padding: 40px; background: #fff; }
        
        .print-container { max-width: 1200px; margin: 0 auto; }
        
        @page { margin: 0; size: auto; }
        
        @media print {
            html, body { margin: 0 !important; padding: 20mm !important; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
            #signerNameInput, #signerTitleInput { display: none !important; }
            #signerNamePrint, #signerTitlePrint { display: inline-block !important; }
            .signature-box { border: none !important; min-height: auto !important; padding: 0 !important; }
            .records-table thead { display: table-header-group; }
            .records-table tr { page-break-inside: avoid; }
        }

        /* Official Header */
        .official-header { text-align: center; border-bottom: 3px solid #000; padding-bottom: 15px; margin-bottom: 30px; }
        .header-logos { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px; }
        .logo-left, .logo-right { width: 100px; height: 100px; }
        .logo-left img, .logo-right img { width: 100%; height: 100%; object-fit: contain; }
        .header-text { flex: 1; padding: 0 20px; }
        .header-text p { font-size: 11px; line-height: 1.4; margin: 2px 0; }
        .header-text h1 { font-size: 14px; font-weight: bold; margin: 8px 0 4px 0; text-transform: uppercase; letter-spacing: 0.5px; }
        .header-text .address { font-size: 10px; font-style: italic; margin-top: 4px; }
        .office-title { text-align: center; font-size: 13px; font-weight: bold; margin: 20px 0; text-transform: uppercase; letter-spacing: 1px; }
        .document-info { text-align: center; font-size: 14px; margin-bottom: 20px; }

        /* Summary Stats */
        .summary-stats { display: flex; gap: 20px; margin-bottom: 30px; }
        .stat-card { flex: 1; padding: 20px; border-radius: 8px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .stat-card.total { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .stat-card.approved { background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); color: #2c3e50; }
        .stat-card.pending { background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); color: #2c3e50; }
        .stat-number { font-size: 36px; font-weight: 700; margin-bottom: 5px; }
        .stat-label { font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }

        /* Table */
        .section-divider { height: 2px; background: #000; margin: 30px 0; }
        .table-title { font-size: 20px; font-weight: 700; color: #2c3e50; margin-bottom: 15px; padding-left: 10px; border-left: 4px solid #667eea; }
        .records-table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden; }
        .records-table thead { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .records-table th { padding: 15px; text-align: left; font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
        .records-table tbody tr { border-bottom: 1px solid #e0e0e0; transition: background 0.2s; }
        .records-table tbody tr:hover { background: #f8f9fa; }
        .records-table tbody tr:last-child { border-bottom: none; }
        .records-table td { padding: 12px 15px; font-size: 13px; color: #2c3e50; }
        .user-info, .event-info { display: flex; flex-direction: column; }
        .user-name, .event-title { font-weight: 600; color: #2c3e50; margin-bottom: 3px; }
        .user-email, .event-date { font-size: 12px; color: #666; }
        .status-badge { display: inline-block; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .status-approved { background: #d4edda; color: #155724; }
        .status-pending { background: #fff3cd; color: #856404; }

        /* Signature */
        .signature-section { margin-top: 80px; page-break-inside: avoid; }
        .signature-label { font-size: 12px; font-weight: bold; margin-bottom: 5px; }
        .signature-controls { display: flex; gap: 10px; margin-bottom: 15px; }
        .btn { padding: 8px 16px; border: none; border-radius: 5px; cursor: pointer; font-size: 12px; font-weight: 600; transition: all 0.3s; }
        .btn-clear { background: #dc3545; color: white; }
        .btn-clear:hover { background: #c82333; }
        .btn-print { background: #28a745; color: white; }
        .btn-print:hover { background: #218838; }
        .signature-box { border: none; min-height: 100px; margin-bottom: 0; background: #fff; position: relative; }
        .signature-box canvas { display: block; cursor: crosshair; border: none; }
        .signature-name { text-align: left; margin-top: 0; }
        .signature-line { border-top: 2px solid #000; width: 300px; margin-bottom: 5px; margin-top: 5px; }
        .signature-name-text { font-weight: bold; font-size: 13px; margin: 2px 0; }
        .signature-title-text { font-size: 12px; margin: 2px 0; line-height: 1.3; }
        #signerNameInput, #signerTitleInput { border: none; outline: none; background: transparent; width: 300px; padding: 2px 0; }
        #signerNameInput { font-weight: bold; font-size: 13px; }
        #signerTitleInput { font-size: 12px; }
        #signerNamePrint, #signerTitlePrint { display: none; }

        /* Footer */
        .print-footer { margin-top: 40px; padding-top: 20px; border-top: 2px solid #000; text-align: center; font-size: 11px; page-break-inside: avoid; }
    </style>
</head>
<body>
    <div class="print-container">
        <!-- Official Header -->
        <div class="official-header">
            <div class="header-logos">
                <div class="logo-left"><img src="{{ $summaryData['left_logo'] }}" alt="Left Logo"></div>
                <div class="header-text">
                    <p>Republic of the Philippines</p>
                    <p>Region VII, Central Visayas</p>
                    <p>Municipality of Madridejos</p>
                    <h1>MADRIDEJOS COMMUNITY COLLEGE</h1>
                    <p class="address">Crossing Bunakan, Madridejos, Cebu</p>
                </div>
                <div class="logo-right"><img src="{{ $summaryData['right_logo'] }}" alt="Right Logo"></div>
            </div>
        </div>

        <div class="office-title">OFFICE OF THE COLLEGE PRESIDENT</div>
        <div class="document-info"><p>{{ $summaryData['description'] }}</p></div>

        <!-- Summary Stats -->
        <div class="summary-stats">
            <div class="stat-card total">
                <div class="stat-number">{{ $summaryData['total_records'] }}</div>
                <div class="stat-label">Total Requests</div>
            </div>
            <div class="stat-card approved">
                <div class="stat-number">{{ $summaryData['approved_count'] }}</div>
                <div class="stat-label">Approved</div>
            </div>
            <div class="stat-card pending">
                <div class="stat-number">{{ $summaryData['pending_count'] }}</div>
                <div class="stat-label">Pending</div>
            </div>
        </div>

        <div class="section-divider"></div>

        <!-- Records Table -->
        <h2 class="table-title">Detailed Records List</h2>
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
                            <span class="status-badge status-approved"><i class="fas fa-check"></i> Approved</span>
                        @else
                            <span class="status-badge status-pending"><i class="fas fa-clock"></i> Pending</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 40px; color: #999;">
                        <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 10px; display: block;"></i>
                        No records found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Signature Section -->
        <div class="signature-section">
            <p class="signature-label">Signed by:</p>
            
            <div class="signature-controls no-print">
                <button class="btn btn-clear" onclick="clearSignature()">
                    <i class="fas fa-eraser"></i> Clear
                </button>
                <button class="btn btn-print" onclick="printDoc()">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>

            <div class="signature-box">
                <canvas id="signatureCanvas" width="300" height="80"></canvas>
            </div>

            <div class="signature-name">
                <div class="signature-line"></div>
                <div>
                    <input type="text" id="signerNameInput" class="no-print signature-name-text" placeholder="Enter Name">
                    <p class="signature-name-text" id="signerNamePrint"></p>
                </div>
                <div>
                    <input type="text" id="signerTitleInput" class="no-print signature-title-text" placeholder="Enter Title/Position">
                    <p class="signature-title-text" id="signerTitlePrint"></p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="print-footer">
            <p>This is a computer-generated document. No signature is required.</p>
            <p>© {{ date('Y') }} Event Management System. All rights reserved.</p>
            <p style="margin-top: 10px;">Generated: {{ $summaryData['generated_at'] }}</p>
        </div>
    </div>

    <script>
        const canvas = document.getElementById('signatureCanvas');
        const ctx = canvas.getContext('2d');
        let drawing = false, lastX = 0, lastY = 0;

        // Init canvas
        ctx.fillStyle = 'white';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.strokeStyle = '#000';
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';

        // Get position
        function getPos(e) {
            const rect = canvas.getBoundingClientRect();
            return {
                x: (e.clientX || e.touches[0].clientX) - rect.left,
                y: (e.clientY || e.touches[0].clientY) - rect.top
            };
        }

        function startDraw(e) {
            drawing = true;
            const pos = getPos(e);
            lastX = pos.x;
            lastY = pos.y;
        }

        function draw(e) {
            if (!drawing) return;
            e.preventDefault();
            const pos = getPos(e);
            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
            lastX = pos.x;
            lastY = pos.y;
        }

        function stopDraw() { drawing = false; }

        // Events
        canvas.addEventListener('mousedown', startDraw);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDraw);
        canvas.addEventListener('mouseout', stopDraw);
        canvas.addEventListener('touchstart', startDraw);
        canvas.addEventListener('touchmove', draw);
        canvas.addEventListener('touchend', stopDraw);

        function clearSignature() {
            ctx.fillStyle = 'white';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
        }

        // Update fields
        document.getElementById('signerNameInput').oninput = function() {
            document.getElementById('signerNamePrint').textContent = this.value;
        };
        document.getElementById('signerTitleInput').oninput = function() {
            document.getElementById('signerTitlePrint').textContent = this.value;
        };

        // Print
        function printDoc() {
            document.getElementById('signerNamePrint').textContent = document.getElementById('signerNameInput').value;
            document.getElementById('signerTitlePrint').textContent = document.getElementById('signerTitleInput').value;
            window.print();
        }
    </script>
</body>
</html>