{{-- resources/views/profile/edit.blade.php --}}
<x-app-layout>
<div class="profile-page">
    {{-- Success/Error Messages --}}
    @if(session('status') === 'profile-updated')
    <div class="toast toast-success">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
        </svg>
        <span>Profile updated successfully!</span>
    </div>
    @endif

    @if(session('error'))
    <div class="toast toast-error">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
            <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
        </svg>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    @if(session('status') === 'password-updated')
    <div class="toast toast-success">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
        </svg>
        <span>Password updated successfully!</span>
    </div>
    @endif

    @if($errors->updatePassword->any())
    <div class="toast toast-error">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
            <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
        </svg>
        <span>{{ $errors->updatePassword->first() }}</span>
    </div>
    @endif

    <div class="container">
        <form action="{{ route('profile.update') }}" method="POST" id="profile-form">
            @csrf
            @method('PUT')
            
            <input type="hidden" name="profile_picture_base64" id="profile_picture_base64">
            <input type="file" id="profile_picture_header" name="profile_picture_file" accept="image/jpeg,image/png,image/gif" hidden onchange="previewImage(event)">

            {{-- Profile Information Card --}}
            <div class="profile-card">
                {{-- Header with Title --}}
                <div class="card-header">
                    <h2 class="page-title">Personal Information</h2>
                </div>

                {{-- Profile Photo Section --}}
                <div class="photo-section">
                    <div class="photo-wrapper" onclick="togglePhotoOverlay()">
                        <img id="header-avatar" src="{{ $user->profile_picture_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->full_name) . '&background=764ba2&color=fff&size=400' }}" alt="{{ $user->full_name }}">
                        <div class="photo-overlay" id="photoOverlay">
                            <button type="button" class="btn-change-photo" onclick="event.stopPropagation(); document.getElementById('profile_picture_header').click()">
                                <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                    <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                </svg>
                                Change Photo
                            </button>
                            <button type="button" class="btn-save-photo" id="savePhotoBtn" style="display: none;" onclick="event.stopPropagation(); savePhotoChange()">
                                <svg width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
                                </svg>
                                Save Photo
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Editable Information Section --}}
                <div class="info-section">
                    <div class="info-row single-line">
                        <label class="info-label">ID Number:</label>
                        <span class="info-value">{{ $user->id_number }}</span>
                    </div>

                    <div class="info-row single-line">
                        <label class="info-label">Course:</label>
                        <span class="info-value course-text">{{ strtoupper($user->department ?? 'BACHELOR OF SCIENCE IN INFORMATION TECHNOLOGY') }}</span>
                    </div>

                    <div class="info-row single-line">
                        <label class="info-label" for="first_name">First Name:</label>
                        <input type="text" id="first_name" name="first_name" class="info-input-inline editable @error('first_name') error @enderror" value="{{ old('first_name', $user->first_name) }}" required>
                    </div>
                    @error('first_name')
                    <span class="error-message-inline">{{ $message }}</span>
                    @enderror

                    <div class="info-row single-line">
                        <label class="info-label" for="middle_name">Middle Name:</label>
                        <input type="text" id="middle_name" name="middle_name" class="info-input-inline editable @error('middle_name') error @enderror" value="{{ old('middle_name', $user->middle_name) }}" placeholder="Optional">
                    </div>
                    @error('middle_name')
                    <span class="error-message-inline">{{ $message }}</span>
                    @enderror

                    <div class="info-row single-line">
                        <label class="info-label" for="last_name">Last Name:</label>
                        <input type="text" id="last_name" name="last_name" class="info-input-inline editable @error('last_name') error @enderror" value="{{ old('last_name', $user->last_name) }}" required>
                    </div>
                    @error('last_name')
                    <span class="error-message-inline">{{ $message }}</span>
                    @enderror

                    <div class="info-row single-line">
                        <label class="info-label">Year Level:</label>
                        <span class="info-value">{{ $user->year_level_name ?? '1st Year' }}</span>
                    </div>
                </div>

                {{-- Save Button --}}
                <div class="form-actions">
                    <button type="button" class="btn-save" onclick="showSaveModal()">
                        <svg width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v7.293l2.646-2.647a.5.5 0 0 1 .708.708l-3.5 3.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L7.5 9.293V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h2.5a.5.5 0 0 1 0 1H2z"/>
                        </svg>
                        Save Changes
                    </button>
                </div>
            </div>
        </form>

        {{-- Change Password Card --}}
        <div class="profile-card password-card">
            <div class="card-header">
                <h2 class="page-title">Change Password</h2>
            </div>

            <form action="{{ route('password.update') }}" method="POST" id="password-form">
                @csrf
                @method('PUT')

                <div class="password-section">
                    <div class="form-group">
                        <label class="form-label">Current Password</label>
                        <div class="password-input">
                            <input type="password" id="current_password" name="current_password" class="form-control @error('current_password', 'updatePassword') error @enderror" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('current_password')">
                                <svg class="eye-icon" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                </svg>
                            </button>
                        </div>
                        @error('current_password', 'updatePassword')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">New Password</label>
                        <div class="password-input">
                            <input type="password" id="password" name="password" class="form-control @error('password', 'updatePassword') error @enderror" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('password')">
                                <svg class="eye-icon" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                </svg>
                            </button>
                        </div>
                        @error('password', 'updatePassword')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Confirm New Password</label>
                        <div class="password-input">
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('password_confirmation')">
                                <svg class="eye-icon" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-update-password">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Save Confirmation Modal --}}
