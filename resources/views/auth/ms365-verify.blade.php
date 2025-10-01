<x-guest-layout>
    <div class="ms365-wrapper">
        <div class="ms365-container">
            <div class="ms365-header">
                <div class="ms-logo">
                    <img src="images/logo.png" alt="Logo" width="48" height="48">
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
                        <div class="input-wrapper">
                            <i class="fas fa-envelope input-icon"></i>
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
                        </div>
                        @error('email')
                            <div class="error-msg">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-paper-plane"></i>
                            Send verification code
                        </button>
                    </div>

                    <div class="divider">or</div>

                    <div class="form-footer">
                        <div class="auth-links">
                            <p>Already have an account?</p>
                            <a href="{{ route('login') }}" class="btn-secondary">Sign in here</a>
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
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
        background: #f5f5f5;
        color: #323130;
    }

    .ms365-wrapper {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }

    .ms365-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 400px;
        padding: 32px 24px;
    }

    .ms365-header {
        text-align: center;
        margin-bottom: 24px;
    }

    .ms-logo { margin-bottom: 16px; display: flex; justify-content: center; }

    .ms365-header h1 {
        font-size: 22px;
        font-weight: 600;
        margin-bottom: 8px;
        color: #1a1a1a;
    }

    .ms365-header p { font-size: 14px; color: #666; }
    .ms365-form { width: 100%; }
    .form-group { margin-bottom: 16px; }

    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
    }

    .input-wrapper { position: relative; display: flex; align-items: center; }

    .input-icon {
        position: absolute;
        left: 14px;
        color: #0078d4;
        font-size: 16px;
        pointer-events: none;
    }

    .ms-input {
        width: 100%;
        padding: 14px 14px 14px 44px;
        font-size: 15px;
        border: 2px solid #e0e0e0;
        border-radius: 6px;
        transition: border-color 0.2s;
        outline: none;
    }

    .ms-input:focus { border-color: #0078d4; }
    .ms-input.error { border-color: #d13438; }

    .error-msg {
        margin-top: 8px;
        padding: 8px 12px;
        background: #fef0f0;
        border: 1px solid #d13438;
        border-radius: 6px;
        color: #d13438;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .btn-primary, .btn-secondary {
        width: 100%;
        padding: 14px;
        border-radius: 6px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-primary {
        background: #0078d4;
        color: white;
        border: none;
    }

    .btn-primary:hover { background: #106ebe; }

    .btn-secondary {
        background: white;
        color: #0078d4;
        border: 2px solid #0078d4;
        text-decoration: none;
    }

    .btn-secondary:hover { background: #f0f7ff; }

    .divider {
        text-align: center;
        color: #999;
        font-size: 14px;
        margin: 20px 0;
    }

    .form-footer { margin-top: 0; }
    .auth-links { text-align: center; }
    .auth-links p { font-size: 14px; color: #666; margin-bottom: 12px; }

    .mb-4 { margin-bottom: 16px; }

    .mb-4 div {
        padding: 12px 14px;
        background: #dff6dd;
        border: 1px solid #107c10;
        border-radius: 6px;
        color: #107c10;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .mb-4 div::before {
        content: "\f00c";
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
    }

    .debug-info {
        margin-bottom: 16px;
        padding: 12px 14px;
        background: #fff4ce;
        border: 1px solid #fde300;
        border-radius: 6px;
        font-size: 13px;
    }

    .debug-info h3 { font-size: 15px; margin-bottom: 8px; }
    .debug-info ul { margin-left: 18px; }
    .debug-info li { margin-bottom: 4px; color: #605e5c; }

    @media (max-width: 520px) {
        .ms365-wrapper { padding: 0; align-items: stretch; }
        
        .ms365-container {
            border-radius: 0;
            box-shadow: none;
            min-height: 100vh;
            max-width: 100%;
            padding: 48px 24px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .ms365-header h1 { font-size: 20px; }
        .ms365-header p { font-size: 13px; }
        .ms-input { font-size: 16px; padding: 13px 13px 13px 42px; }
        .btn-primary, .btn-secondary { padding: 13px; font-size: 16px; }
        .auth-links p { font-size: 13px; }
    }

    @media (max-width: 375px) {
        .ms365-container { padding: 40px 20px; }
        .ms365-header h1 { font-size: 19px; }
    }
</style>