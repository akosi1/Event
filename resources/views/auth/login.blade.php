<x-guest-layout>
    <link rel="stylesheet" href="{{ asset('user/login_register/login_register.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <div class="auth-container">
        <div class="floating-particles" id="particles"></div>
        <div class="diagonal-section"></div>
        
        <div class="welcome-section">
            <h1 id="welcomeTitle">WELCOME BACK!</h1>
            <p id="welcomeText">Please sign in to continue</p>
        </div>
        
        <div class="form-section">
            <div class="form-content">
                <div class="form-title">Sign In</div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group">
                        <div class="input-wrapper">
                            <input id="email" type="email" name="email" value="{{ old('email') }}" 
                                   class="form-control" placeholder=" " required autocomplete="username" autofocus>
                            <label class="input-label">Email Address</label>
                            <i class="fas fa-envelope input-icon"></i>
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="error-msg" />
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper">
                            <input id="password" type="password" name="password" 
                                   class="form-control" placeholder=" " required autocomplete="current-password">
                            <label class="input-label">Password</label>
                            <i class="fas fa-lock input-icon"></i>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="error-msg" />
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper">
                            <select id="department" name="department" class="form-select" required>
                                <option value=""></option>
                                <option value="BSIT" {{ old('department') == 'BSIT' ? 'selected' : '' }}>BSIT</option>
                                <option value="BSBA" {{ old('department') == 'BSBA' ? 'selected' : '' }}>BSBA</option>
                                <option value="BSED" {{ old('department') == 'BSED' ? 'selected' : '' }}>BSED</option>
                                <option value="BEED" {{ old('department') == 'BEED' ? 'selected' : '' }}>BEED</option>
                                <option value="BSHM" {{ old('department') == 'BSHM' ? 'selected' : '' }}>BSHM</option>
                            </select>
                            <label class="select-label">Select Your Department</label>
                            <i class="fas fa-graduation-cap input-icon"></i>
                        </div>
                        <x-input-error :messages="$errors->get('department')" class="error-msg" />
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-sign-in-alt"></i>
                        {{ __('Sign In') }}
                    </button>

                    <div class="auth-links">
                        <p>{{ __("Don't have an account?") }} 
                           <a href="#" onclick="authAnimator.switchToRegister(); return false;">{{ __('Sign up here') }}</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('user/login_register/login_register.js') }}"></script>
</x-guest-layout>