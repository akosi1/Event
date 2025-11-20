@extends('admin.layouts.app')
@section('title', 'Edit Event')
@section('page-title', 'Edit Event')

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
    .upload-section { 
        border: 2px dashed #dee2e6; 
        border-radius: 8px; 
        padding: 20px; 
        margin-bottom: 20px; 
        background: #f8f9fc; 
        transition: all 0.3s; 
    }
    .upload-section:hover { 
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
    .remove-btn { 
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
    }
    .remove-btn:hover { 
        background: #c82333; 
    }
    .doc-preview-card { 
        border: 2px solid #e3e6f0; 
        border-radius: 8px; 
        padding: 20px; 
        background: #fff; 
        transition: all 0.3s; 
    }
    .doc-preview-card:hover { 
        border-color: #667eea; 
        box-shadow: 0 4px 12px rgba(0,0,0,0.1); 
    }
    .doc-icon-large { 
        font-size: 3rem; 
        margin-bottom: 10px; 
    }
    .file-upload-area {
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 30px;
        text-align: center;
        background: #f8f9fc;
        transition: all 0.3s;
        cursor: pointer;
    }
    .file-upload-area:hover {
        border-color: #667eea;
        background: #fff;
    }
    .file-upload-area.dragover {
        border-color: #667eea;
        background: #e7f3ff;
    }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Event</h5>
                    <a href="{{ route('admin.events.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.events.update', $event) }}" method="POST" id="editEventForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Basic Information --}}
                    <div class="mb-4">
                        <h6 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Basic Information</h6>
                        <div class="mb-3">
                            <label for="title" class="form-label fw-semibold">Event Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $event->title) }}" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" required>{{ old('description', $event->description) }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- Date, Time & Location --}}
                    <div class="mb-4">
                        <h6 class="text-primary mb-3"><i class="fas fa-calendar-alt me-2"></i>Date, Time & Location</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="date" class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', $event->date->format('Y-m-d')) }}" required>
                                @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="start_time" class="form-label fw-semibold">Start</label>
                                <input type="time" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" value="{{ old('start_time', $event->start_time?->format('H:i')) }}">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="end_time" class="form-label fw-semibold">End</label>
                                <input type="time" class="form-control @error('end_time') is-invalid @enderror" id="end_time" name="end_time" value="{{ old('end_time', $event->end_time?->format('H:i')) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="location" class="form-label fw-semibold">Location <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location', $event->location) }}" required>
                            </div>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="mb-4">
                        <h6 class="text-primary mb-3"><i class="fas fa-toggle-on me-2"></i>Event Status</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="active" {{ old('status', $event->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="postponed" {{ old('status', $event->status) == 'postponed' ? 'selected' : '' }}>Postponed</option>
                                    <option value="cancelled" {{ old('status', $event->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                        </div>
                        <div id="cancelReasonRow" style="display: {{ in_array(old('status', $event->status), ['postponed', 'cancelled']) ? 'block' : 'none' }};">
                            <div class="mb-3">
                                <label for="cancel_reason" class="form-label fw-semibold">Cancellation Reason</label>
                                <textarea class="form-control" id="cancel_reason" name="cancel_reason" rows="2">{{ old('cancel_reason', $event->cancel_reason) }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Cancellation Document Section --}}
                    <div class="mb-4" id="cancellationDocSection" style="display: {{ in_array(old('status', $event->status), ['postponed', 'cancelled']) ? 'block' : 'none' }};">
                        <h6 class="text-primary mb-3"><i class="fas fa-file-alt me-2"></i>Cancellation Document</h6>
                        
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            Upload official cancellation document (PDF or DOCX only, Max 5MB)
                        </div>

                        @if($event->hasCancellationDocument())
                        <div class="mb-4" id="currentDocContainer">
                            <label class="form-label fw-semibold">Current Document</label>
                            <div class="doc-preview-card">
                                <div class="row align-items-center">
                                    <div class="col-md-2 text-center">
                                        <i class="{{ $event->cancellation_document_icon }} doc-icon-large"></i>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="mb-1 fw-bold">{{ $event->cancellation_document_name }}</h6>
                                        <small class="text-muted">
                                            <i class="fas fa-folder me-1"></i>{{ $event->cancellation_document_type }}
                                            <span class="mx-2">•</span>
                                            <i class="fas fa-weight me-1"></i>{{ $event->cancellation_document_size_formatted }}
                                        </small>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-primary btn-sm" onclick="viewDocument('{{ $event->cancellation_document_url }}', '{{ $event->cancellation_document_name }}', '{{ $event->cancellation_document_extension }}')">
                                                <i class="fas fa-eye me-1"></i>View
                                            </button>
                                            <button type="button" class="btn btn-success btn-sm" onclick="downloadDocument('{{ $event->cancellation_document_url }}', '{{ $event->cancellation_document_name }}')">
                                                <i class="fas fa-download me-1"></i>Download
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm" onclick="removeCurrentDocument()">
                                                <i class="fas fa-trash me-1"></i>Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="removeDocumentInput" name="remove_cancellation_document" value="0">
                        </div>
                        @endif

                        {{-- File Upload Area --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ $event->hasCancellationDocument() ? 'Replace Document' : 'Upload Document' }}</label>
                            <div class="file-upload-area" id="fileUploadArea">
                                <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-3"></i>
                                <h6>Click to upload or drag and drop</h6>
                                <p class="text-muted mb-0">PDF or DOCX (Max 5MB)</p>
                                <input type="file" class="form-control d-none @error('cancellation_document') is-invalid @enderror" 
                                       id="cancellationDocInput" 
                                       accept=".pdf,.docx">
                            </div>
                            @error('cancellation_document')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Hidden inputs for base64 data --}}
                        <input type="hidden" name="cancellation_document_base64" id="docBase64">
                        <input type="hidden" name="cancellation_document_name" id="docName">

                        {{-- New Document Preview --}}
                        <div id="newDocPreview" style="display: none;" class="mt-3">
                            <label class="form-label fw-semibold">Selected Document</label>
                            <div class="doc-preview-card border-success">
                                <div class="row align-items-center">
                                    <div class="col-md-2 text-center">
                                        <i id="newDocIcon" class="fas fa-file doc-icon-large"></i>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="mb-1 fw-bold" id="newDocNameDisplay"></h6>
                                        <small class="text-success" id="newDocSize"></small>
                                        <div class="progress mt-2" style="height: 5px;">
                                            <div class="progress-bar bg-success" style="width: 100%"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <button type="button" class="btn btn-danger btn-sm" onclick="removeNewDocument()">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Department & Year Level --}}
                    <div class="exclusivity-card">
                        <div class="card-header-custom">
                            <h6 class="mb-0"><i class="fas fa-users me-2"></i>Access Control</h6>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="is_exclusive" name="is_exclusive" value="1" {{ old('is_exclusive', $event->is_exclusive) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_exclusive">Restrict to specific departments/years</label>
                        </div>
                        <div id="departmentSelection" style="display: {{ old('is_exclusive', $event->is_exclusive) ? 'block' : 'none' }};">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Primary Department</label>
                                    <select class="form-select" id="department" name="department">
                                        <option value="">Select Department</option>
                                        @foreach(['BSIT', 'BSBA', 'BSED', 'BEED', 'BSHM'] as $dept)
                                        <option value="{{ $dept }}" {{ old('department', $event->department) == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Additional Departments</label>
                                    <div class="border rounded p-3">
                                        @foreach(['BSIT', 'BSBA', 'BSED', 'BEED', 'BSHM'] as $dept)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="allowed_departments[]" value="{{ $dept }}" id="dept_{{ $dept }}" {{ in_array($dept, old('allowed_departments', $event->allowed_departments ?? [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="dept_{{ $dept }}">{{ $dept }}</label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Allowed Year Levels</label>
                                <div class="border rounded p-3">
                                    @foreach(['1' => '1st Year', '2' => '2nd Year', '3' => '3rd Year', '4' => '4th Year'] as $year => $label)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="allowed_year_levels[]" value="{{ $year }}" id="year_{{ $year }}" {{ in_array($year, old('allowed_year_levels', $event->allowed_year_levels ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="year_{{ $year }}">{{ $label }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Recurring Events --}}
                    <div class="recurrence-card">
                        <div class="card-header-custom">
                            <h6 class="mb-0"><i class="fas fa-redo me-2"></i>Recurring Event</h6>
                        </div>
                        @if($event->isRecurring())
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle me-2"></i>Pattern: <strong>{{ $event->recurrence_display }}</strong> | Instances: <strong>{{ $event->childEvents->count() + 1 }}</strong>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="update_series" name="update_series" value="1">
                            <label class="form-check-label fw-semibold" for="update_series">Update entire series</label>
                        </div>
                        @else
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="is_recurring" name="is_recurring" value="1" {{ old('is_recurring', $event->is_recurring) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_recurring">Make recurring</label>
                        </div>
                        <div id="recurrenceSettings" style="display: {{ old('is_recurring', $event->is_recurring) ? 'block' : 'none' }};">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold">Pattern</label>
                                    <select class="form-select" id="recurrence_pattern" name="recurrence_pattern">
                                        <option value="">Select Pattern</option>
                                        @foreach(['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly', 'yearly' => 'Yearly', 'weekdays' => 'Weekdays'] as $key => $val)
                                        <option value="{{ $key }}" {{ old('recurrence_pattern', $event->recurrence_pattern) == $key ? 'selected' : '' }}>{{ $val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label fw-semibold">Every</label>
                                    <input type="number" class="form-control" id="recurrence_interval" name="recurrence_interval" value="{{ old('recurrence_interval', $event->recurrence_interval ?? 1) }}" min="1">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-semibold">End Date</label>
                                    <input type="date" class="form-control" id="recurrence_end_date" name="recurrence_end_date" value="{{ old('recurrence_end_date', $event->recurrence_end_date?->format('Y-m-d')) }}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-semibold">Max Occurrences</label>
                                    <input type="number" class="form-control" id="recurrence_count" name="recurrence_count" value="{{ old('recurrence_count', $event->recurrence_count) }}" min="1" placeholder="Optional">
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- Event Image --}}
                    <div class="upload-section">
                        <h6 class="text-primary mb-3"><i class="fas fa-image me-2"></i>Event Image</h6>
                        @if($event->hasImage())
                        <div class="mb-3" id="currentImageContainer">
                            <div class="preview-container">
                                <img id="currentImage" src="{{ $event->image_url }}" alt="Current Image" class="img-fluid">
                                <button type="button" class="remove-btn" onclick="removeImage()"><i class="fas fa-times"></i></button>
                            </div>
                            <input type="hidden" id="removeImage" name="remove_image" value="0">
                        </div>
                        @endif
                        <input type="hidden" name="image" id="imageBase64">
                        <input type="file" class="form-control" id="imageInput" accept="image/jpeg,image/png,image/jpg">
                        <small class="text-muted d-block mt-1">JPG, PNG | Max 2MB</small>
                        <div id="newImagePreview" style="display: none;">
                            <div class="preview-container">
                                <img id="newPreviewImg" src="" alt="Preview" class="img-fluid">
                                <button type="button" class="remove-btn" onclick="removeNewImage()"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                    </div>

                    {{-- Certificate Template --}}
                    <div class="upload-section">
                        <h6 class="text-primary mb-3"><i class="fas fa-certificate me-2"></i>Certificate Template</h6>
                        @if($event->certificate_template_image)
                        <div class="mb-3" id="currentCertContainer">
                            <div class="preview-container">
                                <img id="currentCert" src="{{ $event->certificate_template_image }}" alt="Current Certificate" class="img-fluid">
                                <button type="button" class="remove-btn" onclick="removeCert()"><i class="fas fa-times"></i></button>
                            </div>
                            <input type="hidden" id="removeCert" name="remove_certificate" value="0">
                        </div>
                        @endif
                        <input type="hidden" name="certificate_template_image" id="certBase64">
                        <input type="file" class="form-control" id="certInput" accept="image/jpeg,image/png,image/jpg">
                        <small class="text-muted d-block mt-1">JPG, PNG | Max 2MB</small>
                        <div id="newCertPreview" style="display: none;">
                            <div class="preview-container">
                                <img id="newCertImg" src="" alt="Certificate Preview" class="img-fluid">
                                <button type="button" class="remove-btn" onclick="removeNewCert()"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end border-top pt-4 mt-4">
                        <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary px-4"><i class="fas fa-times me-2"></i>Cancel</a>
                        <button type="submit" class="btn btn-warning px-5" id="submitBtn"><i class="fas fa-save me-2"></i>Update Event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Document Viewer Modal --}}
<div class="modal fade" id="documentViewerModal" tabindex="-1" aria-labelledby="documentViewerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="documentViewerModalLabel">
                    <i class="fas fa-file-alt me-2"></i><span id="modalDocTitle"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="min-height: 500px;">
                <div id="documentViewerContent" class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Loading document...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="modalDownloadBtn">
                    <i class="fas fa-download me-2"></i>Download
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const $ = (id) => document.getElementById(id);
const show = (el, display = 'block') => el && (el.style.display = display);
const hide = (el) => el && (el.style.display = 'none');

// Toggle handlers
$('is_exclusive')?.addEventListener('change', (e) => show($('departmentSelection'), e.target.checked ? 'block' : 'none'));
$('is_recurring')?.addEventListener('change', (e) => show($('recurrenceSettings'), e.target.checked ? 'block' : 'none'));
$('status')?.addEventListener('change', function() {
    const showCancel = ['postponed', 'cancelled'].includes(this.value);
    show($('cancelReasonRow'), showCancel ? 'block' : 'none');
    show($('cancellationDocSection'), showCancel ? 'block' : 'none');
});

// Document Upload Handler
const fileUploadArea = $('fileUploadArea');
const docInput = $('cancellationDocInput');
const newDocPreview = $('newDocPreview');

// Click to upload
fileUploadArea?.addEventListener('click', () => docInput.click());

// Drag and drop
fileUploadArea?.addEventListener('dragover', (e) => {
    e.preventDefault();
    fileUploadArea.classList.add('dragover');
});

fileUploadArea?.addEventListener('dragleave', () => {
    fileUploadArea.classList.remove('dragover');
});

fileUploadArea?.addEventListener('drop', (e) => {
    e.preventDefault();
    fileUploadArea.classList.remove('dragover');
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        docInput.files = files;
        handleDocumentUpload(files[0]);
    }
});

docInput?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        handleDocumentUpload(file);
    }
});

function handleDocumentUpload(file) {
    // Validate file type
    const allowedExtensions = ['.pdf', '.docx'];
    const fileName = file.name.toLowerCase();
    const isValidType = allowedExtensions.some(ext => fileName.endsWith(ext));
    
    if (!isValidType) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid File Type',
            text: 'Please upload only PDF or DOCX files.',
            confirmButtonColor: '#d33'
        });
        docInput.value = '';
        return;
    }

    // Validate file size (5MB)
    if (file.size > 5242880) {
        Swal.fire({
            icon: 'error',
            title: 'File Too Large',
            text: 'Document size must not exceed 5MB.',
            confirmButtonColor: '#d33'
        });
        docInput.value = '';
        return;
    }

    // Convert to base64
    const reader = new FileReader();
    reader.onload = function(e) {
        const base64Data = e.target.result;
        $('docBase64').value = base64Data;
        $('docName').value = file.name;
        
        // Show preview
        const ext = fileName.split('.').pop();
        const iconClass = ext === 'pdf' ? 'fas fa-file-pdf text-danger' : 'fas fa-file-word text-primary';
        const sizeInKB = (file.size / 1024).toFixed(2);
        
        $('newDocIcon').className = `${iconClass} doc-icon-large`;
        $('newDocNameDisplay').textContent = file.name;
        $('newDocSize').innerHTML = `<i class="fas fa-check-circle me-1"></i>${sizeInKB} KB - Ready to upload`;
        show(newDocPreview);
    };
    reader.readAsDataURL(file);
}

// Remove current document
window.removeCurrentDocument = function() {
    Swal.fire({
        title: 'Remove Document?',
        text: 'This will remove the current cancellation document.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, remove it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $('removeDocumentInput').value = '1';
            hide($('currentDocContainer'));
            Swal.fire({
                icon: 'success',
                title: 'Removed!',
                text: 'Document will be removed when you save.',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
};

// Remove new document
window.removeNewDocument = function() {
    docInput.value = '';
    $('docBase64').value = '';
    $('docName').value = '';
    hide(newDocPreview);
};

// View document in modal
window.viewDocument = function(url, name, ext) {
    const modal = new bootstrap.Modal($('documentViewerModal'));
    $('modalDocTitle').textContent = name;
    
    const viewer = $('documentViewerContent');
    viewer.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-3 text-muted">Loading document...</p></div>';
    
    // Setup download button
    $('modalDownloadBtn').onclick = () => downloadDocument(url, name);
    
    modal.show();
    
    setTimeout(() => {
        if (ext === 'pdf') {
            // Check if it's base64
            if (url.startsWith('data:application/pdf;base64,')) {
                viewer.innerHTML = `<iframe src="${url}#toolbar=1&navpanes=0&scrollbar=1" 
                        style="width:100%;height:600px;border:none;border-radius:8px;" 
                        frameborder="0"></iframe>`;
            } else {
                viewer.innerHTML = `<iframe src="${url}#toolbar=1&navpanes=0&scrollbar=1" 
                        style="width:100%;height:600px;border:none;border-radius:8px;" 
                        frameborder="0"></iframe>`;
            }
        } else if (ext === 'docx') {
            viewer.innerHTML = `
                <div class="alert alert-info mb-3">
                    <i class="fas fa-info-circle me-2"></i>
                    Word document preview - Download for full editing capabilities
                </div>
                <div class="text-center mt-4">
                    <i class="fas fa-file-word fa-5x text-primary mb-3"></i>
                    <p class="text-muted">${name}</p>
                    <p class="small text-muted">Click download button to view this document</p>
                </div>`;
        } else {
            viewer.innerHTML = `
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Preview not available. Please download to view.
                </div>
                <div class="text-center mt-4">
                    <i class="fas fa-file fa-5x text-muted mb-3"></i>
                    <p class="text-muted">${name}</p>
                </div>`;
        }
    }, 300);
};

// Download document
window.downloadDocument = function(url, name) {
    if (url.startsWith('data:')) {
        // Handle base64 download
        const link = document.createElement('a');
        link.href = url;
        link.download = name;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    } else {
        // Handle regular URL download
        window.open(url, '_blank');
    }
};

// Image handlers
const handleFile = (input, preview, base64Input, maxSize = 2097152) => {
    input?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        if (file.size > maxSize) {
            Swal.fire('Error', `File too large. Max ${maxSize/1024/1024}MB`, 'error');
            this.value = '';
            return;
        }
        
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                base64Input && (base64Input.value = e.target.result);
                if (preview) {
                    preview.src = e.target.result;
                    show(preview.closest('#newImagePreview, #newCertPreview'));
                }
            };
            reader.readAsDataURL(file);
        }
    });
};

handleFile($('imageInput'), $('newPreviewImg'), $('imageBase64'));
handleFile($('certInput'), $('newCertImg'), $('certBase64'));

// Remove functions
window.removeImage = () => { $('removeImage').value = '1'; hide($('currentImageContainer')); };
window.removeCert = () => { $('removeCert').value = '1'; hide($('currentCertContainer')); };
window.removeNewImage = () => { $('imageInput').value = ''; $('imageBase64').value = ''; hide($('newImagePreview')); };
window.removeNewCert = () => { $('certInput').value = ''; $('certBase64').value = ''; hide($('newCertPreview')); };

// Form submit
$('editEventForm')?.addEventListener('submit', function() {
    const btn = $('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
});

@if(session('success'))
Swal.fire({icon: 'success', title: 'Success!', text: '{{ session('success') }}', timer: 3000, showConfirmButton: false});
@endif

@if($errors->any())
Swal.fire({icon: 'error', title: 'Validation Error', html: '<ul style="text-align:left;">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>'});
@endif
</script>
@endpush