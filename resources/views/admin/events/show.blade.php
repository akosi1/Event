@extends('admin.layouts.app')
@section('title', 'Event Details')
@section('page-title', $event->title)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8 col-md-10">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-eye me-2"></i>Event Details</h5>
                    <div>
                        <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-light btn-sm me-2">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        <a href="{{ route('admin.events.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                
                <!-- Event Image -->
                @if($event->image)
                <div class="mb-4 text-center">
                    <img src="{{ Storage::url($event->image) }}" 
                         alt="{{ $event->title }}" 
                         class="img-fluid rounded shadow" 
                         style="max-height: 300px; object-fit: cover;">
                </div>
                @endif

                <!-- Basic Information -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted">Title</label>
                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light">{{ $event->title }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted">Status</label>
                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light">
                            <span class="badge bg-{{ $event->status == 'active' ? 'success' : ($event->status == 'postponed' ? 'warning' : 'danger') }}">
                                {{ ucfirst($event->status) }}
                            </span>
                        </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted">Date</label>
                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light">
                            <i class="fas fa-calendar me-2"></i>{{ $event->date->format('F j, Y') }}
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted">Location</label>
                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light">
                            <i class="fas fa-map-marker-alt me-2"></i>{{ $event->location }}
                        </p>
                    </div>
                </div>

                @if($event->department)
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted">Department</label>
                    <p class="form-control-plaintext border rounded px-3 py-2 bg-light">
                        @php
                            $departments = \App\Models\Event::DEPARTMENTS;
                            $departmentName = $departments[$event->department] ?? $event->department;
                        @endphp
                        {{ $event->department }} - {{ $departmentName }}
                    </p>
                </div>
                @endif

                <div class="mb-3">
                    <label class="form-label fw-bold text-muted">Description</label>
                    <div class="border rounded px-3 py-2 bg-light" style="min-height: 100px;">
                        {{ $event->description }}
                    </div>
                </div>

                <!-- Recurring Information -->
                @if($event->repeat_type && $event->repeat_type !== 'none')
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted">Recurring Details</label>
                    <div class="border rounded px-3 py-2 bg-light">
                        <p class="mb-1">
                            <strong>Repeat:</strong> 
                            @if($event->repeat_interval && $event->repeat_interval > 1)
                                Every {{ $event->repeat_interval }} {{ str_plural($event->repeat_type, $event->repeat_interval) }}
                            @else
                                {{ ucfirst($event->repeat_type) }}
                            @endif
                        </p>
                        @if($event->repeat_until)
                        <p class="mb-0"><strong>Until:</strong> {{ $event->repeat_until->format('F j, Y') }}</p>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Cancel Reason -->
                @if(in_array($event->status, ['postponed', 'cancelled']) && $event->cancel_reason)
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted">
                        Reason for {{ ucfirst($event->status) }}
                    </label>
                    <div class="border rounded px-3 py-2 bg-warning bg-opacity-10">
                        {{ $event->cancel_reason }}
                    </div>
                </div>
                @endif

                <!-- Parent Event Info -->
                @if($event->parentEvent)
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted">Parent Event</label>
                    <div class="border rounded px-3 py-2 bg-info bg-opacity-10">
                        <a href="{{ route('admin.events.show', $event->parentEvent) }}" class="text-decoration-none">
                            <i class="fas fa-link me-2"></i>{{ $event->parentEvent->title }}
                        </a>
                    </div>
                </div>
                @endif

                <!-- Child Events -->
                @if($event->childEvents->count() > 0)
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted">Recurring Events ({{ $event->childEvents->count() }})</label>
                    <div class="border rounded px-3 py-2 bg-light">
                        <div class="row">
                            @foreach($event->childEvents->take(6) as $childEvent)
                            <div class="col-md-6 mb-2">
                                <a href="{{ route('admin.events.show', $childEvent) }}" class="text-decoration-none">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calendar-day me-2 text-primary"></i>
                                        <span>{{ $childEvent->date->format('M j, Y') }}</span>
                                        <span class="badge bg-{{ $childEvent->status == 'active' ? 'success' : ($childEvent->status == 'postponed' ? 'warning' : 'danger') }} ms-2">
                                            {{ ucfirst($childEvent->status) }}
                                        </span>
                                    </div>
                                </a>
                            </div>
                            @endforeach
                        </div>
                        @if($event->childEvents->count() > 6)
                        <p class="text-muted mb-0 mt-2">
                            <small>and {{ $event->childEvents->count() - 6 }} more events...</small>
                        </p>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Timestamps -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted">Created</label>
                        <p class="form-control-plaintext text-muted">
                            <small>{{ $event->created_at->format('F j, Y \a\t g:i A') }}</small>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted">Last Updated</label>
                        <p class="form-control-plaintext text-muted">
                            <small>{{ $event->updated_at->format('F j, Y \a\t g:i A') }}</small>
                        </p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-grid gap-2 d-md-flex justify-content-md-end border-top pt-3">
                    <form action="{{ route('admin.events.destroy', $event) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this event?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i>Delete
                        </button>
                    </form>
                    <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                    <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">
                        <i class="fas fa-list me-1"></i>Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection