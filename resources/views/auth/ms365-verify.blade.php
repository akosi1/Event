    <x-guest-layout>
        <div class="auth-wrapper">
            <div class="auth-container">
                <div class="auth-header">
                    <h1>Sign Up</h1>
                    <p>Create your E&P-O account</p>
                </div>
                <div class="auth-form" id="authForm">
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    @if($errors->any())
                        <div class="debug-info">
                            <strong>Validation Errors:</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('ms365.verify.store') }}">
                        @csrf
                        <div class="form-group">
                            <div class="input-wrapper">
                                <input id="email" type="email" name="email" value="{{ old('email', '') }}" 
                                    class="form-control @error('email') input-error @enderror" placeholder=" " required autocomplete="username" autofocus>
                                <label class="input-label">Ms365 Email </label>
                                <i class="fas fa-envelope input-icon"></i>
                            </div>
                            @error('email')
                                <div class="error-msg">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-paper-plane"></i>
                            Send verification code
                        </button>
                        
                        <div class="form-footer">
                            <div class="divider">
                                <span>or</span>
                            </div>
                            
                            <div class="signup-link">
                                <p>Already have an account?</p>
                                <a href="{{ route('login') }}" class="btn-secondary">
                                    Sign in here
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <link rel="stylesheet" href="{{ asset('user/ms365/ms365.css') }}">
        <style>
           body {
                background: url("{{ asset('images/mcc background.jpg') }}") center/cover no-repeat;
                position: relative;
            }
        </style>
</x-guest-layout>