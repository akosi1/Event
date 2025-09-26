<x-guest-layout>
    <div class="auth-container register-mode">
        <!-- Floating Particles -->
        <div class="floating-particles" id="particles"></div>
        
        <!-- Diagonal Section -->
        <div class="diagonal-section register-mode"></div>
        
        <!-- Welcome Section -->
        <div class="welcome-section register-mode">
            <h1 id="welcomeTitle">Join Us</h1>
            <p id="welcomeText">Create your EventAps account</p>
        </div>
        
        <!-- Form Section -->
        <div class="form-section register-mode">
            <div class="form-content">
                <h2 class="form-title">Create Account</h2>
                
                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />
                
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    
                    <!-- Name Fields Row -->
                    <div class="form-row">
                        <div class="form-group">
                            <div class="input-wrapper">
                                <input 
                                    id="first_name" 
                                    class="form-control" 
                                    type="text" 
                                    name="first_name" 
                                    value="{{ old('first_name') }}" 
                                    placeholder="First Name" 
                                    required 
                                    autofocus 
                                    autocomplete="given-name"
                                >
                                <i class="fas fa-user input-icon"></i>
                            </div>
                            <x-input-error :messages="$errors->get('first_name')" class="error-msg" />
                        </div>
                        
                        <div class="form-group">
                            <div class="input-wrapper">
                                <input 
                                    id="last_name" 
                                    class="form-control" 
                                    type="text" 
                                    name="last_name" 
                                    value="{{ old('last_name') }}" 
                                    placeholder="Last Name" 
                                    required 
                                    autocomplete="family-name"
                                >
                                <i class="fas fa-user input-icon"></i>
                            </div>
                            <x-input-error :messages="$errors->get('last_name')" class="error-msg" />
                        </div>
                    </div>
                    
                    <!-- Middle Name -->
                    <div class="form-group">
                        <div class="input-wrapper">
                            <input 
                                id="middle_name" 
                                class="form-control" 
                                type="text" 
                                name="middle_name" 
                                value="{{ old('middle_name') }}" 
                                placeholder="Middle Name (Optional)" 
                                autocomplete="additional-name"
                            >
                            <i class="fas fa-user-circle input-icon"></i>
                        </div>
                        <x-input-error :messages="$errors->get('middle_name')" class="error-msg" />
                    </div>
                    
                    <!-- Email -->
                    <div class="form-group">
                        <div class="input-wrapper">
                            <input 
                                id="email" 
                                class="form-control" 
                                type="email" 
                                name="email" 
                                value="{{ old('email') }}" 
                                placeholder="Email Address" 
                                required 
                                autocomplete="username"
                            >
                            <i class="fas fa-envelope input-icon"></i>
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="error-msg" />
                    </div>
                    
                    <!-- Department -->
                    <div class="form-group">
                        <div class="input-wrapper">
                            <select id="department" name="department" class="form-select" required>
                                <option value="">Select Department</option>
                                <option value="BSIT" {{ old('department') == 'BSIT' ? 'selected' : '' }}>Bachelor of Science in Information Technology</option>
                                <option value="BSBA" {{ old('department') == 'BSBA' ? 'selected' : '' }}>Bachelor of Science in Business Administration</option>
                                <option value="BSED" {{ old('department') == 'BSED' ? 'selected' : '' }}>Bachelor of Science in Education</option>
                                <option value="BEED" {{ old('department') == 'BEED' ? 'selected' : '' }}>Bachelor of Elementary Education</option>
                                <option value="BSHM" {{ old('department') == 'BSHM' ? 'selected' : '' }}>Bachelor of Science in Hospitality Management</option>
                            </select>
                            <i class="fas fa-graduation-cap input-icon"></i>
                        </div>
                        <x-input-error :messages="$errors->get('department')" class="error-msg" />
                    </div>
                    
                    <!-- Password Fields Row -->
                    <div class="form-row">
                        <div class="form-group">
                            <div class="input-wrapper">
                                <input 
                                    id="password" 
                                    class="form-control" 
                                    type="password" 
                                    name="password" 
                                    placeholder="Password" 
                                    required 
                                    autocomplete="new-password"
                                >
                                <i class="fas fa-lock input-icon"></i>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="error-msg" />
                        </div>
                        
                        <div class="form-group">
                            <div class="input-wrapper">
                                <input 
                                    id="password_confirmation" 
                                    class="form-control" 
                                    type="password" 
                                    name="password_confirmation" 
                                    placeholder="Confirm Password" 
                                    required 
                                    autocomplete="new-password"
                                >
                                <i class="fas fa-check-circle input-icon"></i>
                            </div>
                            <x-input-error :messages="$errors->get('password_confirmation')" class="error-msg" />
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-user-plus"></i>
                        {{ __('Sign Up') }}
                    </button>
                    
                    <!-- Auth Links -->
                    <div class="auth-links">
                        <p>{{ __('Already registered?') }} 
                           <a href="{{ route('login') }}">{{ __('Sign in here') }}</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Include CSS and JS -->
    <link rel="stylesheet" href="{{ asset('user/login_register/login_register.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="{{ asset('user/login_register/login_register.js') }}"></script>
</x-guest-layout>