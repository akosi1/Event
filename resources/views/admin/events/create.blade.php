@extends('admin.layouts.app')
@section('title', 'Create Event')
@section('page-title', 'Create New Event')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .card-section { border: 1px solid #e3e6f0; border-radius: 8px; padding: 20px; margin-bottom: 20px; background: #f8f9fc; }
    .section-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 10px 15px; border-radius: 6px; margin-bottom: 15px; }
    .dept-checkbox { margin: 5px 0; }
    .time-input { max-width: 150px; }
    
    /* Certificate Editor Styles */
    .cert-editor { background: white; border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.1); margin-top: 15px; }
    .cert-toolbar { background: #f8f9fc; padding: 15px; border-bottom: 1px solid #e3e6f0; display: flex; gap: 10px; flex-wrap: wrap; }
    .cert-btn { background: white; border: 1px solid #d1d3e2; border-radius: 6px; padding: 8px 15px; cursor: pointer; transition: all 0.3s; }
    .cert-btn:hover { background: #667eea; color: white; }
    
    .editor-container { display: flex; height: 75vh; min-height: 500px; position: relative; }
    .canvas-area { flex: 1; border-right: 1px solid #e3e6f0; background: #fff; overflow: auto; display: flex; align-items: center; justify-content: center; }
    .properties-panel { width: 300px; padding: 20px; background: #f8f9fc; overflow-y: auto; transition: all 0.3s; }
    .properties-panel.collapsed { width: 0; padding: 0; opacity: 0; }
    
    .panel-toggle { position: absolute; right: 310px; top: 10px; z-index: 100; background: rgba(255,255,255,0.9); border: 1px solid #ddd; border-radius: 4px; padding: 5px 8px; cursor: pointer; transition: right 0.3s; }
    .properties-panel.collapsed + .panel-toggle, .panel-toggle.collapsed { right: 10px; }
    
    #cert-canvas { border: 2px solid #e3e6f0; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    .property-group { margin-bottom: 20px; background: white; border-radius: 8px; padding: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
    .property-group h6 { color: #667eea; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #f1f3f4; }
    
    .upload-area { padding: 30px; text-align: center; border: 3px dashed #d1d3e2; border-radius: 15px; margin: 20px; transition: all 0.3s; background: #f8f9fc; }
    .upload-area:hover { border-color: #667eea; background: #f0f4ff; }
    
    .signature-pad { border: 2px solid #ddd; border-radius: 8px; background: white; touch-action: none; }
    .preview-container { text-align: center; padding: 30px; background: #f8f9fc; border-radius: 10px; margin-top: 20px; display: none; }
    
    .style-btn.active { background-color: #667eea !important; color: white !important; }
    
    @media (max-width: 768px) {
        .editor-container { flex-direction: column; height: auto; }
        .canvas-area { border-right: none; border-bottom: 1px solid #e3e6f0; min-height: 400px; }
        .properties-panel { width: 100%; }
        .panel-toggle { right: 10px; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
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

                        <!-- Cancel Reason -->
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
                        <div class="card-section">
                            <div class="section-header">
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
                        <div class="card-section">
                            <div class="section-header">
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

                        <!-- Certificate Creator Section -->
                        <div class="card-section">
                            <div class="section-header">
                                <h6 class="mb-0"><i class="fas fa-certificate me-2"></i>Certificate Design (Optional)</h6>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="has_certificate" name="has_certificate" 
                                       value="1" {{ old('has_certificate') ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="has_certificate">
                                    Create certificate for this event
                                </label>
                                <div class="form-text">Design a certificate that attendees can receive after completing the event</div>
                            </div>

                            <div id="certificateDesigner" style="display: {{ old('has_certificate') ? 'block' : 'none' }};">
                                <!-- Upload Area -->
                                <div class="upload-area" id="upload-step">
                                    <i class="fas fa-image text-primary" style="font-size: 3rem;"></i>
                                    <h4 class="mt-3 text-primary">Upload Certificate Background</h4>
                                    <p class="text-muted">Recommended size: 1200×800 pixels</p>
                                    <input type="file" id="cert-bg" class="form-control mb-3" accept="image/*">
                                    <button type="button" class="btn btn-primary" onclick="loadBackground()">
                                        <i class="fas fa-upload me-2"></i>Start Designing
                                    </button>
                                </div>

                                <!-- Certificate Editor -->
                                <div class="cert-editor" id="cert-editor" style="display: none;">
                                    <div class="cert-toolbar">
                                        <button type="button" class="cert-btn" onclick="addText()">
                                            <i class="fas fa-font"></i> Text
                                        </button>
                                        <button type="button" class="cert-btn" onclick="document.getElementById('img-upload').click()">
                                            <i class="fas fa-image"></i> Image
                                        </button>
                                        <input type="file" id="img-upload" style="display: none;" accept="image/*">
                                        <button type="button" class="cert-btn" onclick="initSignature()">
                                            <i class="fas fa-signature"></i> Signature
                                        </button>
                                        <button type="button" class="cert-btn" onclick="duplicateObj()">
                                            <i class="fas fa-copy"></i> Copy
                                        </button>
                                        <button type="button" class="cert-btn" onclick="deleteObj()">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                        <button type="button" class="btn btn-success ms-auto" onclick="previewCert()">
                                            <i class="fas fa-eye me-2"></i>Preview
                                        </button>
                                    </div>
                                    
                                    <div class="editor-container">
                                        <div class="canvas-area">
                                            <canvas id="cert-canvas"></canvas>
                                        </div>
                                        
                                        <div class="properties-panel" id="props-panel">
                                            <div class="property-group">
                                                <h6><i class="fas fa-font me-2"></i>Text Properties</h6>
                                                <div class="mb-2">
                                                    <textarea class="form-control form-control-sm" id="text-content" rows="2" placeholder="Enter text..."></textarea>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label small">Size: <span id="size-val">30</span></label>
                                                    <input type="range" class="form-range" id="text-size" min="10" max="100" value="30">
                                                </div>
                                                <div class="mb-2">
                                                    <select class="form-select form-select-sm" id="text-font">
                                                        <option value="Arial">Arial</option>
                                                        <option value="Times New Roman">Times New Roman</option>
                                                        <option value="Georgia">Georgia</option>
                                                        <option value="Verdana">Verdana</option>
                                                        <option value="Helvetica">Helvetica</option>
                                                    </select>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label small">Color</label>
                                                    <input type="color" class="form-control" id="text-color" value="#000000">
                                                </div>
                                                <div class="mb-2">
                                                    <div class="btn-group w-100" role="group">
                                                        <button type="button" class="btn btn-outline-secondary btn-sm style-btn" id="bold-btn" onclick="toggleBold()">
                                                            <i class="fas fa-bold"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-secondary btn-sm style-btn" id="italic-btn" onclick="toggleItalic()">
                                                            <i class="fas fa-italic"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-secondary btn-sm style-btn" id="underline-btn" onclick="toggleUnderline()">
                                                            <i class="fas fa-underline"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="property-group">
                                                <h6><i class="fas fa-signature me-2"></i>Signature</h6>
                                                <div id="sig-container" style="display: none;">
                                                    <canvas id="sig-pad" class="signature-pad" width="250" height="120"></canvas>
                                                    <div class="d-flex gap-1 mt-2">
                                                        <button type="button" class="btn btn-primary btn-sm" onclick="addSig()">Add</button>
                                                        <button type="button" class="btn btn-secondary btn-sm" onclick="clearSig()">Clear</button>
                                                    </div>
                                                </div>
                                                <p class="text-muted small" id="sig-help">Click signature button to draw</p>
                                            </div>
                                            
                                            <div class="property-group">
                                                <h6><i class="fas fa-layer-group me-2"></i>Layer Controls</h6>
                                                <div class="d-grid gap-1">
                                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="bringFront()">Bring Front</button>
                                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="sendBack()">Send Back</button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="panel-toggle" onclick="togglePanel()">
                                            <i class="fas fa-chevron-right" id="toggle-icon"></i>
                                        </div>
                                    </div>
                                    
                                    <!-- Preview -->
                                    <div class="preview-container" id="preview-area">
                                        <h4>Certificate Preview</h4>
                                        <img id="cert-preview" class="img-fluid" style="max-height: 500px; border: 1px solid #ddd;">
                                        <div class="mt-3">
                                            <button type="button" class="btn btn-secondary" onclick="closePreview()">Back to Editor</button>
                                            <button type="button" class="btn btn-success" onclick="saveCert()">Save Design</button>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" id="cert_data" name="certificate_data">
                            </div>
                        </div>

                        <!-- Event Image Upload -->
                        <div class="mb-4">
                            <label for="image" class="form-label fw-semibold">Event Image</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                id="image" name="image" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">Supported: JPG, PNG, GIF, WebP. Max size: 2MB</div>
                            
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

                        <!-- Submit -->
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
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>
<script>
let canvas, sigPad, selected, saved = false;

document.addEventListener('DOMContentLoaded', function() {
    initializeEventHandlers();
});

function initializeEventHandlers() {
    // Department exclusivity toggle
    const isExclusiveCheckbox = document.getElementById('is_exclusive');
    const departmentSelection = document.getElementById('departmentSelection');
    
    if (isExclusiveCheckbox && departmentSelection) {
        isExclusiveCheckbox.addEventListener('change', function() {
            departmentSelection.style.display = this.checked ? 'block' : 'none';
        });
    }

    // Recurring event toggle
    const isRecurringCheckbox = document.getElementById('is_recurring');
    const recurrenceSettings = document.getElementById('recurrenceSettings');
    
    if (isRecurringCheckbox && recurrenceSettings) {
        isRecurringCheckbox.addEventListener('change', function() {
            recurrenceSettings.style.display = this.checked ? 'block' : 'none';
        });
    }

    // Certificate toggle
    const hasCertificateCheckbox = document.getElementById('has_certificate');
    const certificateDesigner = document.getElementById('certificateDesigner');
    
    if (hasCertificateCheckbox && certificateDesigner) {
        hasCertificateCheckbox.addEventListener('change', function() {
            certificateDesigner.style.display = this.checked ? 'block' : 'none';
        });
    }

    // Recurrence pattern change
    const recurrencePattern = document.getElementById('recurrence_pattern');
    const intervalText = document.getElementById('intervalText');
    
    if (recurrencePattern && intervalText) {
        recurrencePattern.addEventListener('change', function() {
            const pattern = this.value;
            let text = 'day(s)';
            
            switch(pattern) {
                case 'weekly': text = 'week(s)'; break;
                case 'monthly': text = 'month(s)'; break;
                case 'yearly': text = 'year(s)'; break;
                case 'weekdays': 
                    text = 'weekday';
                    document.getElementById('recurrence_interval').style.display = 'none';
                    break;
                default:
                    document.getElementById('recurrence_interval').style.display = 'block';
            }
            intervalText.textContent = text;
        });
    }

    // Status change for cancel reason
    const statusSelect = document.getElementById('status');
    const cancelReasonRow = document.getElementById('cancelReasonRow');
    
    if (statusSelect && cancelReasonRow) {
        statusSelect.addEventListener('change', function() {
            const showReason = ['postponed', 'cancelled'].includes(this.value);
            cancelReasonRow.style.display = showReason ? 'block' : 'none';
        });
    }

    // Image preview functionality
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    
    if (imageInput && imagePreview && previewImg) {
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
    }

    // Certificate editor property event listeners
    setupPropertyEventListeners();

    // Image upload handler for certificate
    const imgUpload = document.getElementById('img-upload');
    if (imgUpload) {
        imgUpload.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            const reader = new FileReader();
            reader.onload = function(e) {
                fabric.Image.fromURL(e.target.result, function(img) {
                    if (img.width > 300) img.scaleToWidth(300);
                    img.set({ 
                        left: 50, 
                        top: 50, 
                        cornerStyle: 'circle', 
                        cornerColor: '#667eea',
                        transparentCorners: false,
                        borderColor: '#667eea'
                    });
                    canvas.add(img);
                    canvas.setActiveObject(img);
                });
            };
            reader.readAsDataURL(file);
        });
    }

    // Form validation
    const eventForm = document.getElementById('eventForm');
    if (eventForm) {
        eventForm.addEventListener('submit', function(e) {
            const hasCert = document.getElementById('has_certificate')?.checked;
            if (hasCert && !saved) {
                e.preventDefault();
                alert('Please save your certificate design before creating the event.');
                return false;
            }
        });
    }
}

function setupPropertyEventListeners() {
    // Text content change
    const textContent = document.getElementById('text-content');
    if (textContent) {
        textContent.addEventListener('input', debounce(updateTextProperties, 300));
        textContent.addEventListener('blur', updateTextProperties);
    }

    // Font size change
    const textSize = document.getElementById('text-size');
    if (textSize) {
        textSize.addEventListener('input', function() {
            document.getElementById('size-val').textContent = this.value;
            updateTextProperties();
        });
    }

    // Font family change
    const textFont = document.getElementById('text-font');
    if (textFont) {
        textFont.addEventListener('change', updateTextProperties);
    }

    // Color change
    const textColor = document.getElementById('text-color');
    if (textColor) {
        textColor.addEventListener('input', updateTextProperties);
        textColor.addEventListener('change', updateTextProperties);
    }
}

// Debounce function to limit frequent updates
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Initialize canvas
function initCanvas() {
    const canvasElement = document.getElementById('cert-canvas');
    if (!canvasElement) return;

    canvas = new fabric.Canvas('cert-canvas', {
        width: 900,
        height: 600,
        backgroundColor: '#ffffff',
        selection: true
    });

    // Canvas events
    canvas.on('selection:created', function(e) {
        selected = e.selected[0];
        updatePropertiesPanel(selected);
    });

    canvas.on('selection:updated', function(e) {
        selected = e.selected[0];
        updatePropertiesPanel(selected);
    });

    canvas.on('selection:cleared', function() {
        selected = null;
        clearPropertiesPanel();
    });

    canvas.on('object:modified', function(e) {
        if (selected) {
            updatePropertiesPanel(selected);
        }
    });

    // Keyboard controls
    document.addEventListener('keydown', function(e) {
        if (!canvas || !canvas.getActiveObject()) return;
        
        if (e.key === 'Delete' && selected) {
            e.preventDefault();
            deleteObj();
        }
        if (e.key === 'Escape') {
            canvas.discardActiveObject().renderAll();
        }
        if (e.ctrlKey && e.key === 'c' && selected) {
            e.preventDefault();
            duplicateObj();
        }
    });
}

// Load background - now moveable
function loadBackground() {
    const file = document.getElementById('cert-bg').files[0];
    if (!file) {
        alert('Please select a background image.');
        return;
    }

    if (!canvas) initCanvas();

    const reader = new FileReader();
    reader.onload = function(e) {
        fabric.Image.fromURL(e.target.result, function(img) {
            const scale = Math.min(canvas.width / img.width, canvas.height / img.height);
            
            // Make background moveable and selectable
            img.set({
                scaleX: scale,
                scaleY: scale,
                left: 0,
                top: 0,
                selectable: true,
                moveable: true,
                cornerStyle: 'circle',
                cornerColor: '#667eea',
                transparentCorners: false,
                borderColor: '#667eea',
                name: 'background'
            });
            
            canvas.add(img);
            canvas.sendToBack(img);
            
            document.getElementById('upload-step').style.display = 'none';
            document.getElementById('cert-editor').style.display = 'block';
        });
    };
    reader.readAsDataURL(file);
}

// Add text
function addText() {
    if (!canvas) return;
    
    const text = new fabric.IText('Double-click to edit', {
        left: 100,
        top: 100,
        fontFamily: 'Arial',
        fontSize: 30,
        fill: '#000000',
        editable: true,
        cornerStyle: 'circle',
        cornerColor: '#667eea',
        transparentCorners: false,
        borderColor: '#667eea',
        name: 'text'
    });
    
    canvas.add(text);
    canvas.setActiveObject(text);
}

// Update text properties from panel
function updateTextProperties() {
    if (!selected || (selected.type !== 'textbox' && selected.type !== 'i-text')) return;
    
    const content = document.getElementById('text-content').value;
    const size = parseInt(document.getElementById('text-size').value);
    const font = document.getElementById('text-font').value;
    const color = document.getElementById('text-color').value;
    
    // Update selected object properties
    selected.set({
        text: content,
        fontSize: size,
        fontFamily: font,   
        fill: color
    });
    
    canvas.renderAll();
}

// Update properties panel based on selected object
function updatePropertiesPanel(obj) {
    if (!obj) return;
    
    if (obj.type === 'textbox' || obj.type === 'i-text') {
        // Update text properties
        document.getElementById('text-content').value = obj.text || '';
        document.getElementById('text-size').value = obj.fontSize || 30;
        document.getElementById('text-font').value = obj.fontFamily || 'Arial';
        document.getElementById('text-color').value = obj.fill || '#000000';
        document.getElementById('size-val').textContent = obj.fontSize || 30;
        
        // Update style buttons
        updateStyleButtons(obj);
    }
}

function clearPropertiesPanel() {
    document.getElementById('text-content').value = '';
    document.getElementById('text-size').value = 30;
    document.getElementById('text-font').value = 'Arial';
    document.getElementById('text-color').value = '#000000';
    document.getElementById('size-val').textContent = '30';
    resetStyleButtons();
}

function updateStyleButtons(obj) {
    const boldBtn = document.getElementById('bold-btn');
    const italicBtn = document.getElementById('italic-btn');
    const underlineBtn = document.getElementById('underline-btn');
    
    if (boldBtn) boldBtn.classList.toggle('active', obj.fontWeight === 'bold');
    if (italicBtn) italicBtn.classList.toggle('active', obj.fontStyle === 'italic');
    if (underlineBtn) underlineBtn.classList.toggle('active', obj.underline === true);
}

function resetStyleButtons() {
    ['bold-btn', 'italic-btn', 'underline-btn'].forEach(id => {
        const btn = document.getElementById(id);
        if (btn) btn.classList.remove('active');
    });
}

// Text styling functions
function toggleBold() {
    if (!selected || (selected.type !== 'textbox' && selected.type !== 'i-text')) return;
    const isBold = selected.fontWeight === 'bold';
    selected.set('fontWeight', isBold ? 'normal' : 'bold');
    canvas.renderAll();
    updateStyleButtons(selected);
}

function toggleItalic() {
    if (!selected || (selected.type !== 'textbox' && selected.type !== 'i-text')) return;
    const isItalic = selected.fontStyle === 'italic';
    selected.set('fontStyle', isItalic ? 'normal' : 'italic');
    canvas.renderAll();
    updateStyleButtons(selected);
}

function toggleUnderline() {
    if (!selected || (selected.type !== 'textbox' && selected.type !== 'i-text')) return;
    const isUnderline = selected.underline === true;
    selected.set('underline', !isUnderline);
    canvas.renderAll();
    updateStyleButtons(selected);
}

// Signature functions
function initSignature() {
    const sigContainer = document.getElementById('sig-container');
    const sigHelp = document.getElementById('sig-help');
    
    if (sigContainer) sigContainer.style.display = 'block';
    if (sigHelp) sigHelp.style.display = 'none';
    
    if (sigPad) {
        sigPad.dispose();
    }
    
    sigPad = new fabric.Canvas('sig-pad', {
        isDrawingMode: true,
        width: 250,
        height: 120,
        backgroundColor: '#ffffff'
    });
    
    if (sigPad.freeDrawingBrush) {
        sigPad.freeDrawingBrush.width = 2;
        sigPad.freeDrawingBrush.color = '#000000';
    }
}

function addSig() {
    if (!sigPad || sigPad.isEmpty()) {
        alert('Draw signature first.');
        return;
    }
    
    const sigData = sigPad.toDataURL({ backgroundColor: 'transparent' });
    
    fabric.Image.fromURL(sigData, function(img) {
        img.set({
            left: 100,
            top: 400,
            scaleX: 0.8,
            scaleY: 0.8,
            cornerStyle: 'circle',
            cornerColor: '#667eea',
            transparentCorners: false,
            borderColor: '#667eea',
            name: 'signature'
        });
        canvas.add(img);
        canvas.setActiveObject(img);
        clearSig();
    });
}

function clearSig() {
    if (sigPad) {
        sigPad.clear();
        sigPad.backgroundColor = '#ffffff';
        sigPad.renderAll();
    }
}

// Object controls
function duplicateObj() {
    if (!selected) {
        alert('Please select an object to duplicate.');
        return;
    }
    
    selected.clone(function(cloned) {
        cloned.set({
            left: selected.left + 20,
            top: selected.top + 20,
        });
        canvas.add(cloned);
        canvas.setActiveObject(cloned);
        canvas.renderAll();
    });
}

function deleteObj() {
    if (!selected) {
        alert('Please select an object to delete.');
        return;
    }
    canvas.remove(selected);
    selected = null;
    clearPropertiesPanel();
}

function bringFront() {
    if (!selected) {
        alert('Please select an object.');
        return;
    }
    canvas.bringToFront(selected);
    canvas.renderAll();
}

function sendBack() {
    if (!selected) {
        alert('Please select an object.');
        return;
    }
    canvas.sendToBack(selected);
    canvas.renderAll();
}

// Panel toggle
function togglePanel() {
    const panel = document.getElementById('props-panel');
    const icon = document.getElementById('toggle-icon');
    const toggle = document.querySelector('.panel-toggle');
    
    if (panel && icon && toggle) {
        if (panel.classList.contains('collapsed')) {
            panel.classList.remove('collapsed');
            toggle.classList.remove('collapsed');
            icon.className = 'fas fa-chevron-right';
        } else {
            panel.classList.add('collapsed');
            toggle.classList.add('collapsed');
            icon.className = 'fas fa-chevron-left';
        }
    }
}

// Preview functions
function previewCert() {
    if (!canvas) {
        alert('No canvas available for preview.');
        return;
    }
    
    const dataURL = canvas.toDataURL({ format: 'png', quality: 1, multiplier: 2 });
    
    const previewImg = document.getElementById('cert-preview');
    const previewArea = document.getElementById('preview-area');
    const editorContainer = document.querySelector('.editor-container');
    const toolbar = document.querySelector('.cert-toolbar');
    
    if (previewImg) previewImg.src = dataURL;
    if (previewArea) previewArea.style.display = 'block';
    if (editorContainer) editorContainer.style.display = 'none';
    if (toolbar) toolbar.style.display = 'none';
}

function closePreview() {
    const previewArea = document.getElementById('preview-area');
    const editorContainer = document.querySelector('.editor-container');
    const toolbar = document.querySelector('.cert-toolbar');
    
    if (previewArea) previewArea.style.display = 'none';
    if (editorContainer) editorContainer.style.display = 'flex';
    if (toolbar) toolbar.style.display = 'flex';
}

function saveCert() {
    if (!canvas) {
        alert('No certificate design to save.');
        return;
    }
    
    const certData = {
        canvas: canvas.toJSON(['selectable', 'editable', 'name']),
        preview: canvas.toDataURL({ format: 'png', quality: 1, multiplier: 2 }),
        dimensions: { width: canvas.width, height: canvas.height }
    };
    
    const certDataInput = document.getElementById('cert_data');
    if (certDataInput) {
        certDataInput.value = JSON.stringify(certData);
    }
    
    saved = true;
    
    alert('Certificate design saved successfully!');
    setTimeout(() => closePreview(), 1000);
}

function removePreview() {
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    
    if (imageInput) imageInput.value = '';
    if (imagePreview) imagePreview.style.display = 'none';
}
</script>
@endpush
@endsection