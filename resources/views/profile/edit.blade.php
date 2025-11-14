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

    {{-- Validation Errors --}}
    @if($errors->updatePassword->any())
    <div class="toast toast-error">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
            <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
        </svg>
        <span>{{ $errors->updatePassword->first() }}</span>
    </div>
    @endif

    @if($errors->any() && !$errors->updatePassword->any())
    <div class="toast toast-error">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
            <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
        </svg>
        <span>{{ $errors->first() }}</span>
    </div>
    @endif

    <div class="container">
        {{-- Profile Header Section --}}
        <div class="profile-header">
            <div class="profile-header-content">
                <div class="profile-avatar-large">
                    <img id="header-avatar" src="{{ $user->profile_picture_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->full_name) . '&background=764ba2&color=fff&size=300' }}" alt="{{ $user->full_name }}">
                    <div class="avatar-overlay">
                        <label for="profile_picture_header" class="avatar-edit-btn">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                            </svg>
                        </label>
                    </div>
                </div>
                <div class="profile-info">
                    <h1 class="profile-name">{{ $user->full_name }}</h1>
                    <p class="profile-handle">{{ '@' . strtolower(str_replace(' ', '', $user->first_name . $user->last_name)) }}</p>
                    <div class="profile-badges">
                        <span class="badge badge-role">{{ ucfirst($user->role) }}</span>
                        <span class="badge badge-status badge-{{ $user->status }}">{{ ucfirst($user->status) }}</span>
                    </div>
                </div>
            </div>
            <div class="info-cards">
                <div class="info-card">
                    <div class="info-card-icon">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3Zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                        </svg>
                    </div>
                    <div class="info-card-content">
                        <span class="info-card-label">ID Number</span>
                        <span class="info-card-value">{{ $user->id_number }}</span>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-card-icon icon-red">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z"/>
                        </svg>
                    </div>
                    <div class="info-card-content">
                        <span class="info-card-label">Email</span>
                        <span class="info-card-value">{{ $user->email }}</span>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-card-icon icon-red">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M6.5 1A1.5 1.5 0 0 0 5 2.5V3H1.5A1.5 1.5 0 0 0 0 4.5v1.384l7.614 2.03a1.5 1.5 0 0 0 .772 0L16 5.884V4.5A1.5 1.5 0 0 0 14.5 3H11v-.5A1.5 1.5 0 0 0 9.5 1h-3zm0 1h3a.5.5 0 0 1 .5.5V3H6v-.5a.5.5 0 0 1 .5-.5z"/>
                            <path d="M0 12.5A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5V6.85L8.129 8.947a.5.5 0 0 1-.258 0L0 6.85v5.65z"/>
                        </svg>
                    </div>
                    <div class="info-card-content">
                        <span class="info-card-label">Department</span>
                        <span class="info-card-value">{{ $user->department }}</span>
                    </div>
                </div>

                @if($user->year_level)
                <div class="info-card">
                    <div class="info-card-icon">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8.211 2.047a.5.5 0 0 0-.422 0l-7.5 3.5a.5.5 0 0 0 .025.917l7.5 3a.5.5 0 0 0 .372 0L14 7.14V13a1 1 0 0 0-1 1v2h3v-2a1 1 0 0 0-1-1V6.739l.686-.275a.5.5 0 0 0 .025-.917l-7.5-3.5Z"/>
                            <path d="M4.176 9.032a.5.5 0 0 0-.656.327l-.5 1.7a.5.5 0 0 0 .294.605l4.5 1.8a.5.5 0 0 0 .372 0l4.5-1.8a.5.5 0 0 0 .294-.605l-.5-1.7a.5.5 0 0 0-.656-.327L8 10.466 4.176 9.032Z"/>
                        </svg>
                    </div>
                    <div class="info-card-content">
                        <span class="info-card-label">Year Level</span>
                        <span class="info-card-value">{{ $user->year_level_name }}</span>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Tabs Navigation --}}
        <div class="tabs">
            <button class="tab active" data-tab="profile">
                <svg width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3Zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                </svg>
                Profile Information
            </button>
            <button class="tab" data-tab="password">
                <svg width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                </svg>
                Security
            </button>
        </div>

        {{-- Profile Information Tab --}}
        <div class="tab-content active" id="profile-tab">
            <form action="{{ route('profile.update') }}" method="POST" id="profile-form" class="modern-form">
                @csrf
                @method('PUT')

                <input type="hidden" name="profile_picture_base64" id="profile_picture_base64">
                <input type="hidden" name="remove_profile_picture" id="remove_profile_picture" value="0">
                <input type="file" id="profile_picture_header" name="profile_picture_file" accept="image/jpeg,image/png,image/gif" hidden onchange="previewImage(event)">

                <div class="form-section">
                    <h2 class="section-title">Personal Information</h2>
                    <p class="section-subtitle">Update your personal details and profile picture</p>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="first_name" class="form-label">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3Zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                                </svg>
                                First Name
                            </label>
                            <input type="text" id="first_name" name="first_name" class="form-control @error('first_name') error @enderror" value="{{ old('first_name', $user->first_name) }}" required>
                            @error('first_name')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="middle_name" class="form-label">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3Zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                                </svg>
                                Middle Name
                                <span class="optional">Optional</span>
                            </label>
                            <input type="text" id="middle_name" name="middle_name" class="form-control @error('middle_name') error @enderror" value="{{ old('middle_name', $user->middle_name) }}">
                            @error('middle_name')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="last_name" class="form-label">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3Zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                                </svg>
                                Last Name
                            </label>
                            <input type="text" id="last_name" name="last_name" class="form-control @error('last_name') error @enderror" value="{{ old('last_name', $user->last_name) }}" required>
                            @error('last_name')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z"/>
                                </svg>
                                Email Address
                            </label>
                            <input type="email" id="email" class="form-control" value="{{ $user->email }}" disabled>
                            <p class="form-hint">Email cannot be changed</p>
                        </div>

                        <div class="form-group">
                            <label for="id_number" class="form-label">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5v-3zM2.5 2a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zm6.5.5A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zM1 10.5A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zm6.5.5A1.5 1.5 0 0 1 10.5 9h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 13.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3z"/>
                                </svg>
                                ID Number
                            </label>
                            <input type="text" id="id_number" class="form-control" value="{{ $user->id_number }}" disabled>
                            <p class="form-hint">ID Number is assigned by the system</p>
                        </div>

                        <div class="form-group">
                            <label for="department" class="form-label">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M6.5 1A1.5 1.5 0 0 0 5 2.5V3H1.5A1.5 1.5 0 0 0 0 4.5v1.384l7.614 2.03a1.5 1.5 0 0 0 .772 0L16 5.884V4.5A1.5 1.5 0 0 0 14.5 3H11v-.5A1.5 1.5 0 0 0 9.5 1h-3zm0 1h3a.5.5 0 0 1 .5.5V3H6v-.5a.5.5 0 0 1 .5-.5z"/>
                                    <path d="M0 12.5A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5V6.85L8.129 8.947a.5.5 0 0 1-.258 0L0 6.85v5.65z"/>
                                </svg>
                                Department
                            </label>
                            <input type="text" id="department" class="form-control" value="{{ $user->department_name }}" disabled>
                        </div>

                        @if($user->year_level)
                        <div class="form-group">
                            <label for="year_level" class="form-label">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M8.211 2.047a.5.5 0 0 0-.422 0l-7.5 3.5a.5.5 0 0 0 .025.917l7.5 3a.5.5 0 0 0 .372 0L14 7.14V13a1 1 0 0 0-1 1v2h3v-2a1 1 0 0 0-1-1V6.739l.686-.275a.5.5 0 0 0 .025-.917l-7.5-3.5Z"/>
                                    <path d="M4.176 9.032a.5.5 0 0 0-.656.327l-.5 1.7a.5.5 0 0 0 .294.605l4.5 1.8a.5.5 0 0 0 .372 0l4.5-1.8a.5.5 0 0 0 .294-.605l-.5-1.7a.5.5 0 0 0-.656-.327L8 10.466 4.176 9.032Z"/>
                                </svg>
                                Year Level
                            </label>
                            <input type="text" id="year_level" class="form-control" value="{{ $user->year_level_name }}" disabled>
                        </div>
                        @endif
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <span class="btn-text">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                                </svg>
                                Save Changes
                            </span>
                            <span class="btn-loader" style="display: none;">
                                <svg class="spinner" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0z" opacity=".3"/>
                                    <path d="M8 0a8 8 0 0 0 0 16v-2a6 6 0 0 1 0-12V0z"/>
                                </svg>
                                Saving...
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Password Tab --}}
        <div class="tab-content" id="password-tab">
            <form action="{{ route('password.update') }}" method="POST" id="password-form" class="modern-form">
                @csrf
                @method('PUT')

                <div class="form-section">
                    <h2 class="section-title">Change Password</h2>
                    <p class="section-subtitle">Ensure your account is using a long, random password to stay secure</p>

                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label for="current_password" class="form-label">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                                </svg>
                                Current Password
                            </label>
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
                            <label for="password" class="form-label">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                                </svg>
                                New Password
                            </label>
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
                            <p class="form-hint">Minimum 8 characters</p>
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation" class="form-label">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                                </svg>
                                Confirm New Password
                            </label>
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
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="document.getElementById('password-form').reset()">
                            Reset
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <span class="btn-text">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                                </svg>
                                Update Password
                            </span>
                            <span class="btn-loader" style="display: none;">
                                <svg class="spinner" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0z" opacity=".3"/>
                                    <path d="M8 0a8 8 0 0 0 0 16v-2a6 6 0 0 1 0-12V0z"/>
                                </svg>
                                Updating...
                            </span>
                        </button>
                    </div>
                </div>
            </form>
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
        background: linear-gradient(135deg, #7c3aed 0%, #6366f1 100%) !important;
        margin: 0;
    }

    .profile-page {
        min-height: 100vh;
        background: transparent !important;
        padding: 6rem 1rem 2rem;
    }

    .container {
        max-width: 800px;
        margin: 0 auto;
        zoom: 0.9;
    }

    /* Toast Notifications */
    .toast {
        position: fixed;
        top: 2rem;
        right: 2rem;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.875rem;
        font-weight: 500;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
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

    /* Profile Header */
    .profile-header {
        background: rgba(255, 255, 255, 0.98);
        border-radius: 20px;
        padding: 2.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        backdrop-filter: blur(10px);
        position: relative;
    }

    .profile-header-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 1.5rem;
        margin-bottom: 2rem;
        padding-bottom: 2rem;
        border-bottom: 2px solid #f3f4f6;
    }

    .profile-avatar-large {
        position: relative;
        width: 140px;
        height: 140px;
        flex-shrink: 0;
    }

    .profile-avatar-large img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid rgba(118, 75, 162, 0.2);
        box-shadow: 0 8px 24px rgba(118, 75, 162, 0.3);
    }

    .avatar-overlay {
        position: absolute;
        bottom: 0;
        right: 0;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .profile-avatar-large:hover .avatar-overlay {
        opacity: 1;
    }

    .avatar-edit-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        border-radius: 50%;
        cursor: pointer;
        box-shadow: 0 4px 16px rgba(118, 75, 162, 0.5);
        transition: all 0.3s;
    }

    .avatar-edit-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(118, 75, 162, 0.6);
    }

    .avatar-edit-btn svg {
        color: white;
    }

    .profile-info {
        flex: 1;
    }

    .profile-name {
        font-size: 2rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 0.5rem;
    }

    .profile-handle {
        font-size: 1.125rem;
        color: #6b7280;
        margin-bottom: 1rem;
    }

    .profile-badges {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        justify-content: center;
    }

    .badge {
        padding: 0.5rem 1rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-role {
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        color: white;
        box-shadow: 0 2px 8px rgba(118, 75, 162, 0.3);
    }

    .badge-status {
        border: 2px solid;
    }

    .badge-active {
        background: #d1fae5;
        color: #065f46;
        border-color: #6ee7b7;
    }

    .badge-inactive {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fecaca;
    }

    /* Info Cards */
    .info-cards {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .info-card {
        background: transparent;
        padding: 0;
        border-radius: 0;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.3s;
        border: none;
    }

    .info-card:hover {
        transform: translateY(-2px);
    }

    .info-card-icon {
        width: 48px;
        height: 48px;
        background: #ef4444;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    .info-card-icon.icon-red {
        background: #ef4444;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    .info-card-content {
        flex: 1;
        min-width: 0;
    }

    .info-card-label {
        display: block;
        font-size: 0.75rem;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }

    .info-card-value {
        display: block;
        font-size: 0.9375rem;
        font-weight: 600;
        color: #111827;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Tabs */
    .tabs {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 2rem;
        background: rgba(255, 255, 255, 0.98);
        padding: 0.5rem;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    .tab {
        flex: 1;
        padding: 1rem 1.5rem;
        border: none;
        background: transparent;
        color: #6b7280;
        font-size: 0.9375rem;
        font-weight: 600;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .tab:hover {
        background: #f9fafb;
        color: #374151;
    }

    .tab.active {
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(118, 75, 162, 0.4);
    }

    .tab svg {
        flex-shrink: 0;
    }

    /* Tab Content */
    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    /* Modern Form */
    .modern-form {
        background: rgba(255, 255, 255, 0.98);
        border-radius: 20px;
        padding: 2.5rem;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        backdrop-filter: blur(10px);
    }

    .form-section {
        margin-bottom: 2rem;
    }

    .form-section:last-child {
        margin-bottom: 0;
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 0.5rem;
    }

    .section-subtitle {
        font-size: 0.9375rem;
        color: #6b7280;
        margin-bottom: 2rem;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .form-label svg {
        color: #764ba2;
        flex-shrink: 0;
    }

    .optional {
        font-weight: 400;
        color: #9ca3af;
        font-size: 0.8125rem;
    }

    .form-control {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 0.9375rem;
        color: #111827;
        transition: all 0.3s;
        font-family: inherit;
        background: white;
    }

    .form-control:focus {
        outline: none;
        border-color: #764ba2;
        box-shadow: 0 0 0 4px rgba(118, 75, 162, 0.1);
    }

    .form-control.error {
        border-color: #ef4444;
    }

    .form-control:disabled {
        background: #f9fafb;
        color: #9ca3af;
        cursor: not-allowed;
    }

    .form-hint {
        font-size: 0.8125rem;
        color: #6b7280;
        margin-top: 0.375rem;
    }

    .error-message {
        font-size: 0.8125rem;
        color: #ef4444;
        margin-top: 0.375rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
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
        transition: color 0.3s;
    }

    .toggle-password:hover {
        color: #764ba2;
    }

    /* Buttons */
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        padding-top: 2rem;
        border-top: 2px solid #f3f4f6;
    }

    .btn {
        padding: 0.875rem 2rem;
        border-radius: 10px;
        font-size: 0.9375rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        font-family: inherit;
    }

    .btn-primary {
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        color: white;
        box-shadow: 0 4px 16px rgba(118, 75, 162, 0.4);
    }

    .btn-primary:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(118, 75, 162, 0.5);
    }

    .btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .btn-secondary {
        background: white;
        color: #374151;
        border: 2px solid #e5e7eb;
    }

    .btn-secondary:hover {
        background: #f9fafb;
        border-color: #d1d5db;
    }

    .spinner {
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    /* Desktop - Larger screens */
    @media (min-width: 769px) {
        .info-cards {
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
        }
        
        .form-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .container {
            zoom: 1;
        }

        .profile-page {
            padding: 2rem 0.5rem;
        }

        .profile-header {
            padding: 1.5rem;
        }

        .profile-avatar-large {
            width: 120px;
            height: 120px;
        }

        .profile-name {
            font-size: 1.5rem;
        }

        .tabs {
            flex-direction: column;
        }

        .modern-form {
            padding: 1.5rem;
        }

        .form-actions {
            flex-direction: column-reverse;
        }

        .btn {
            width: 100%;
        }

        .toast {
            right: 1rem;
            left: 1rem;
        }

        .info-cards {
            gap: 1rem;
        }
    }

    @media (max-width: 480px) {
        .section-title {
            font-size: 1.25rem;
        }

        .profile-name {
            font-size: 1.25rem;
        }
    }
</style>

<script>
    // Tab switching
    document.querySelectorAll('.tab').forEach(tab => {
        tab.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            
            // Remove active class from all tabs and contents
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            
            // Add active class to clicked tab and corresponding content
            this.classList.add('active');
            document.getElementById(tabName + '-tab').classList.add('active');
        });
    });

    // Auto-switch to password tab if there are password errors
    @if($errors->updatePassword->any())
    document.addEventListener('DOMContentLoaded', function() {
        // Remove active from profile tab
        document.querySelector('.tab[data-tab="profile"]').classList.remove('active');
        document.getElementById('profile-tab').classList.remove('active');
        
        // Add active to password tab
        document.querySelector('.tab[data-tab="password"]').classList.add('active');
        document.getElementById('password-tab').classList.add('active');
    });
    @endif

    // Image preview
    function previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            // Check file size (2MB limit)
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
                document.getElementById('remove_profile_picture').value = '0';
            }
            reader.readAsDataURL(file);
        }
    }

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

    // Form submission with loading state
    document.getElementById('profile-form').addEventListener('submit', function(e) {
        const btn = this.querySelector('.btn-primary');
        btn.disabled = true;
        btn.querySelector('.btn-text').style.display = 'none';
        btn.querySelector('.btn-loader').style.display = 'inline-flex';
    });

    document.getElementById('password-form').addEventListener('submit', function(e) {
        const btn = this.querySelector('.btn-primary');
        btn.disabled = true;
        btn.querySelector('.btn-text').style.display = 'none';
        btn.querySelector('.btn-loader').style.display = 'inline-flex';
    });

    // Auto-hide toast messages
    setTimeout(() => {
        const toasts = document.querySelectorAll('.toast');
        toasts.forEach(toast => {
            toast.style.animation = 'fadeOut 0.3s ease forwards';
            setTimeout(() => toast.remove(), 300);
        });
    }, 5000);
</script>
</x-app-layout>