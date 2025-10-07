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

            // Optional: Real-time validation feedback (not required, but user-friendly)
            confirmPasswordInput.addEventListener('input', function () {
                if (passwordInput.value !== confirmPasswordInput.value) {
                    confirmPasswordInput.setCustomValidity("Passwords do not match");
                } else {
                    confirmPasswordInput.setCustomValidity("");
                }
            });

            // Prevent form submission if spaces were somehow injected (extra safety)
            form.addEventListener('submit', function (e) {
                const pass = passwordInput.value;
                const confirmPass = confirmPasswordInput.value;

                if (/\s/.test(pass) || /\s/.test(confirmPass)) {
                    e.preventDefault();
                    alert("Password must not contain spaces.");
                    return false;
                }
            });
        });
    </script>
</x-guest-layout>