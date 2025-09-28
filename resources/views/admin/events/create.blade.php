@extends('admin.layouts.app')
@section('title', 'Create Event')
@section('page-title', 'Create New Event')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/events-create.css') }}">
<style>
    .exclusivity-card, .recurrence-card {
        border: 1px solid #e3e6f0;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        background: #f8f9fc;
    }
    .card-header-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 10px 15px;
        border-radius: 6px;
        margin-bottom: 15px;
    }
    .dept-checkbox {
        margin: 5px 0;
    }
    .time-input {
        max-width: 150px;
    }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10 col-md-12">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Event Details</h5>
                    <a href="{{ route('admin.events.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data" id="eventForm">
                    @csrf
                    
                    <!-- Basic Info -->
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="title" class="form-label fw-semibold">Event Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                id="title" name="title" value="{{ old('title') }}" required placeholder="Enter event title">
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label fw-semibold">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                    id="description" name="description" rows="4" required 
                                    placeholder="Describe your event...">{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <!-- Date, Time & Location -->
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="date" class="form-label fw-semibold">Event Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('date') is-invalid @enderror" 
                                id="date" name="date" value="{{ old('date') }}" required>
                            @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="start_time" class="form-label fw-semibold">Start Time</label>
                            <input type="time" class="form-control time-input @error('start_time') is-invalid @enderror" 
                                id="start_time" name="start_time" value="{{ old('start_time') }}">
                            @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="end_time" class="form-label fw-semibold">End Time</label>
                            <input type="time" class="form-control time-input @error('end_time') is-invalid @enderror" 
                                id="end_time" name="end_time" value="{{ old('end_time') }}">
                            @error('end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="location" class="form-label fw-semibold">Location <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                id="location" name="location" value="{{ old('location') }}" required 
                                placeholder="Event location">
                            @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label fw-semibold">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="postponed" {{ old('status') == 'postponed' ? 'selected' : '' }}>Postponed</option>
                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <!-- Cancel Reason (hidden by default) -->
                    <div class="row" id="cancelReasonRow" style="display: {{ in_array(old('status'), ['postponed', 'cancelled']) ? 'block' : 'none' }};">
                        <div class="col-md-12 mb-3">
                            <label for="cancel_reason" class="form-label fw-semibold">Reason for Postponement/Cancellation</label>
                            <textarea class="form-control @error('cancel_reason') is-invalid @enderror" 
                                    id="cancel_reason" name="cancel_reason" rows="2" 
                                    placeholder="Provide reason...">{{ old('cancel_reason') }}</textarea>
                            @error('cancel_reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <!-- Department Exclusivity Section -->
                    <div class="exclusivity-card">
                        <div class="card-header-custom">
                            <h6 class="mb-0"><i class="fas fa-users me-2"></i>Department Access</h6>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="is_exclusive" name="is_exclusive" 
                                   value="1" {{ old('is_exclusive') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_exclusive">
                                Restrict to specific departments
                            </label>
                            <div class="form-text">Uncheck to make this event available to all departments</div>
                        </div>

                        <div id="departmentSelection" style="display: {{ old('is_exclusive') ? 'block' : 'none' }};">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="department" class="form-label fw-semibold">Primary Department</label>
                                    <select class="form-select @error('department') is-invalid @enderror" id="department" name="department">
                                        <option value="">Select Primary Department</option>
                                        @foreach(['BSIT' => 'Bachelor of Science in Information Technology', 'BSBA' => 'Bachelor of Science in Business Administration', 'BSED' => 'Bachelor of Science in Education', 'BEED' => 'Bachelor of Elementary Education', 'BSHM' => 'Bachelor of Science in Hospitality Management'] as $code => $name)
                                            <option value="{{ $code }}" {{ old('department') == $code ? 'selected' : '' }}>
                                                {{ $code }} - {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('department')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Additional Allowed Departments</label>
                                    <div class="border rounded p-2" style="max-height: 120px; overflow-y: auto;">
                                        @foreach(['BSIT' => 'Bachelor of Science in Information Technology', 'BSBA' => 'Bachelor of Science in Business Administration', 'BSED' => 'Bachelor of Science in Education', 'BEED' => 'Bachelor of Elementary Education', 'BSHM' => 'Bachelor of Science in Hospitality Management'] as $code => $name)
                                            <div class="form-check dept-checkbox">
                                                <input class="form-check-input" type="checkbox" name="allowed_departments[]" 
                                                       value="{{ $code }}" id="dept_{{ $code }}"
                                                       {{ in_array($code, old('allowed_departments', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="dept_{{ $code }}">
                                                    {{ $code }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('allowed_departments')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recurring Events Section -->
                    <div class="recurrence-card">
                        <div class="card-header-custom">
                            <h6 class="mb-0"><i class="fas fa-redo me-2"></i>Recurring Event</h6>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="is_recurring" name="is_recurring" 
                                   value="1" {{ old('is_recurring') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_recurring">
                                Make this a recurring event
                            </label>
                            <div class="form-text">Create multiple instances of this event based on a schedule</div>
                        </div>

                        <div id="recurrenceSettings" style="display: {{ old('is_recurring') ? 'block' : 'none' }};">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="recurrence_pattern" class="form-label fw-semibold">Repeat Pattern</label>
                                    <select class="form-select @error('recurrence_pattern') is-invalid @enderror" 
                                            id="recurrence_pattern" name="recurrence_pattern">
                                        <option value="">Select Pattern</option>
                                        <option value="daily" {{ old('recurrence_pattern') == 'daily' ? 'selected' : '' }}>Daily</option>
                                        <option value="weekly" {{ old('recurrence_pattern') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                        <option value="monthly" {{ old('recurrence_pattern') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                        <option value="yearly" {{ old('recurrence_pattern') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                        <option value="weekdays" {{ old('recurrence_pattern') == 'weekdays' ? 'selected' : '' }}>Weekdays Only</option>
                                    </select>
                                    @error('recurrence_pattern')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="recurrence_interval" class="form-label fw-semibold">Every</label>
                                    <input type="number" class="form-control @error('recurrence_interval') is-invalid @enderror" 
                                           id="recurrence_interval" name="recurrence_interval" 
                                           value="{{ old('recurrence_interval', 1) }}" min="1" max="365">
                                    @error('recurrence_interval')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <div class="form-text" id="intervalText">day(s)</div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="recurrence_end_date" class="form-label fw-semibold">End Date</label>
                                    <input type="date" class="form-control @error('recurrence_end_date') is-invalid @enderror" 
                                           id="recurrence_end_date" name="recurrence_end_date" 
                                           value="{{ old('recurrence_end_date') }}">
                                    @error('recurrence_end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="recurrence_count" class="form-label fw-semibold">Max Occurrences</label>
                                    <input type="number" class="form-control @error('recurrence_count') is-invalid @enderror" 
                                           id="recurrence_count" name="recurrence_count" 
                                           value="{{ old('recurrence_count') }}" min="1" max="365" placeholder="Optional">
                                    @error('recurrence_count')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Image Upload -->
                    <div class="mb-4">
                        <label for="image" class="form-label fw-semibold">Event Image</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror" 
                            id="image" name="image" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Supported: JPG, PNG, GIF, WebP. Max size: 2MB</div>
                        
                        <!-- Preview -->
                        <div id="imagePreview" class="mt-3" style="display: none;">
                            <div class="border rounded p-3 bg-light">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="text-success fw-semibold"><i class="fas fa-check-circle me-1"></i>Image Preview</span>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removePreview()">
                                        <i class="fas fa-times me-1"></i>Remove
                                    </button>
                                </div>
                                <img id="previewImg" src="" alt="Preview" class="img-fluid rounded shadow-sm" style="max-height: 200px;">
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end border-top pt-3">
                        <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-1"></i>Create Event
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/admin/events-create.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle exclusivity toggle
    const isExclusiveCheckbox = document.getElementById('is_exclusive');
    const departmentSelection = document.getElementById('departmentSelection');
    
    isExclusiveCheckbox.addEventListener('change', function() {
        departmentSelection.style.display = this.checked ? 'block' : 'none';
    });

    // Handle recurring toggle
    const isRecurringCheckbox = document.getElementById('is_recurring');
    const recurrenceSettings = document.getElementById('recurrenceSettings');
    
    isRecurringCheckbox.addEventListener('change', function() {
        recurrenceSettings.style.display = this.checked ? 'block' : 'none';
    });

    // Handle recurrence pattern change
    const recurrencePattern = document.getElementById('recurrence_pattern');
    const intervalText = document.getElementById('intervalText');
    
    recurrencePattern.addEventListener('change', function() {
        const pattern = this.value;
        let text = 'day(s)';
        
        switch(pattern) {
            case 'weekly':
                text = 'week(s)';
                break;
            case 'monthly':
                text = 'month(s)';
                break;
            case 'yearly':
                text = 'year(s)';
                break;
            case 'weekdays':
                text = 'weekday';
                document.getElementById('recurrence_interval').style.display = 'none';
                break;
            default:
                document.getElementById('recurrence_interval').style.display = 'block';
        }
        
        intervalText.textContent = text;
    });

    // Handle status change for cancel reason
    const statusSelect = document.getElementById('status');
    const cancelReasonRow = document.getElementById('cancelReasonRow');
    
    statusSelect.addEventListener('change', function() {
        const showReason = ['postponed', 'cancelled'].includes(this.value);
        cancelReasonRow.style.display = showReason ? 'block' : 'none';
    });

    // Image preview functionality
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });
});

function removePreview() {
    document.getElementById('image').value = '';
    document.getElementById('imagePreview').style.display = 'none';
}
</script>
@endpush
@endsection