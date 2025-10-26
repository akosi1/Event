<x-guest-layout>
    <!-- ✅ Load CSS in HEAD before body renders -->
    <link rel="stylesheet" href="{{ asset('public/user/forgotpass/forgotpass.css') }}">
    
    <style>
        body {
            background: url("{{ asset('public/images/mcc background.jpg') }}") center/cover no-repeat;
            position: relative;
        }
    </style>

    <div class="forgot-wrapper">
        <div class="forgot-container">
            <div class="forgot-header">
                <h1>Reset your password</h1>
                <p>Enter your Ms365 College email and we'll send you a password reset link.</p>
            </div>

            <div class="forgot-form">
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="form-group">
                        <div class="input-wrapper">
                            <input 
                                id="email" 
                                type="email" 
                                name="email" 
                                value="{{ old('email') }}" 
                                placeholder=" "
                                required 
                                autofocus
                                autocomplete="username"
                                class="form-control @error('email') input-error @enderror"
                                oninput="this.value = this.value.replace(/\s+/g, '')"
                                onblur="this.value = this.value.trim()"
                            >
                            <label class="input-label">Ms365 Email</label>
                            <i class="fas fa-envelope input-icon"></i>
                        </div>
                        @error('email')
                            <div class="error-msg">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-key"></i>
                            Reset password
                        </button>
                    </div>

                    <div class="form-footer">
                        <div class="divider">
                            <span>or</span>
                        </div>
                        
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

    <!-- ✅ Show content after CSS loads -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add 'loaded' class to trigger fade-in
            const wrapper = document.querySelector('.forgot-wrapper');
            if (wrapper) {
                // Small delay to ensure CSS is fully applied
                setTimeout(() => {
                    wrapper.classList.add('loaded');
                }, 10);
            }
        });
    </script>
</x-guest-layout>   
