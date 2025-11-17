@extends('admin.layouts.app')
@section('title', 'Create Event')
@section('page-title', 'Create New Event')

@push('styles')
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
    .dept-checkbox, .year-checkbox {
        margin: 5px 0;
    }
    .time-input {
        max-width: 150px;
    }
    .image-upload-section {
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        background: #f8f9fc;
        transition: all 0.3s ease;
    }
    .image-upload-section:hover {
        border-color: #667eea;
        background: #fff;
    }
    .preview-container {
        position: relative;
        display: inline-block;
        margin-top: 15px;
    }
    .preview-container img {
        max-height: 250px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .remove-preview-btn {
        position: absolute;
        top: -10px;
        right: -10px;
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    .remove-preview-btn:hover {
        background: #c82333;
    }
    .file-size-info {
        font-size: 0.875rem;
        color: #6c757d;
        margin-top: 5px;
    }
    .checkbox-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 10px;
    }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10 col-md-12">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Create New Event</h5>
                    <a href="{{ route('admin.events.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Back to Events
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.events.store') }}" method="POST" id="eventForm">
                    @csrf

                    <!-- Basic Information -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Basic Information</h6>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="title" class="form-label fw-semibold">Event Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                    id="title" name="title" value="{{ old('title') }}" required 
                                    placeholder="Enter event title">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label fw-semibold">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                        id="description" name="description" rows="4" required
                                        placeholder="Describe your event in detail...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Date, Time & Location -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3"><i class="fas fa-calendar-alt me-2"></i>Date, Time & Location</h6>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="date" class="form-label fw-semibold">Event Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('date') is-invalid @enderror"
                                    id="date" name="date" value="{{ old('date') }}" required>
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-2 mb-3">
                                <label for="start_time" class="form-label fw-semibold">Start Time</label>
                                <input type="time" class="form-control time-input @error('start_time') is-invalid @enderror"
                                    id="start_time" name="start_time" value="{{ old('start_time') }}">
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-2 mb-3">
                                <label for="end_time" class="form-label fw-semibold">End Time</label>
                                <input type="time" class="form-control time-input @error('end_time') is-invalid @enderror"
                                    id="end_time" name="end_time" value="{{ old('end_time') }}">
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="location" class="form-label fw-semibold">Location <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror"
                                    id="location" name="location" value="{{ old('location') }}" required
                                    placeholder="Event venue">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3"><i class="fas fa-toggle-on me-2"></i>Event Status</h6>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="postponed" {{ old('status') == 'postponed' ? 'selected' : '' }}>Postponed</option>
                                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row" id="cancelReasonRow" style="display: {{ in_array(old('status'), ['postponed', 'cancelled']) ? 'block' : 'none' }};">
                            <div class="col-md-12 mb-3">
                                <label for="cancel_reason" class="form-label fw-semibold">Reason for Postponement/Cancellation</label>
                                <textarea class="form-control @error('cancel_reason') is-invalid @enderror"
                                        id="cancel_reason" name="cancel_reason" rows="2"
                                        placeholder="Provide a reason...">{{ old('cancel_reason') }}</textarea>
                                @error('cancel_reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Department & Year Level Exclusivity -->
                    <div class="exclusivity-card">
                        <div class="card-header-custom">
                            <h6 class="mb-0"><i class="fas fa-users me-2"></i>Department & Year Level Access</h6>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="is_exclusive" name="is_exclusive"
                                   value="1" {{ old('is_exclusive') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_exclusive">
                                Restrict to specific departments and year levels
                            </label>
                            <div class="form-text">Uncheck to make this event available to all students</div>
                        </div>

                        <div id="departmentSelection" style="display: {{ old('is_exclusive') ? 'block' : 'none' }};">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="department" class="form-label fw-semibold">Primary Department</label>
                                    <select class="form-select @error('department') is-invalid @enderror" id="department" name="department">
                                        <option value="">Select Primary Department</option>
                                        <option value="BSIT" {{ old('department') == 'BSIT' ? 'selected' : '' }}>BSIT - Information Technology</option>
                                        <option value="BSBA" {{ old('department') == 'BSBA' ? 'selected' : '' }}>BSBA - Business Administration</option>
                                        <option value="BSED" {{ old('department') == 'BSED' ? 'selected' : '' }}>BSED - Secondary Education</option>
                                        <option value="BEED" {{ old('department') == 'BEED' ? 'selected' : '' }}>BEED - Elementary Education</option>
                                        <option value="BSHM" {{ old('department') == 'BSHM' ? 'selected' : '' }}>BSHM - Hospitality Management</option>
                                    </select>
                                    @error('department')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Additional Allowed Departments</label>
                                    <div class="border rounded p-3" style="max-height: 150px; overflow-y: auto;">
                                        <div class="checkbox-grid">
                                            <div class="form-check dept-checkbox">
                                                <input class="form-check-input" type="checkbox" name="allowed_departments[]"
                                                       value="BSIT" id="dept_BSIT" {{ in_array('BSIT', old('allowed_departments', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="dept_BSIT">BSIT</label>
                                            </div>
                                            <div class="form-check dept-checkbox">
                                                <input class="form-check-input" type="checkbox" name="allowed_departments[]"
                                                       value="BSBA" id="dept_BSBA" {{ in_array('BSBA', old('allowed_departments', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="dept_BSBA">BSBA</label>
                                            </div>
                                            <div class="form-check dept-checkbox">
                                                <input class="form-check-input" type="checkbox" name="allowed_departments[]"
                                                       value="BSED" id="dept_BSED" {{ in_array('BSED', old('allowed_departments', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="dept_BSED">BSED</label>
                                            </div>
                                            <div class="form-check dept-checkbox">
                                                <input class="form-check-input" type="checkbox" name="allowed_departments[]"
                                                       value="BEED" id="dept_BEED" {{ in_array('BEED', old('allowed_departments', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="dept_BEED">BEED</label>
                                            </div>
                                            <div class="form-check dept-checkbox">
                                                <input class="form-check-input" type="checkbox" name="allowed_departments[]"
                                                       value="BSHM" id="dept_BSHM" {{ in_array('BSHM', old('allowed_departments', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="dept_BSHM">BSHM</label>
                                            </div>
                                        </div>
                                    </div>
                                    @error('allowed_departments')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-semibold">Allowed Year Levels</label>
                                    <div class="border rounded p-3">
                                        <div class="checkbox-grid">
                                            <div class="form-check year-checkbox">
                                                <input class="form-check-input" type="checkbox" name="allowed_year_levels[]"
                                                       value="1" id="year_1" {{ in_array('1', old('allowed_year_levels', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="year_1">1st Year</label>
                                            </div>
                                            <div class="form-check year-checkbox">
                                                <input class="form-check-input" type="checkbox" name="allowed_year_levels[]"
                                                       value="2" id="year_2" {{ in_array('2', old('allowed_year_levels', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="year_2">2nd Year</label>
                                            </div>
                                            <div class="form-check year-checkbox">
                                                <input class="form-check-input" type="checkbox" name="allowed_year_levels[]"
                                                       value="3" id="year_3" {{ in_array('3', old('allowed_year_levels', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="year_3">3rd Year</label>
                                            </div>
                                            <div class="form-check year-checkbox">
                                                <input class="form-check-input" type="checkbox" name="allowed_year_levels[]"
                                                       value="4" id="year_4" {{ in_array('4', old('allowed_year_levels', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="year_4">4th Year</label>
                                            </div>
                                        </div>
                                    </div>
                                    @error('allowed_year_levels')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">If no year level is selected, event will be open to all year levels</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recurring Events -->
                    <div class="recurrence-card">
                        <div class="card-header-custom">
                            <h6 class="mb-0"><i class="fas fa-redo me-2"></i>Recurring Event Settings</h6>
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
                                    @error('recurrence_pattern')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-2 mb-3">
                                    <label for="recurrence_interval" class="form-label fw-semibold">Every</label>
                                    <input type="number" class="form-control @error('recurrence_interval') is-invalid @enderror"
                                           id="recurrence_interval" name="recurrence_interval"
                                           value="{{ old('recurrence_interval', 1) }}" min="1" max="365">
                                    @error('recurrence_interval')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text" id="intervalText">day(s)</div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="recurrence_end_date" class="form-label fw-semibold">End Date</label>
                                    <input type="date" class="form-control @error('recurrence_end_date') is-invalid @enderror"
                                           id="recurrence_end_date" name="recurrence_end_date"
                                           value="{{ old('recurrence_end_date') }}">
                                    @error('recurrence_end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="recurrence_count" class="form-label fw-semibold">Max Occurrences</label>
                                    <input type="number" class="form-control @error('recurrence_count') is-invalid @enderror"
                                           id="recurrence_count" name="recurrence_count"
                                           value="{{ old('recurrence_count') }}" min="1" max="365" placeholder="Optional">
                                    @error('recurrence_count')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Event Image Upload -->
                    <div class="image-upload-section">
                        <h6 class="text-primary mb-3"><i class="fas fa-image me-2"></i>Event Image</h6>
                        
                        <input type="hidden" name="image" id="imageBase64">
                        <input type="file" class="form-control @error('image') is-invalid @enderror"
                            id="imageInput" accept="image/jpeg,image/png,image/jpg">
                        @error('image')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="file-size-info">
                            <i class="fas fa-info-circle me-1"></i>Supported formats: JPG, PNG, JPEG | Maximum size: 2MB
                        </div>

                        <div id="imagePreview" style="display: none;">
                            <div class="preview-container">
                                <img id="previewImg" src="" alt="Preview" class="img-fluid">
                                <button type="button" class="remove-preview-btn" onclick="removeImagePreview()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="mt-2" id="imageSizeInfo"></div>
                        </div>
                    </div>

                    <!-- Certificate Template Upload -->
                    <div class="image-upload-section">
                        <h6 class="text-primary mb-3"><i class="fas fa-certificate me-2"></i>Certificate Template Image</h6>
                        
                        <input type="hidden" name="certificate_template_image" id="certificateImageBase64">
                        <input type="file" class="form-control @error('certificate_template_image') is-invalid @enderror"
                            id="certificateImageInput" accept="image/jpeg,image/png,image/jpg">
                        @error('certificate_template_image')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="file-size-info">
                            <i class="fas fa-info-circle me-1"></i>Supported formats: JPG, PNG, JPEG | Maximum size: 2MB
                        </div>

                        <div id="certificateImagePreview" style="display: none;">
                            <div class="preview-container">
                                <img id="certificatePreviewImg" src="" alt="Certificate Preview" class="img-fluid">
                                <button type="button" class="remove-preview-btn" onclick="removeCertificatePreview()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="mt-2" id="certificateSizeInfo"></div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end border-top pt-4 mt-4">
                        <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary px-4">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary px-5" id="submitBtn">
                            <i class="fas fa-save me-2"></i>Create Event
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    const recurrenceInterval = document.getElementById('recurrence_interval');

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
                recurrenceInterval.style.display = 'none';
                break;
            default:
                recurrenceInterval.style.display = 'block';
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

    // Event Image Upload Handler
    const imageInput = document.getElementById('imageInput');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const imageSizeInfo = document.getElementById('imageSizeInfo');

    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validate file type
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!validTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File Type',
                    text: 'Please upload only JPG, PNG, or JPEG images.',
                    confirmButtonColor: '#d33'
                });
                imageInput.value = '';
                return;
            }

            // Validate file size (2MB = 2097152 bytes)
            if (file.size > 2097152) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: 'Image size must not exceed 2MB. Please choose a smaller file.',
                    confirmButtonColor: '#d33'
                });
                imageInput.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const base64String = e.target.result;
                document.getElementById('imageBase64').value = base64String;
                previewImg.src = base64String;
                imagePreview.style.display = 'block';
                
                // Display file size
                const sizeInKB = (file.size / 1024).toFixed(2);
                imageSizeInfo.innerHTML = `<small class="text-success"><i class="fas fa-check-circle me-1"></i>File size: ${sizeInKB} KB</small>`;
            };
            reader.readAsDataURL(file);
        }
    });

    // Certificate Image Upload Handler
    const certificateImageInput = document.getElementById('certificateImageInput');
    const certificateImagePreview = document.getElementById('certificateImagePreview');
    const certificatePreviewImg = document.getElementById('certificatePreviewImg');
    const certificateSizeInfo = document.getElementById('certificateSizeInfo');

    certificateImageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validate file type
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!validTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File Type',
                    text: 'Please upload only JPG, PNG, or JPEG images.',
                    confirmButtonColor: '#d33'
                });
                certificateImageInput.value = '';
                return;
            }

            // Validate file size (2MB)
            if (file.size > 2097152) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: 'Image size must not exceed 2MB. Please choose a smaller file.',
                    confirmButtonColor: '#d33'
                });
                certificateImageInput.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const base64String = e.target.result;
                document.getElementById('certificateImageBase64').value = base64String;
                certificatePreviewImg.src = base64String;
                certificateImagePreview.style.display = 'block';
                
                // Display file size
                const sizeInKB = (file.size / 1024).toFixed(2);
                certificateSizeInfo.innerHTML = `<small class="text-success"><i class="fas fa-check-circle me-1"></i>File size: ${sizeInKB} KB</small>`;
            };
            reader.readAsDataURL(file);
        }
    });

    // Form submission handler
    document.getElementById('eventForm').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating Event...';
    });
});

// Remove image preview
function removeImagePreview() {
    document.getElementById('imageInput').value = '';
    document.getElementById('imageBase64').value = '';
    document.getElementById('imagePreview').style.display = 'none';
}

// Remove certificate preview
function removeCertificatePreview() {
    document.getElementById('certificateImageInput').value = '';
    document.getElementById('certificateImageBase64').value = '';
    document.getElementById('certificateImagePreview').style.display = 'none';
}

// Show success message
@if(session('success'))
Swal.fire({
    icon: 'success',
    title: 'Success!',
    text: '{{ session('success') }}',
    timer: 3000,
    showConfirmButton: false
});
@endif

// Show error message
@if($errors->any())
Swal.fire({
    icon: 'error',
    title: 'Validation Error',
    html: '<ul style="text-align: left;">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
    confirmButtonColor: '#d33'
});
@endif
</script>
@endpush