<x-guest-layout>
  #   <link rel="stylesheet" href="{{ asset('user/ms365/ms365.css') }}">
       <link rel="stylesheet" href="{{ asset('user/ms365/ms365.css') }}" />
    
    <style>
        body {
        #    background: url("{{ asset('images/mcc background.jpg') }}") center/cover no-repeat;
             background: url("{{ asset('images/mcc background.jpg') }}") center/cover no-repeat;
            position: relative;
        }
    </style>

    <div class="auth-wrapper">
        <div class="auth-container">
            <div class="auth-header">
                <h1>Sign Up</h1>
                <p>Create your E&amp;P-O account</p>
            </div>
            
            <div class="auth-form">
                <x-auth-session-status class="mb-4" :status="session('status')" />

                @if($errors->any())
                    <div class="error-list">
                        <strong>Validation Errors:</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('ms365.verify.store') }}" id="ms365Form">
                    @csrf
                    
                    <div class="form-group">
                        <div class="input-wrapper">
                            <input 
                                id="email" 
                                type="email" 
                                name="email" 
                                value="{{ old('email', '') }}" 
                                class="form-control @error('email') input-error @enderror" 
                                placeholder=" " 
                                required 
                                autocomplete="username" 
                                autofocus
                                oninput="this.value = this.value.replace(/\s+/g, '')"
                                onblur="this.value = this.value.trim()"
                            >
                            <label class="input-label">MS365 Email</label>
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
                        Send Verification Code
                    </button>
                    
                    <div class="form-footer">
                        <div class="divider">
                            <span>or</span>
                        </div>
                        
                        <div class="signup-link">
                            <p>Already have an account?</p>
                            <a href="{{ route('login') }}" class="btn-secondary">
                                Sign In Here
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const wrapper = document.querySelector('.auth-wrapper');
            if (wrapper) {
                setTimeout(() => wrapper.classList.add('loaded'), 10);
            }

            const form = document.getElementById('ms365Form');
            const emailInput = document.getElementById('email');

            form.addEventListener('submit', function(e) {
                const email = emailInput.value;
                if (/\s/.test(email)) {
                    e.preventDefault();
                    alert("Email must not contain any spaces.");
                    emailInput.focus();
                    return false;
                }
            });
        });
    </script>
</x-guest-layout>
