<x-guest-layout>
    <div class="forgot-wrapper">
        <div class="forgot-container">
            <div class="forgot-header">
                <h1>Set New Password</h1>
                <p>Create a strong password for your McLawis College account.</p>
            </div>

            <div class="forgot-form">
                <form method="POST" action="{{ route('password.store') }}" id="resetPasswordForm">
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <!-- Email Address (Read-only) -->
                    <div class="form-group">
                        <div class="input-wrapper">
                            <input 
                                id="email" 
                                type="email" 
                                name="email" 
                                value="{{ old('email', $request->email) }}"
                                placeholder=" "
                                required 
                                autofocus
                                autocomplete="username"
                                readonly
                                class="form-control"
                                style="background-color: rgba(245, 245, 245, 0.95); cursor: not-allowed;"
                            >
                            <label class="input-label" for="email">{{ __('Email') }}</label>
                            <i class="fas fa-envelope input-icon"></i>
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <div class="input-wrapper">
                            <input 
                                id="password" 
                                type="password" 
                                name="password" 
                                placeholder=" "
                                required
                                autocomplete="new-password"
                                class="form-control"
                                oninput="this.value = this.value.replace(/\s+/g, '')"
                                onblur="this.value = this.value.trim()"
                            >
                            <label class="input-label" for="password">{{ __('Password') }}</label>
                            <i class="fas fa-lock input-icon"></i>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group">
                        <div class="input-wrapper">
                            <input 
                                id="password_confirmation" 
                                type="password" 
                                name="password_confirmation" 
                                placeholder=" "
                                required
                                autocomplete="new-password"
                                class="form-control"
                                oninput="this.value = this.value.replace(/\s+/g, '')"
                                onblur="this.value = this.value.trim()"
                            >
                            <label class="input-label" for="password_confirmation">{{ __('Confirm Password') }}</label>
                            <i class="fas fa-lock input-icon"></i>
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-check-circle"></i>
                            {{ __('Confirm Password') }}
                        </button>
                    </div>

                    <div class="form-footer">
                        <div class="back-link">
                            <a href="{{ route('login') }}" class="btn-secondary">
                                Back to sign in
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="{{ asset('user/resetpass/resetpass.css') }}">
    <style>
        body {
            background: url("{{ asset('images/mcc background.jpg') }}") center/cover no-repeat;
            position: relative;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('resetPasswordForm');
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('password_confirmation');

            // Real-time password match validation
            confirmPasswordInput.addEventListener('input', function () {
                if (passwordInput.value !== confirmPasswordInput.value) {
                    confirmPasswordInput.setCustomValidity("Passwords do not match");
                } else {
                    confirmPasswordInput.setCustomValidity("");
                }
            });

            // Final space check on submit (defense-in-depth)
            form.addEventListener('submit', function (e) {
                const pass = passwordInput.value;
                const confirmPass = confirmPasswordInput.value;

                // Block if any space exists (including non-breaking spaces)
                if (/\s/.test(pass) || /\s/.test(confirmPass)) {
                    e.preventDefault();
                    alert("Password must not contain any spaces.");
                    // Focus first invalid field
                    if (/\s/.test(pass)) {
                        passwordInput.focus();
                    } else {
                        confirmPasswordInput.focus();
                    }
                    return false;
                }

                // Optional: Enforce min length or strength (if not done server-side)
                if (pass.length < 8) {
                    e.preventDefault();
                    alert("Password must be at least 8 characters long.");
                    passwordInput.focus();
                    return false;
                }
            });
        });
    </script>
</x-guest-layout>