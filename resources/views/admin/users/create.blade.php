@extends('admin.layouts.app')

@section('title', 'Create User')
@section('page-title', 'Create New User')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>User Details</h5>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong><i class="fas fa-exclamation-triangle me-2"></i>Validation Errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-times-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('admin.users.store') }}" method="POST" id="userForm">
                    @csrf
                    
                    <!-- Hidden input for base64 image -->
                    <input type="hidden" name="profile_picture" id="profile_picture_base64">
                    
                    <!-- Profile Picture Upload -->
                    <div class="mb-4 text-center">
                        <label class="form-label fw-bold">Profile Picture</label>
                        <div class="d-flex justify-content-center mb-3">
                            <div class="position-relative">
                                <img id="profilePreview" 
                                     src="https://ui-avatars.com/api/?name=User&size=150&background=0d6efd&color=fff" 
                                     alt="Profile Preview" 
                                     class="rounded-circle border border-3 border-primary" 
                                     style="width: 150px; height: 150px; object-fit: cover;">
                                <label for="profile_picture" class="position-absolute bottom-0 end-0 btn btn-primary btn-sm rounded-circle" 
                                       style="width: 40px; height: 40px; cursor: pointer;">
                                    <i class="fas fa-camera"></i>
                                </label>
                            </div>
                        </div>
                        <input type="file" 
                               class="form-control @error('profile_picture') is-invalid @enderror" 
                               id="profile_picture" 
                               accept="image/png, image/jpeg, image/jpg"
                               style="display: none;">
                        @error('profile_picture')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Maximum file size: 2MB (PNG, JPEG, JPG only)</small>
                    </div>

                    <!-- ID Number -->
                    <div class="mb-3">
                        <label for="id_number" class="form-label">ID Number <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('id_number') is-invalid @enderror" 
                               id="id_number" 
                               name="id_number" 
                               value="{{ old('id_number') }}" 
                               placeholder="e.g., 2024-0001" 
                               required>
                        @error('id_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Unique student/employee ID number</small>
                    </div>

                    <!-- Name Fields -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('first_name') is-invalid @enderror" 
                                   id="first_name" 
                                   name="first_name" 
                                   value="{{ old('first_name') }}" 
                                   placeholder="John" 
                                   required>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="middle_name" class="form-label">Middle Name</label>
                            <input type="text" 
                                   class="form-control @error('middle_name') is-invalid @enderror" 
                                   id="middle_name" 
                                   name="middle_name" 
                                   value="{{ old('middle_name') }}" 
                                   placeholder="Michael">
                            @error('middle_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('last_name') is-invalid @enderror" 
                                   id="last_name" 
                                   name="last_name" 
                                   value="{{ old('last_name') }}" 
                                   placeholder="Doe" 
                                   required>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               placeholder="john.doe@example.com" 
                               required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Department and Year Level -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="department" class="form-label">Department <span class="text-danger">*</span></label>
                            <select class="form-select @error('department') is-invalid @enderror" 
                                    id="department" 
                                    name="department" 
                                    required>
                                <option value="">Select Department</option>
                                <option value="BSIT" {{ old('department') == 'BSIT' ? 'selected' : '' }}>
                                    Bachelor of Science in Information Technology
                                </option>
                                <option value="BSBA" {{ old('department') == 'BSBA' ? 'selected' : '' }}>
                                    Bachelor of Science in Business Administration
                                </option>
                                <option value="BSED" {{ old('department') == 'BSED' ? 'selected' : '' }}>
                                    Bachelor of Science in Education
                                </option>
                                <option value="BEED" {{ old('department') == 'BEED' ? 'selected' : '' }}>
                                    Bachelor of Elementary Education
                                </option>
                                <option value="BSHM" {{ old('department') == 'BSHM' ? 'selected' : '' }}>
                                    Bachelor of Science in Hospitality Management
                                </option>
                            </select>
                            @error('department')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="year_level" class="form-label">Year Level <span class="text-danger">*</span></label>
                            <select class="form-select @error('year_level') is-invalid @enderror" 
                                    id="year_level" 
                                    name="year_level" 
                                    required>
                                <option value="">Select Year Level</option>
                                <option value="1" {{ old('year_level') == '1' ? 'selected' : '' }}>1st Year</option>
                                <option value="2" {{ old('year_level') == '2' ? 'selected' : '' }}>2nd Year</option>
                                <option value="3" {{ old('year_level') == '3' ? 'selected' : '' }}>3rd Year</option>
                                <option value="4" {{ old('year_level') == '4' ? 'selected' : '' }}>4th Year</option>
                            </select>
                            @error('year_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Password Fields -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">Minimum 8 characters</small>
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   required>
                        </div>
                    </div>

                    <!-- Role and Status -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select @error('role') is-invalid @enderror" 
                                    id="role" 
                                    name="role" 
                                    required>
                                <option value="">Select Role</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                                    👑 Administrator
                                </option>
                                <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>
                                    👤 Regular User
                                </option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" 
                                    name="status" 
                                    required>
                                <option value="">Select Status</option>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>
                                    ✅ Active
                                </option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                    ❌ Inactive
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success" id="submitBtn">
                            <i class="fas fa-save me-1"></i> Create User
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Users
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Toggle Password Visibility
document.getElementById('togglePassword').addEventListener('click', function() {
    const password = document.getElementById('password');
    const icon = this.querySelector('i');
    
    if (password.type === 'password') {
        password.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        password.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});

// Profile Picture Preview with Base64 Conversion
document.getElementById('profile_picture').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Validate file type
        const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
        if (!allowedTypes.includes(file.type)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid File Type',
                text: 'Only PNG, JPEG, and JPG are allowed.',
                timer: 3000
            });
            this.value = '';
            return;
        }

        // Validate file size (2MB max)
        const maxSize = 2 * 1024 * 1024; // 2MB in bytes
        if (file.size > maxSize) {
            Swal.fire({
                icon: 'error',
                title: 'File Too Large',
                text: 'File size exceeds 2MB. Please choose a smaller image.',
                timer: 3000
            });
            this.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const base64String = e.target.result;
            
            // Update preview image
            document.getElementById('profilePreview').src = base64String;
            
            // Store base64 in hidden input
            document.getElementById('profile_picture_base64').value = base64String;
        };
        reader.readAsDataURL(file);
    }
});

// Form validation before submit
document.getElementById('userForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Creating...';
});

// Show success message if exists
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session('success') }}',
        timer: 3000,
        showConfirmButton: false
    });
@endif
</script>
@endpush
@endsection