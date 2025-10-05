@extends('admin.layouts.app')

@section('title', 'Events Report')
@section('page-title', 'Events Report')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Events Report</h5>
                <button onclick="printReport()" class="btn btn-success">
                    <i class="fas fa-print"></i> Print Report
                </button>
            </div>
            
            <!-- Filters -->
            <div class="card-body border-bottom">
                <form method="GET" class="row g-3" id="reportForm">
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" class="form-control" name="search" 
                               value="{{ request('search') }}" placeholder="Title, description, location...">
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Date From</label>
                        <input type="date" class="form-control" name="date_from" 
                               value="{{ request('date_from') }}">
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Date To</label>
                        <input type="date" class="form-control" name="date_to" 
                               value="{{ request('date_to') }}">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Location</label>
                        <select class="form-select" name="location">
                            <option value="">All Locations</option>
                            @foreach($locations as $location)
                                <option value="{{ $location }}" 
                                        {{ request('location') == $location ? 'selected' : '' }}>
                                    {{ $location }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <a href="{{ route('admin.reports.events') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    </div>
                </form>
            </div>
            
            <!-- Results -->
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6>Total Events: {{ $events->count() }}</h6>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                                data-bs-toggle="dropdown">Sort By</button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort_by' => 'date', 'sort_order' => 'desc']) }}">Date (Newest)</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort_by' => 'date', 'sort_order' => 'asc']) }}">Date (Oldest)</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort_by' => 'title', 'sort_order' => 'asc']) }}">Title (A-Z)</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort_by' => 'title', 'sort_order' => 'desc']) }}">Title (Z-A)</a></li>
                        </ul>
                    </div>
                </div>
                
                @if($events->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Date</th>
                                    <th>Location</th>
                                    <th>Description</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($events as $event)
                                <tr>
                                    <td>
                                        @if($event->hasImage())
                                            <img src="{{ $event->image_url }}" 
                                                 alt="{{ $event->title }}" 
                                                 class="img-thumbnail" 
                                                 style="width: 60px; height: 60px; object-fit: cover;">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center" 
                                                 style="width: 60px; height: 60px; border-radius: 4px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $event->title }}</strong>
                                    </td>
                                    <td>
                                        {{ $event->date->format('M d, Y') }}
                                        <br>
                                        <small class="text-muted">{{ $event->date->format('D') }}</small>
                                    </td>
                                    <td>{{ $event->location }}</td>
                                    <td>
                                        <div style="max-width: 200px;">
                                            {{ Str::limit($event->description, 80, '...') }}
                                        </div>
                                    </td>
                                    <td>
                                        {{ $event->created_at->format('M d, Y') }}
                                        <br>
                                        <small class="text-muted">{{ $event->created_at->format('g:i A') }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-alt fa-2x text-muted mb-2"></i>
                        <p class="text-muted">No events found matching your criteria</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function printReport() {
    const form = document.getElementById('reportForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    
    window.open(`{{ route('admin.reports.events.print') }}?${params.toString()}`, '_blank');
}
</script>
@endpush