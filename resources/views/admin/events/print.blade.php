<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events Summary Report</title>
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
            #signatureCanvas, #uploadedSignature { max-width: 300px !important; max-height: 100px !important; }
            .calendar-card:hover { transform: none; }
        }

        /* Header */
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

        /* Summary Bar */
        .summary-info-bar { background: #f8f9fa; border: 1px solid #ddd; border-radius: 8px; padding: 15px 20px; margin-bottom: 30px; display: flex; justify-content: space-between; flex-wrap: wrap; gap: 15px; }
        .summary-info-item { display: flex; align-items: center; gap: 10px; }
        .summary-info-label { font-size: 13px; color: #666; font-weight: 500; }
        .summary-info-value { font-size: 18px; font-weight: 700; color: #667eea; }

        /* Events Grid */
        .section-divider { height: 2px; background: #000; margin: 30px 0; }
        .events-calendar-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .calendar-card { background: white; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); overflow: hidden; transition: transform 0.2s, box-shadow 0.2s; border: 1px solid #e0e0e0; }
        .calendar-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.15); }

        /* Calendar Date Section */
        .calendar-date-section { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; position: relative; }
        .calendar-date-section.status-postponed { background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); color: #2c3e50; }
        .calendar-date-section.status-cancelled { background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%); color: #2c3e50; }
        .calendar-month { font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; }
        .calendar-day { font-size: 48px; font-weight: 700; line-height: 1; margin-bottom: 5px; }
        .calendar-year { font-size: 14px; font-weight: 500; opacity: 0.9; }
        .calendar-time { margin-top: 10px; font-size: 13px; font-weight: 500; padding: 5px 10px; background: rgba(255,255,255,0.2); border-radius: 20px; display: inline-block; }
        .calendar-time i { margin-right: 3px; }
        .event-number { position: absolute; top: 10px; right: 10px; background: rgba(255,255,255,0.3); color: white; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; }
        .status-cancelled .event-number, .status-postponed .event-number { color: #2c3e50; }

        /* Card Content */
        .calendar-content { padding: 20px; }
        .event-card-title { font-size: 16px; font-weight: 700; color: #2c3e50; margin-bottom: 10px; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .event-card-description { font-size: 13px; color: #666; line-height: 1.5; margin-bottom: 15px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
        .event-card-location { font-size: 12px; color: #667eea; display: flex; align-items: center; gap: 5px; margin-bottom: 10px; }
        .event-card-location i { font-size: 14px; }
        .event-card-meta { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 10px; }
        .event-card-footer { display: flex; justify-content: space-between; align-items: center; padding-top: 15px; border-top: 1px solid #e0e0e0; }
        .participants-badge { display: flex; align-items: center; gap: 5px; font-size: 12px; color: #667eea; font-weight: 600; }
        .participants-badge i { font-size: 14px; }

        /* Badges */
        .badge { display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-active { background: #d4edda; color: #155724; }
        .badge-postponed { background: #fff3cd; color: #856404; }
        .badge-cancelled { background: #f8d7da; color: #721c24; }
        .badge-recurring { background: #d1ecf1; color: #0c5460; }
        .badge-exclusive { background: #fff3cd; color: #856404; }
        .badge-open { background: #d4edda; color: #155724; }

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
        .empty-state { text-align: center; padding: 80px 20px; color: #999; }
        .empty-state h3 { font-size: 24px; margin-bottom: 10px; color: #666; }

        /* Icons */
        .icon-location::before { content: "\f3c5"; font-family: "Font Awesome 6 Free"; font-weight: 900; margin-right: 5px; }
        .icon-users::before { content: "\f0c0"; font-family: "Font Awesome 6 Free"; font-weight: 900; margin-right: 5px; }
        .icon-calendar::before { content: "\f073"; font-family: "Font Awesome 6 Free"; font-weight: 900; margin-right: 5px; }
    </style>
</head>
<body>
    <div class="print-container">
        <!-- Header -->
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

        <!-- Summary -->
        <div class="summary-info-bar">
            <div class="summary-info-item">
                <span class="summary-info-label">Total Events:</span>
                <span class="summary-info-value">{{ $summaryData['total_events'] }}</span>
            </div>
            <div class="summary-info-item">
                <span class="summary-info-label">Active:</span>
                <span class="summary-info-value" style="color: #28a745;">{{ $summaryData['active_count'] }}</span>
            </div>
            <div class="summary-info-item">
                <span class="summary-info-label">Postponed:</span>
                <span class="summary-info-value" style="color: #ffc107;">{{ $summaryData['postponed_count'] }}</span>
            </div>
            <div class="summary-info-item">
                <span class="summary-info-label">Cancelled:</span>
                <span class="summary-info-value" style="color: #dc3545;">{{ $summaryData['cancelled_count'] }}</span>
            </div>
        </div>

        <div class="section-divider"></div>

        <!-- Events -->
        @if($summaryData['events']->count() > 0)
        <div class="events-calendar-grid">
            @foreach($summaryData['events'] as $index => $event)
            <div class="calendar-card">
                <div class="calendar-date-section status-{{ $event->status }}">
                    <div class="event-number">#{{ $index + 1 }}</div>
                    <div class="calendar-month">{{ $event->date->format('F') }}</div>
                    <div class="calendar-day">{{ $event->date->format('d') }}</div>
                    <div class="calendar-year">{{ $event->date->format('Y') }}</div>
                    @if($event->start_time)
                    <div class="calendar-time">
                        <i class="fas fa-clock"></i>
                        {{ $event->start_time->format('h:i A') }}
                        @if($event->end_time) - {{ $event->end_time->format('h:i A') }} @endif
                    </div>
                    @endif
                </div>
                <div class="calendar-content">
                    <h3 class="event-card-title">{{ $event->title }}</h3>
                    <p class="event-card-description">{{ $event->description }}</p>
                    <div class="event-card-location">
                        <i class="fas fa-map-marker-alt"></i> {{ $event->location }}
                    </div>
                    <div class="event-card-meta">
                        <span class="badge badge-{{ $event->status }}">{{ ucfirst($event->status) }}</span>
                        @if($event->is_exclusive)
                            <span class="badge badge-exclusive">Exclusive</span>
                        @else
                            <span class="badge badge-open">Open to All</span>
                        @endif
                        @if($event->is_recurring)
                            <span class="badge badge-recurring">Recurring</span>
                        @endif
                    </div>
                    <div class="event-card-footer">
                        @if($event->department)
                        <div style="font-size: 11px; color: #666; font-weight: 600;">{{ $event->department }}</div>
                        @else
                        <div></div>
                        @endif
                        <div class="participants-badge">
                            <i class="fas fa-users"></i> {{ $event->joinedUsers->count() }}
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-state">
            <div style="font-size: 64px; margin-bottom: 20px;">📅</div>
            <h3>No Events Found</h3>
            <p>There are no events to display in this calendar.</p>
        </div>
        @endif

        <!-- Signature -->
        <div class="signature-section">
            <p class="signature-label">Signed by:</p>
            
            <div class="signature-mode-toggle no-print">
                <button class="mode-btn active" onclick="switchMode('draw')" id="drawModeBtn">
                    <i class="fas fa-pen"></i> Draw Signature
                </button>
                <button class="mode-btn" onclick="switchMode('upload')" id="uploadModeBtn">
                    <i class="fas fa-upload"></i> Upload Signature
                </button>
            </div>

            <div id="drawMode" class="draw-mode" style="display: block;">
                <div class="signature-controls no-print">
                    <button class="btn btn-clear" onclick="clearSignature()">
                        <i class="fas fa-eraser"></i> Clear
                    </button>
                    <button class="btn btn-print" onclick="printDoc()">
                        <i class="fas fa-print"></i> Print
                    </button>
                </div>
                <div class="signature-box">
                    <canvas id="signatureCanvas" width="300" height="100"></canvas>
                </div>
            </div>

            <div id="uploadMode" class="draw-mode">
                <div class="signature-controls no-print">
                    <button class="btn btn-clear" onclick="removeSignature()">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                    <button class="btn btn-print" onclick="printDoc()">
                        <i class="fas fa-print"></i> Print
                    </button>
                </div>
                <div class="signature-upload-area" id="uploadArea" onclick="document.getElementById('signatureFile').click()">
                    <div class="signature-upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                    <p style="margin: 0; font-size: 14px; color: #666;">
                        <strong>Click to upload signature</strong><br>
                        <small>PNG, JPG or GIF (max 2MB)</small>
                    </p>
                    <div class="signature-preview" id="signaturePreview">
                        <img id="uploadedSignature" alt="Signature">
                    </div>
                </div>
                <input type="file" id="signatureFile" accept="image/*" style="display: none;" onchange="handleUpload(event)">
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
        let mode = 'draw', uploadedSig = null;
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

        // Switch mode
        function switchMode(m) {
            mode = m;
            document.getElementById('drawModeBtn').classList.toggle('active', m === 'draw');
            document.getElementById('uploadModeBtn').classList.toggle('active', m === 'upload');
            document.getElementById('drawMode').style.display = m === 'draw' ? 'block' : 'none';
            document.getElementById('uploadMode').style.display = m === 'upload' ? 'block' : 'none';
        }

        // Drawing
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

        // Upload
        function handleUpload(e) {
            const file = e.target.files[0];
            if (!file) return;
            if (file.size > 2 * 1024 * 1024) return alert('File too large (max 2MB)');
            if (!file.type.match('image.*')) return alert('Please upload an image');

            const reader = new FileReader();
            reader.onload = function(e) {
                uploadedSig = e.target.result;
                const img = document.getElementById('uploadedSignature');
                img.src = uploadedSig;
                img.style.display = 'block';
                document.getElementById('signaturePreview').style.display = 'block';
                document.getElementById('uploadArea').classList.add('has-signature');
            };
            reader.readAsDataURL(file);
        }

        function removeSignature() {
            uploadedSig = null;
            document.getElementById('uploadedSignature').src = '';
            document.getElementById('uploadedSignature').style.display = 'none';
            document.getElementById('signaturePreview').style.display = 'none';
            document.getElementById('uploadArea').classList.remove('has-signature');
            document.getElementById('signatureFile').value = '';
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
            
            if (mode === 'upload' && uploadedSig) {
                canvas.style.display = 'none';
                document.getElementById('uploadedSignature').style.display = 'block';
            } else {
                document.getElementById('uploadedSignature').style.display = 'none';
            }
            
            window.print();
            
            setTimeout(() => {
                if (mode === 'upload') canvas.style.display = 'none';
                else {
                    canvas.style.display = 'block';
                    document.getElementById('uploadedSignature').style.display = 'none';
                }
            }, 100);
        }
    </script>
</body>
</html>