<x-guest-layout>
    <div class="auth-wrapper">
        <div class="auth-container">
            <div class="auth-header">
                <h1>Welcome Back</h1>
                <p>Sign in to your McClawis Events System account</p>
            </div>

            <div class="auth-form">
                <x-auth-session-status class="mb-4" :status="session('status')" />

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group">
                        <i class="fas fa-envelope"></i>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" 
                               placeholder="Email Address (@mcclawis.edu.ph)" required autocomplete="username" autofocus>
                        <x-input-error :messages="$errors->get('email')" class="error-msg" />
                    </div>

                    <div class="form-group">
                        <i class="fas fa-graduation-cap"></i>
                        <select id="department" name="department" required>
                            <option value="">Select Your Department</option>
                            <option value="BSIT" {{ old('department') == 'BSIT' ? 'selected' : '' }}>Bachelor of Science in Information Technology</option>
                            <option value="BSBA" {{ old('department') == 'BSBA' ? 'selected' : '' }}>Bachelor of Science in Business Administration</option>
                            <option value="BSED" {{ old('department') == 'BSED' ? 'selected' : '' }}>Bachelor of Science in Education</option>
                            <option value="BEED" {{ old('department') == 'BEED' ? 'selected' : '' }}>Bachelor of Elementary Education</option>
                            <option value="BSHM" {{ old('department') == 'BSHM' ? 'selected' : '' }}>Bachelor of Science in Hospitality Management</option>
                        </select>
                        <x-input-error :messages="$errors->get('department')" class="error-msg" />
                    </div>

                    <div class="form-group">
                        <i class="fas fa-lock"></i>
                        <input id="password" type="password" name="password" 
                               placeholder="Password" required autocomplete="current-password">
                        <x-input-error :messages="$errors->get('password')" class="error-msg" />
                    </div>

                    <div class="form-group remember-group">
                        <label for="remember" class="remember-label">
                            <input id="remember" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <span class="checkmark"></span>
                            {{ __('Remember me') }}
                        </label>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-sign-in-alt"></i>
                        {{ __('Sign In') }}
                    </button>

                    <div class="auth-links">
                        @if (Route::has('password.request'))
                            <p><a href="{{ route('password.request') }}">{{ __('Forgot your password?') }}</a></p>
                        @endif
                        
                        <p>{{ __("Don't have an account?") }} 
                           <a href="{{ route('register') }}">{{ __('Sign up here') }}</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div><link rel="stylesheet" href="{{ asset('user/auth/auth.css') }}">
</x-guest-layout>