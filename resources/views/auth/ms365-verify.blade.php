<x-guest-layout>
    <div class="ms365-wrapper">
        <div class="ms365-container">
            <div class="ms365-header">
                <div class="ms-logo">
                    <img src="images/logo.png" alt="Logo" width="32" height="32">
                </div>
                <h1>Sign up for E&P-O</h1>
                <p>Use your ms365 email account</p>
            </div>

            <div class="ms365-form">
                <x-auth-session-status class="mb-4" :status="session('status')" />

                @if($errors->any())
                    <div class="debug-info">
                        <h3>Validation Errors:</h3>
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
                        <label for="email" class="sr-only">Email Address</label>
                        <input 
                            id="email" 
                            type="email" 
                            name="email" 
                            value="{{ old('email', '') }}" 
                            placeholder="someone@mcclawis.edu.ph"
                            required 
                            autocomplete="username"
                            autofocus
                            class="ms-input @error('email') error @enderror"
                        >
                        @error('email')
                            <div class="error-msg">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">
                            Send verification code
                        </button>
                    </div>

                    <div class="form-footer">
                        <div class="auth-links">
                            <p>Already have an account? 
                               <a href="{{ route('login') }}">Sign in here</a>
                            </p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>

<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        background: #f5f5f5;
        color: #323130;
    }

    .ms365-wrapper {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        overflow-y: auto;
    }

    .ms365-container {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 440px;
        animation: fadeIn 0.4s ease;
        margin: auto;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .ms365-header {
        text-align: center;
        padding: 40px 40px 20px;
    }

    .ms-logo {
        margin-bottom: 20px;
        display: flex;
        justify-content: center;
    }

    .ms365-header h1 {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 8px;
        line-height: 1.3;
    }

    .ms365-header p {
        font-size: 15px;
        color: #605e5c;
    }

    .ms365-form { padding: 0 40px 40px; }
    .form-group { margin-bottom: 24px; }

    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
    }

    .ms-input {
        width: 100%;
        padding: 11px 12px;
        font-size: 15px;
        border: 1px solid #605e5c;
        border-radius: 2px;
        transition: all 0.2s ease;
        outline: none;
    }

    .ms-input:focus {
        border-color: #0078d4;
        box-shadow: 0 0 0 1px #0078d4;
    }

    .ms-input.error {
        border-color: #d13438;
        box-shadow: 0 0 0 1px #d13438;
    }

    .error-msg {
        margin-top: 8px;
        padding: 8px 12px;
        background: #fef0f0;
        border: 1px solid #d13438;
        border-radius: 2px;
        color: #d13438;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary {
        width: 100%;
        padding: 12px;
        background: #0078d4;
        color: white;
        border: none;
        border-radius: 2px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s ease;
    }

    .btn-primary:hover { background: #106ebe; }
    .btn-primary:active { background: #005a9e; }

    .form-footer { margin-top: 32px; }

    .auth-links {
        text-align: center;
        padding-top: 16px;
        border-top: 1px solid #edebe9;
    }

    .auth-links p {
        font-size: 15px;
        color: #605e5c;
    }

    .auth-links a {
        color: #0078d4;
        text-decoration: none;
        font-weight: 600;
    }

    .auth-links a:hover { text-decoration: underline; }

    .mb-4 { margin-bottom: 24px; }

    .mb-4 div {
        padding: 16px;
        background: #dff6dd;
        border: 1px solid #107c10;
        border-radius: 2px;
        color: #107c10;
        font-size: 14px;
    }

    .debug-info {
        margin-bottom: 24px;
        padding: 16px;
        background: #fff4ce;
        border: 1px solid #fde300;
        border-radius: 2px;
        font-size: 14px;
    }

    .debug-info h3 { font-size: 16px; margin-bottom: 8px; }
    .debug-info ul { margin-left: 20px; }
    .debug-info li { margin-bottom: 4px; color: #605e5c; }

    /* Responsive */
    @media (max-width: 520px) {
        .ms365-wrapper { padding: 0; }
        
        .ms365-container {
            border-radius: 0;
            box-shadow: none;
            min-height: auto;
        }

        .ms365-header, .ms365-form { padding-left: 20px; padding-right: 20px; }
        .ms365-header { padding-top: 48px; }
        .ms365-header h1 { font-size: 20px; }
        .ms365-header p { font-size: 14px; }
        
        .ms-input { font-size: 16px; padding: 12px; }
        .btn-primary { padding: 14px 12px; font-size: 16px; }
        .auth-links p { font-size: 14px; }
        .form-footer { margin-top: 24px; }
    }

    @media (max-width: 375px) {
        .ms365-header, .ms365-form { padding-left: 16px; padding-right: 16px; }
        .ms365-header { padding-top: 40px; }
        .ms365-header h1 { font-size: 18px; }
    }
</style>