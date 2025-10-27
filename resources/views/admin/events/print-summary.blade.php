<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Receipt - MCC Event & Portfolio Organizer</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            body { margin: 0; color: #000000 !important; }
            .no-print { display: none !important; }
            .receipt-container { box-shadow: none !important; margin: 0 !important; }
            .print-btn { display: none !important; }
            * { color: #000000 !important; }
            .receipt-header * { color: white !important; }
            .calendar-day-header { color: white !important; }
            .event-date-box * { color: white !important; }
            .status-badge { color: inherit !important; }
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Georgia', 'Times New Roman', serif;
            background: #f5f5f5;
            color: #000000;
            line-height: 1.6;
            display: flex;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        
        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            width: 100%;
        }
        
        .receipt-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }
        
        .receipt-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
        }
        
        .receipt-header > * {
            position: relative;
            z-index: 1;
        }
        
        .company-logo {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .company-name {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 5px;
            color: white !important;
        }
        
        .company-subtitle {
            font-size: 1rem;
            opacity: 0.9;
            margin-bottom: 15px;
            color: white !important;
        }
        
        .receipt-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.2);
        }
        
        .receipt-number {
            font-size: 0.9rem;
            opacity: 0.9;
            color: white !important;
        }
        
        .receipt-date {
            font-size: 0.9rem;
            opacity: 0.9;
            color: white !important;
        }
        
        .receipt-body {
            padding: 40px;
        }
        
        .calendar-section {
            margin-bottom: 40px;
        }
        
        .section-title {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            font-size: 1.4rem;
            font-weight: 700;
            color: #000000 !important;
            text-align: center;
        }
        
        .section-title i {
            margin-right: 10px;
            font-size: 1.1rem;
            color: #007bff !important;
        }
        
        .calendar-container {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 20px;
            background: #f8f9fa;
        }
        
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .calendar-title {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .calendar-month {
            font-size: 1.2rem;
            font-weight: 600;
            color: #000000;
        }
        
        .calendar-year {
            color: #000000;
            font-size: 1rem;
        }
        
        .calendar-controls {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .calendar-nav-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .calendar-nav-btn:hover {
            background: #0056b3;
            transform: translateY(-1px);
        }
        
        .calendar-nav-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
        }
        
        .calendar-selector {
            display: flex;
            gap: 8px;
        }
        
        .calendar-select {
            padding: 6px 10px;
            border: 2px solid #e9ecef;
            border-radius: 6px;
            font-size: 0.9rem;
            background: white;
            cursor: pointer;
            transition: border-color 0.3s ease;
        }
        
        .calendar-select:focus {
            outline: none;
            border-color: #007bff;
        }
        
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 2px;
            margin-bottom: 10px;
        }
        
        .calendar-day-header {
            background: #007bff;
            color: white;
            padding: 8px;
            text-align: center;
            font-weight: 600;
            font-size: 0.8rem;
            border-radius: 4px;
        }
        
        .calendar-day {
            background: white;
            border: 1px solid #e9ecef;
            padding: 8px;
            text-align: center;
            min-height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            position: relative;
        }
        
        .calendar-day.other-month {
            color: #ced4da;
            background: #f8f9fa;
        }
        
        .calendar-day.event-day {
            background: #007bff;
            color: white;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(0,123,255,0.3);
        }
        
        .calendar-day.multiple-events {
            background: #dc3545;
            color: white;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(220,53,69,0.3);
        }
        
        .calendar-day.event-day::after {
            content: '•';
            position: absolute;
            bottom: 2px;
            right: 4px;
            font-size: 0.8rem;
        }
        
        .calendar-day.today {
            border: 2px solid #28a745;
            font-weight: 600;
        }
        
        .events-list {
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .event-item {
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            align-items: flex-start;
            gap: 20px;
        }
        
        .event-item:last-child {
            border-bottom: none;
        }
        
        .event-date-box {
            background: #007bff;
            color: white;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            min-width: 80px;
            flex-shrink: 0;
        }
        
        .event-day {
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1;
        }
        
        .event-month {
            font-size: 0.8rem;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .event-details {
            flex: 1;
        }
        
        .event-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: #000000 !important;
        }
        
        .event-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
            margin-bottom: 12px;
        }
        
        .event-meta-item {
            display: flex;
            align-items: center;
            font-size: 1rem;
            color: #000000 !important;
            font-weight: 500;
        }
        
        .event-meta-item i {
            margin-right: 8px;
            width: 16px;
            color: #007bff !important;
        }
        
        .event-description {
            font-size: 1rem;
            color: #000000 !important;
            line-height: 1.5;
            margin-top: 10px;
            font-weight: 400;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        
        .status-postponed {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        
        .receipt-footer {
            background: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        
        .footer-logo {
            font-size: 1.5rem;
            color: #007bff;
            margin-bottom: 10px;
        }
        
        .footer-text {
            color: #000000 !important;
            font-size: 0.9rem;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #007bff;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 25px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0,123,255,0.3);
            cursor: pointer;
            z-index: 1000;
            transition: all 0.3s ease;
        }
        
        .print-btn:hover {
            background: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,123,255,0.4);
        }
        
        .no-events {
            text-align: center;
            padding: 40px;
            color: #000000;
        }
        
        .no-events i {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        .stats-section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .stat-item {
            background: white;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
            border: 1px solid #e9ecef;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: #000000 !important;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 0.9rem;
            color: #000000 !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <!-- Print Button -->
    <button onclick="window.print()" class="print-btn no-print">
        <i class="fas fa-print"></i> Print Receipt
    </button>

    <div class="receipt-container">
        <!-- Header -->
        <div class="receipt-header">
            <div class="company-logo">
                <i class="fas fa-calendar-check"></i>
            </div>
            <h1 class="company-name">MCC Event & Portfolio Organizer</h1>
            <p class="company-subtitle">Comprehensive Event Management System</p>
            
            <div class="receipt-info">
                <div class="receipt-number">
                    <i class="fas fa-hashtag"></i> Receipt #EPO-{{ date('Y') }}-{{ str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT) }}
                </div>
                <div class="receipt-date">
                    <i class="fas fa-calendar"></i> {{ now()->format('F j, Y \a\t g:i A') }}
                </div>
            </div>
        </div>

        <!-- Body -->
        <div class="receipt-body">
            <!-- Statistics Section -->
            <div class="stats-section">
                <h2 class="section-title">
                    <i class="fas fa-chart-bar"></i>
                    Event Statistics
                </h2>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-number">{{ $stats['total'] }}</div>
                        <div class="stat-label">Total Events</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">{{ $stats['upcoming'] }}</div>
                        <div class="stat-label">Upcoming</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">{{ $stats['active'] }}</div>
                        <div class="stat-label">Active</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">{{ $stats['postponed'] }}</div>
                        <div class="stat-label">Postponed</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">{{ $stats['cancelled'] }}</div>
                        <div class="stat-label">Cancelled</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">{{ $stats['recurring'] }}</div>
                        <div class="stat-label">Recurring</div>
                    </div>
                </div>
            </div>

            <!-- Calendar Section -->
            <div class="calendar-section">
                <h2 class="section-title">
                    <i class="fas fa-calendar-alt"></i>
                    Event Calendar
                </h2>
                
                <div class="calendar-container">
                    <div class="calendar-header">
                        <div class="calendar-title">
                            <div class="calendar-month" id="currentMonth">{{ now()->format('F') }}</div>
                            <div class="calendar-year" id="currentYear">{{ now()->format('Y') }}</div>
                        </div>
                        
                        <div class="calendar-controls">
                            <div class="calendar-selector">
                                <select class="calendar-select" id="monthSelect">
                                    @for($i = 0; $i < 12; $i++)
                                    <option value="{{ $i }}" {{ now()->month - 1 == $i ? 'selected' : '' }}>
                                        {{ DateTime::createFromFormat('!m', $i + 1)->format('F') }}
                                    </option>
                                    @endfor
                                </select>
                                
                                <select class="calendar-select" id="yearSelect">
                                    @for($year = now()->year - 1; $year <= now()->year + 3; $year++)
                                    <option value="{{ $year }}" {{ now()->year == $year ? 'selected' : '' }}>{{ $year }}</option>
                                    @endfor
                                </select>
                            </div>
                            
                            <div class="calendar-nav">
                                <button class="calendar-nav-btn" id="prevMonth">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button class="calendar-nav-btn" id="nextMonth">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div id="calendarGrid" class="calendar-grid">
                        <!-- Day Headers -->
                        <div class="calendar-day-header">Sun</div>
                        <div class="calendar-day-header">Mon</div>
                        <div class="calendar-day-header">Tue</div>
                        <div class="calendar-day-header">Wed</div>
                        <div class="calendar-day-header">Thu</div>
                        <div class="calendar-day-header">Fri</div>
                        <div class="calendar-day-header">Sat</div>
                    </div>
                    
                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #dee2e6;">
                        <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
                            <div style="display: flex; align-items: center; gap: 5px; font-size: 0.9rem;">
                                <div style="width: 16px; height: 16px; background: #007bff; border-radius: 3px;"></div>
                                <span>Event Day</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 5px; font-size: 0.9rem;">
                                <div style="width: 16px; height: 16px; border: 2px solid #28a745; border-radius: 3px;"></div>
                                <span>Today</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 5px; font-size: 0.9rem;">
                                <div style="width: 16px; height: 16px; background: #dc3545; border-radius: 3px;"></div>
                                <span>Multiple Events</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Events List -->
            <div class="events-section">
                <h2 class="section-title">
                    <i class="fas fa-list-ul"></i>
                    Events List
                </h2>
                
                @if($events->isEmpty())
                <div class="no-events">
                    <i class="fas fa-calendar-times"></i>
                    <h4>No Events Found</h4>
                    <p>There are no events matching the current criteria.</p>
                </div>
                @else
                <div class="events-list">
                    @foreach($events as $event)
                    <div class="event-item">
                        <div class="event-date-box">
                            <div class="event-day">{{ $event->date->format('d') }}</div>
                            <div class="event-month">{{ $event->date->format('M') }}</div>
                        </div>
                        <div class="event-details">
                            <h3 class="event-title">{{ $event->title }}</h3>
                            <div class="event-meta">
                                <div class="event-meta-item">
                                    <i class="fas fa-clock"></i>
                                    {{ $event->start_time ? $event->start_time->format('g:i A') : 'All Day' }}
                                    @if($event->end_time)
                                        - {{ $event->end_time->format('g:i A') }}
                                    @endif
                                </div>
                                <div class="event-meta-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    {{ $event->location }}
                                </div>
                                <div class="event-meta-item">
                                    <i class="fas fa-graduation-cap"></i>
                                    {{ $event->department_display }}
                                </div>
                                <div class="event-meta-item">
                                    <span class="status-badge status-{{ $event->status }}">{{ ucfirst($event->status) }}</span>
                                </div>
                            </div>
                            @if($event->description)
                            <div class="event-description">
                                {{ Str::limit($event->description, 150) }}
                            </div>
                            @endif
                            @if($event->is_recurring)
                            <div class="event-meta-item" style="margin-top: 8px;">
                                <i class="fas fa-repeat"></i>
                                {{ $event->recurrence_display }}
                            </div>
                            @endif
                            @if($event->cancel_reason && in_array($event->status, ['postponed', 'cancelled']))
                            <div class="event-meta-item" style="margin-top: 8px;">
                                <i class="fas fa-info-circle"></i>
                                <strong>Reason:</strong> {{ $event->cancel_reason }}
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <!-- Footer -->
        <div class="receipt-footer">
            <div class="footer-logo">
                <i class="fas fa-certificate"></i>
            </div>
            <div class="footer-text"><strong>MCC Event & Portfolio Organizer</strong></div>
            <div class="footer-text">Professional Event Management System</div>
            <div class="footer-text" style="margin-top: 15px; font-size: 0.8rem;">
                Generated on {{ now()->format('F j, Y \a\t g:i A') }} • Thank you for using our services
            </div>
        </div>
    </div>

    <script>
        // Convert PHP events data to JavaScript properly structured
        const eventsData = {};
        const allEventsData = [];
        
        @if($events->count() > 0)
        @foreach($events as $event)
            const eventDate = '{{ $event->date->format("Y-m-d") }}';
            const eventData = {
                id: {{ $event->id }},
                title: {!! json_encode($event->title) !!},
                description: {!! json_encode($event->description) !!},
                date: eventDate,
                start_time: '{{ $event->start_time ? $event->start_time->format("g:i A") : "All Day" }}',
                end_time: '{{ $event->end_time ? $event->end_time->format("g:i A") : "" }}',
                location: {!! json_encode($event->location) !!},
                department: '{{ $event->department ?? "All" }}',
                department_display: {!! json_encode($event->department_display) !!},
                status: '{{ $event->status }}',
                is_exclusive: {{ $event->is_exclusive ? 'true' : 'false' }},
                is_recurring: {{ $event->is_recurring ? 'true' : 'false' }},
                recurrence_display: {!! json_encode($event->recurrence_display ?? 'One-time event') !!},
                cancel_reason: {!! json_encode($event->cancel_reason) !!}
            };
            
            // Add to events by date
            if (!eventsData[eventDate]) {
                eventsData[eventDate] = [];
            }
            eventsData[eventDate].push(eventData);
            
            // Add to all events array
            allEventsData.push(eventData);
        @endforeach
        @endif

        console.log('Events Data loaded:', eventsData);
        console.log('All Events:', allEventsData);

        let currentDate = new Date();
        const monthNames = ["January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"];

        function generateCalendar(year, month) {
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const daysInMonth = lastDay.getDate();
            const startingDayOfWeek = firstDay.getDay();
            
            // Update header
            document.getElementById('currentMonth').textContent = monthNames[month];
            document.getElementById('currentYear').textContent = year;
            
            // Clear calendar grid (keep headers)
            const grid = document.getElementById('calendarGrid');
            const headers = grid.querySelectorAll('.calendar-day-header');
            grid.innerHTML = '';
            headers.forEach(header => grid.appendChild(header));
            
            // Add empty cells for days before month starts
            for (let i = 0; i < startingDayOfWeek; i++) {
                const emptyDay = document.createElement('div');
                emptyDay.className = 'calendar-day other-month';
                const prevMonth = new Date(year, month, 0);
                emptyDay.textContent = prevMonth.getDate() - startingDayOfWeek + i + 1;
                grid.appendChild(emptyDay);
            }
            
            // Add days of current month
            const today = new Date();
            for (let day = 1; day <= daysInMonth; day++) {
                const dayElement = document.createElement('div');
                dayElement.className = 'calendar-day';
                dayElement.textContent = day;
                
                const currentDateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                
                // Check if it's today
                if (year === today.getFullYear() && month === today.getMonth() && day === today.getDate()) {
                    dayElement.classList.add('today');
                }
                
                // Check if there are events on this day
                if (eventsData[currentDateStr] && eventsData[currentDateStr].length > 0) {
                    if (eventsData[currentDateStr].length > 1) {
                        dayElement.classList.add('multiple-events');
                        dayElement.title = `${eventsData[currentDateStr].length} events on this day:\n` + 
                                          eventsData[currentDateStr].map(e => `• ${e.title}`).join('\n');
                    } else {
                        dayElement.classList.add('event-day');
                        dayElement.title = eventsData[currentDateStr][0].title + '\n' + eventsData[currentDateStr][0].start_time;
                    }
                }
                
                grid.appendChild(dayElement);
            }
            
            // Add remaining days from next month
            const totalCells = grid.children.length - 7; // Subtract headers
            const remainingCells = 42 - totalCells - 7; // 6 rows * 7 days - existing cells - headers
            if (remainingCells > 0) {
                for (let day = 1; day <= remainingCells; day++) {
                    const nextMonthDay = document.createElement('div');
                    nextMonthDay.className = 'calendar-day other-month';
                    nextMonthDay.textContent = day;
                    grid.appendChild(nextMonthDay);
                }
            }
            
            // Update the events list for the current month
            updateEventsList(year, month);
        }

        function updateEventsList(year, month) {
            const eventsContainer = document.querySelector('.events-list');
            if (!eventsContainer) return;
            
            // Filter events for current month and year
            const monthEvents = allEventsData.filter(event => {
                const eventDate = new Date(event.date);
                return eventDate.getFullYear() === year && eventDate.getMonth() === month;
            });
            
            // Sort events by date
            monthEvents.sort((a, b) => new Date(a.date) - new Date(b.date));
            
            if (monthEvents.length === 0) {
                eventsContainer.innerHTML = `
                    <div class="no-events">
                        <i class="fas fa-calendar-times"></i>
                        <h4>No Events This Month</h4>
                        <p>There are no scheduled events for ${monthNames[month]} ${year}</p>
                    </div>
                `;
                return;
            }
            
            eventsContainer.innerHTML = '';
            
            monthEvents.forEach(event => {
                const eventElement = document.createElement('div');
                eventElement.className = 'event-item';
                
                const eventDate = new Date(event.date);
                const statusClass = `status-${event.status}`;
                
                const timeDisplay = event.end_time && event.end_time !== '' ? 
                    `${event.start_time} - ${event.end_time}` : event.start_time;
                
                let additionalInfo = '';
                if (event.is_recurring) {
                    additionalInfo += `
                        <div class="event-meta-item" style="margin-top: 8px;">
                            <i class="fas fa-repeat"></i>
                            ${event.recurrence_display}
                        </div>
                    `;
                }
                
                if (event.cancel_reason && (event.status === 'postponed' || event.status === 'cancelled')) {
                    additionalInfo += `
                        <div class="event-meta-item" style="margin-top: 8px;">
                            <i class="fas fa-info-circle"></i>
                            <strong>Reason:</strong> ${event.cancel_reason}
                        </div>
                    `;
                }
                
                eventElement.innerHTML = `
                    <div class="event-date-box">
                        <div class="event-day">${String(eventDate.getDate()).padStart(2, '0')}</div>
                        <div class="event-month">${monthNames[eventDate.getMonth()].substring(0, 3)}</div>
                    </div>
                    <div class="event-details">
                        <h3 class="event-title">${event.title}</h3>
                        <div class="event-meta">
                            <div class="event-meta-item">
                                <i class="fas fa-clock"></i>
                                ${timeDisplay}
                            </div>
                            <div class="event-meta-item">
                                <i class="fas fa-map-marker-alt"></i>
                                ${event.location}
                            </div>
                            <div class="event-meta-item">
                                <i class="fas fa-graduation-cap"></i>
                                ${event.department_display}
                            </div>
                            <div class="event-meta-item">
                                <span class="status-badge ${statusClass}">${event.status.charAt(0).toUpperCase() + event.status.slice(1)}</span>
                            </div>
                        </div>
                        ${event.description ? `<div class="event-description">${event.description.length > 150 ? event.description.substring(0, 150) + '...' : event.description}</div>` : ''}
                        ${additionalInfo}
                    </div>
                `;
                eventsContainer.appendChild(eventElement);
            });
        }

        function changeMonth(delta) {
            currentDate.setMonth(currentDate.getMonth() + delta);
            generateCalendar(currentDate.getFullYear(), currentDate.getMonth());
            updateSelectors();
        }

        function updateSelectors() {
            document.getElementById('monthSelect').value = currentDate.getMonth();
            document.getElementById('yearSelect').value = currentDate.getFullYear();
        }

        // Event listeners
        document.getElementById('prevMonth').addEventListener('click', () => changeMonth(-1));
        document.getElementById('nextMonth').addEventListener('click', () => changeMonth(1));

        document.getElementById('monthSelect').addEventListener('change', function() {
            currentDate.setMonth(parseInt(this.value));
            generateCalendar(currentDate.getFullYear(), currentDate.getMonth());
        });

        document.getElementById('yearSelect').addEventListener('change', function() {
            currentDate.setFullYear(parseInt(this.value));
            generateCalendar(currentDate.getFullYear(), currentDate.getMonth());
        });

        // Initialize calendar
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing calendar...');
            const now = new Date();
            currentDate = new Date(now.getFullYear(), now.getMonth());
            updateSelectors();
            generateCalendar(currentDate.getFullYear(), currentDate.getMonth());
        });

        // Auto-print functionality (disable for development)
        window.onload = function() {
            // Uncomment the line below for auto-print
            // setTimeout(() => window.print(), 1500);
        };

        // Close window after printing
        window.onafterprint = function() {
            // Uncomment the line below to auto-close
            // window.close();
        };
    </script>
</body>
</html>