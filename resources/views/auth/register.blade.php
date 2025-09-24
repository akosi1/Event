<x-guest-layout>
    <link rel="stylesheet" href="{{ asset('user/login_register/login_register.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <div class="auth-container register-mode">
        <div class="floating-particles" id="particles"></div>
        <div class="diagonal-section register-mode"></div>
        
        <div class="welcome-section register-mode">
            <h1 id="welcomeTitle">WELCOME!</h1>
            <p id="welcomeText">Create your account</p>
        </div>
        
        <div class="form-section register-mode">
            <div class="form-content">
                <div class="form-title">Sign Up</div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="form-row">
                        <div class="form-group">
                            <div class="input-wrapper">
                                <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" 
                                       class="form-control" placeholder=" " required autofocus autocomplete="given-name">
                                <label class="input-label">First Name</label>
                                <i class="fas fa-user input-icon"></i>
                            </div>
                            <x-input-error :messages="$errors->get('first_name')" class="error-msg" />
                        </div>

                        <div class="form-group">
                            <div class="input-wrapper">
                                <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" 
                                       class="form-control" placeholder=" " required autocomplete="family-name">
                                <label class="input-label">Last Name</label>
                                <i class="fas fa-user input-icon"></i>
                            </div>
                            <x-input-error :messages="$errors->get('last_name')" class="error-msg" />
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper">
                            <input id="middle_name" type="text" name="middle_name" value="{{ old('middle_name') }}" 
                                   class="form-control" placeholder=" " autocomplete="additional-name">
                            <label class="input-label">Middle Name (Optional)</label>
                            <i class="fas fa-user-circle input-icon"></i>
                        </div>
                        <x-input-error :messages="$errors->get('middle_name')" class="error-msg" />
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper">
                            <input id="email" type="email" name="email" value="{{ old('email') }}" 
                                   class="form-control" placeholder=" " required autocomplete="username">
                            <label class="input-label">Email Address</label>
                            <i class="fas fa-envelope input-icon"></i>
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="error-msg" />
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper">
                            <select id="department" name="department" class="form-select" required>
                                <option value=""></option>
                                <option value="BSIT" {{ old('department') == 'BSIT' ? 'selected' : '' }}>Bachelor of Science in Information Technology</option>
                                <option value="BSBA" {{ old('department') == 'BSBA' ? 'selected' : '' }}>Bachelor of Science in Business Administration</option>
                                <option value="BSED" {{ old('department') == 'BSED' ? 'selected' : '' }}>Bachelor of Science in Education</option>
                                <option value="BEED" {{ old('department') == 'BEED' ? 'selected' : '' }}>Bachelor of Elementary Education</option>
                                <option value="BSHM" {{ old('department') == 'BSHM' ? 'selected' : '' }}>Bachelor of Science in Hospitality Management</option>
                            </select>
                            <label class="select-label">Select Department</label>
                            <i class="fas fa-graduation-cap input-icon"></i>
                        </div>
                        <x-input-error :messages="$errors->get('department')" class="error-msg" />
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <div class="input-wrapper">
                                <input id="password" type="password" name="password" 
                                       class="form-control" placeholder=" " required autocomplete="new-password">
                                <label class="input-label">Password</label>
                                <i class="fas fa-lock input-icon"></i>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="error-msg" />
                        </div>

                        <div class="form-group">
                            <div class="input-wrapper">
                                <input id="password_confirmation" type="password" name="password_confirmation" 
                                       class="form-control" placeholder=" " required autocomplete="new-password">
                                <label class="input-label">Confirm Password</label>
                                <i class="fas fa-check-circle input-icon"></i>
                            </div>
                            <x-input-error :messages="$errors->get('password_confirmation')" class="error-msg" />
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-user-plus"></i>
                        {{ __('Sign Up') }}
                    </button>

                    <div class="auth-links">
                        <p>{{ __('Already registered?') }} 
                           <a href="{{ route('login') }}">{{ __('Sign in here') }}</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="{{ asset('user/login_register/login_register.js') }}"></script>
</x-guest-layout>