<div id="saveModal" class="modal">
    <div class="modal-overlay" onclick="closeSaveModal()"></div>
    <div class="modal-content">
        <div class="modal-icon">
            <svg width="48" height="48" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                <path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
            </svg>
        </div>
        <h3 class="modal-title">Save Changes?</h3>
        <p class="modal-message">Are you sure you want to save these changes to your profile?</p>
        <div class="modal-actions">
            <button type="button" class="btn-modal btn-cancel" onclick="closeSaveModal()">Cancel</button>
            <button type="button" class="btn-modal btn-confirm" onclick="confirmSave()">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
                </svg>
                Yes, Save
            </button>
        </div>
    </div>
</div>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background: #f5f5f5 !important;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }

    .profile-page {
        min-height: 100vh;
        background: #f5f5f5 !important;
        padding: 7rem 1rem 2rem;
    }

    .container {
        max-width: 1000px;
        margin: 0 auto;
    }

    /* Toast Notifications */
    .toast {
        position: fixed;
        top: 2rem;
        right: 2rem;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.875rem;
        font-weight: 500;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        animation: slideInRight 0.3s ease, fadeOut 0.3s ease 4.7s;
        z-index: 1000;
    }

    .toast-success {
        background: #10b981;
        color: white;
    }

    .toast-error {
        background: #ef4444;
        color: white;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes fadeOut {
        to {
            opacity: 0;
            transform: translateX(100%);
        }
    }

    /* Profile Card */
    .profile-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 2.5rem 3rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    /* Card Header */
    .card-header {
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .page-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #111827;
        margin: 0;
    }

    /* Photo Section */
    .photo-section {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .photo-wrapper {
        width: 200px;
        height: 200px;
        border: 3px solid #d1d5db;
        border-radius: 50%;
        overflow: hidden;
        background: #f9fafb;
        position: relative;
        cursor: pointer;
        transition: all 0.3s;
    }

    .photo-wrapper:hover {
        border-color: #9ca3af;
    }

    .photo-wrapper:hover .photo-overlay {
        opacity: 1;
    }

    .photo-overlay.show {
        opacity: 1;
    }

    .photo-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .photo-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .btn-change-photo,
    .btn-save-photo {
        background: white;
        color: #374151;
        border: none;
        padding: 0.5rem 1.25rem;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .btn-change-photo:hover {
        background: #f3f4f6;
        transform: translateY(-1px);
    }

    .btn-save-photo {
        background: #10b981;
        color: white;
    }

    .btn-save-photo:hover {
        background: #059669;
        transform: translateY(-1px);
    }

    .btn-capture {
        background: #fbbf24;
        color: #78350f;
        border: none;
        padding: 0.625rem 2rem;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }

    .btn-capture:hover {
        background: #f59e0b;
    }

    /* Information Section */
    .info-section {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .info-row {
        display: grid;
        grid-template-columns: 160px 1fr;
        gap: 1rem;
        align-items: center;
        font-size: 0.9375rem;
    }

    .info-row.single-line {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .info-label {
        font-weight: 600;
        color: #374151;
        flex-shrink: 0;
        min-width: 130px;
    }

    .info-value {
        color: #111827;
        font-weight: 500;
        font-size: 1rem;
    }

    .info-input-inline {
        flex: 1;
        padding: 0.625rem 0.875rem;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 1rem;
        color: #111827;
        font-family: inherit;
        background: white;
        transition: all 0.2s;
    }

    .info-input-inline.editable:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .info-input-inline.error {
        border-color: #ef4444;
    }

    .course-text {
        font-weight: 600;
    }

    .error-message-inline {
        font-size: 0.8125rem;
        color: #ef4444;
        margin-left: 146px;
        display: block;
        margin-top: -0.75rem;
    }

    /* Form Actions */
    .form-actions {
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: flex-end;
    }

    .btn-save {
        background: #10b981;
        color: white;
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 6px;
        font-size: 0.9375rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-save:hover {
        background: #059669;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    /* Password Card */
    .password-card {
        margin-top: 2rem;
    }

    .password-section {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .form-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
    }

    .form-control {
        width: 100%;
        padding: 0.625rem 0.875rem;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 0.9375rem;
        color: #111827;
        transition: all 0.2s;
        font-family: inherit;
    }

    .form-control:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-control.error {
        border-color: #ef4444;
    }

    .password-input {
        position: relative;
    }

    .password-input input {
        padding-right: 3rem;
    }

    .toggle-password {
        position: absolute;
        right: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #9ca3af;
        cursor: pointer;
        padding: 0.5rem;
        display: flex;
        align-items: center;
        transition: color 0.2s;
    }

    .toggle-password:hover {
        color: #6b7280;
    }

    .btn-update-password {
        background: #3b82f6;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 6px;
        font-size: 0.9375rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
        margin-top: 0.5rem;
    }

    .btn-update-password:hover {
        background: #2563eb;
    }

    /* Modal */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }

    .modal.active {
        display: flex;
    }

    .modal-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        animation: fadeIn 0.3s ease;
    }

    .modal-content {
        position: relative;
        background: white;
        border-radius: 12px;
        padding: 2rem;
        max-width: 400px;
        width: 90%;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        animation: scaleIn 0.3s ease;
        text-align: center;
    }

    .modal-icon {
        width: 64px;
        height: 64px;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        color: white;
    }

    .modal-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 0.5rem;
    }

    .modal-message {
        font-size: 0.9375rem;
        color: #6b7280;
        margin-bottom: 2rem;
        line-height: 1.6;
    }

    .modal-actions {
        display: flex;
        gap: 1rem;
        justify-content: center;
    }

    .btn-modal {
        padding: 0.75rem 2rem;
        border-radius: 8px;
        font-size: 0.9375rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-cancel {
        background: #f3f4f6;
        color: #374151;
    }

    .btn-cancel:hover {
        background: #e5e7eb;
    }

    .btn-confirm {
        background: #10b981;
        color: white;
    }

    .btn-confirm:hover {
        background: #059669;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    @keyframes scaleIn {
        from {
            transform: scale(0.9);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .profile-page {
            padding: 2rem 1rem;
        }

        .profile-card {
            padding: 1.5rem;
        }

        .photo-section {
            padding-bottom: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .photo-wrapper {
            width: 150px;
            height: 150px;
            border-width: 2px;
        }

        .photo-wrapper img {
            object-fit: cover;
            object-position: center;
        }

        .photo-overlay {
            opacity: 0;
            background: rgba(0, 0, 0, 0.6);
            flex-direction: row;
            padding: 0.5rem;
        }

        .photo-overlay.show {
            opacity: 1;
        }

        .btn-change-photo,
        .btn-save-photo {
            padding: 0.375rem 0.75rem;
            font-size: 0.75rem;
            gap: 0.25rem;
            flex: 1;
        }

        .btn-change-photo svg,
        .btn-save-photo svg {
            width: 14px;
            height: 14px;
        }

        .card-header {
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
        }

        .page-title {
            font-size: 1rem;
        }

        .info-section {
            gap: 1rem;
        }

        .info-row.single-line {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.375rem;
        }

        .info-label {
            font-size: 0.8125rem;
            color: #6b7280;
            min-width: auto;
            font-weight: 600;
        }

        .info-value {
            font-size: 0.9375rem;
            padding-left: 0;
        }

        .info-input-inline {
            width: 100%;
            font-size: 0.9375rem;
            padding: 0.5rem 0.75rem;
        }

        .error-message-inline {
            margin-left: 0;
            margin-top: -0.25rem;
        }

        .form-actions {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
        }

        .btn-save {
            width: 100%;
            justify-content: center;
        }

        .toast {
            right: 1rem;
            left: 1rem;
        }

        .modal-content {
            padding: 1.5rem;
        }

        .modal-actions {
            flex-direction: column;
        }

        .btn-modal {
            width: 100%;
        }

        .password-card {
            padding: 1.5rem;
        }

        .password-section {
            gap: 1.25rem;
        }

        .btn-update-password {
            width: 100%;
        }
    }
</style>

<script>
    // Toggle photo overlay on tap/click
    function togglePhotoOverlay() {
        const overlay = document.getElementById('photoOverlay');
        overlay.classList.toggle('show');
    }

    // Close overlay when clicking outside
    document.addEventListener('click', function(e) {
        const photoWrapper = document.querySelector('.photo-wrapper');
        const overlay = document.getElementById('photoOverlay');
        
        if (!photoWrapper.contains(e.target)) {
            overlay.classList.remove('show');
        }
    });

    // Image preview
    function previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            if (file.size > 2 * 1024 * 1024) {
                alert('Image size must be less than 2MB');
                event.target.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const base64Data = e.target.result;
                document.getElementById('header-avatar').src = base64Data;
                document.getElementById('profile_picture_base64').value = base64Data;
                
                // Show save photo button
                document.getElementById('savePhotoBtn').style.display = 'flex';
                document.getElementById('photoOverlay').classList.add('show');
            }
            reader.readAsDataURL(file);
        }
    }

    // Save photo change
    function savePhotoChange() {
        const base64Data = document.getElementById('profile_picture_base64').value;
        
        if (!base64Data) {
            alert('Please select a photo first');
            return;
        }

        // Create a form and submit only the photo
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("profile.update") }}';
        
        const csrfToken = document.querySelector('input[name="_token"]').value;
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PUT';
        
        const csrfField = document.createElement('input');
        csrfField.type = 'hidden';
        csrfField.name = '_token';
        csrfField.value = csrfToken;
        
        const photoField = document.createElement('input');
        photoField.type = 'hidden';
        photoField.name = 'profile_picture_base64';
        photoField.value = base64Data;
        
        // Add current name fields to prevent validation errors
        const firstNameField = document.createElement('input');
        firstNameField.type = 'hidden';
        firstNameField.name = 'first_name';
        firstNameField.value = document.getElementById('first_name').value;
        
        const middleNameField = document.createElement('input');
        middleNameField.type = 'hidden';
        middleNameField.name = 'middle_name';
        middleNameField.value = document.getElementById('middle_name').value;
        
        const lastNameField = document.createElement('input');
        lastNameField.type = 'hidden';
        lastNameField.name = 'last_name';
        lastNameField.value = document.getElementById('last_name').value;
        
        form.appendChild(csrfField);
        form.appendChild(methodField);
        form.appendChild(photoField);
        form.appendChild(firstNameField);
        form.appendChild(middleNameField);
        form.appendChild(lastNameField);
        
        document.body.appendChild(form);
        form.submit();
    }

    // Show save modal
    function showSaveModal() {
        // Validate required fields
        const firstName = document.getElementById('first_name').value.trim();
        const lastName = document.getElementById('last_name').value.trim();

        if (!firstName || !lastName) {
            alert('Please fill in all required fields (First Name and Last Name)');
            return;
        }

        document.getElementById('saveModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    // Close save modal
    function closeSaveModal() {
        document.getElementById('saveModal').classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    // Confirm save and submit form
    function confirmSave() {
        closeSaveModal();
        document.getElementById('profile-form').submit();
    }

    // Close modal with ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeSaveModal();
        }
    });

    // Toggle password visibility
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const button = field.parentElement.querySelector('.toggle-password');
        
        if (field.type === 'password') {
            field.type = 'text';
            button.innerHTML = `
                <svg class="eye-icon" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                    <path d="m10.79 12.912-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588zM5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z"/>
                    <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.83-2.829zm4.95.708-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829zm3.171 6-12-12 .708-.708 12 12-.708.708z"/>
                </svg>
            `;
        } else {
            field.type = 'password';
            button.innerHTML = `
                <svg class="eye-icon" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                </svg>
            `;
        }
    }

    // Auto-hide toast messages
    setTimeout(() => {
        const toasts = document.querySelectorAll('.toast');
        toasts.forEach(toast => {
            toast.style.animation = 'fadeOut 0.3s ease forwards';
            setTimeout(() => toast.remove(), 300);
        });
    }, 5000);

    // Auto-scroll to password section if there are password errors
    @if($errors->updatePassword->any())
    document.addEventListener('DOMContentLoaded', function() {
        const passwordCard = document.querySelector('.password-card');
        if (passwordCard) {
            passwordCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Add highlight effect
            passwordCard.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.3)';
            setTimeout(() => {
                passwordCard.style.transition = 'box-shadow 0.5s ease';
                passwordCard.style.boxShadow = '0 1px 3px rgba(0, 0, 0, 0.1)';
            }, 2000);
        }
    });
    @endif
</script>
</x-app-layout>