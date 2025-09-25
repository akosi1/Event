<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $type === 'registration' ? 'Account Registration' : 'Login Verification' }} - McClawis</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
            color: #374151;
            line-height: 1.6;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .header p {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .content {
            padding: 40px 30px;
        }
        
        .greeting {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #1f2937;
        }
        
        .message {
            font-size: 16px;
            margin-bottom: 30px;
            color: #6b7280;
        }
        
        .otp-container {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            border: 2px solid #dc2626;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }
        
        .otp-label {
            font-size: 14px;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .otp-code {
            font-size: 36px;
            font-weight: 800;
            color: #dc2626;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
            margin-bottom: 15px;
        }
        
        .otp-note {
            font-size: 13px;
            color: #9ca3af;
        }
        
        .warning {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px 20px;
            margin: 25px 0;
            border-radius: 6px;
        }
        
        .warning-title {
            font-weight: 600;
            color: #92400e;
            margin-bottom: 5px;
        }
        
        .warning-text {
            font-size: 14px;
            color: #a16207;
        }
        
        .instructions {
            background: #f0f9ff;
            border: 1px solid #0ea5e9;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .instructions h3 {
            color: #0c4a6e;
            font-size: 16px;
            margin-bottom: 12px;
        }
        
        .instructions ol {
            padding-left: 20px;
            color: #0369a1;
        }
        
        .instructions li {
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .footer {
            background: #f9fafb;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        
        .footer-logo {
            font-size: 20px;
            font-weight: 700;
            color: #dc2626;
            margin-bottom: 15px;
        }
        
        .footer-text {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 10px;
        }
        
        .contact-info {
            font-size: 13px;
            color: #9ca3af;
        }
        
        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, #e5e7eb, transparent);
            margin: 25px 0;
        }
        
        @media (max-width: 600px) {
            .email-container {
                margin: 0;
                border-radius: 0;
            }
            
            .header, .content, .footer {
                padding: 25px 20px;
            }
            
            .otp-code {
                font-size: 28px;
                letter-spacing: 4px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>{{ $type === 'registration' ? 'Account Registration' : 'Login Verification' }}</h1>
            <p>McClawis Educational Institution</p>
        </div>
        
        <!-- Content -->
        <div class="content">
            <div class="greeting">Hello {{ $studentName }},</div>
            
            <div class="message">
                @if($type === 'registration')
                    We received a request to create a new account for your MS365 email address. To complete your registration, please use the verification code below:
                @else
                    We received a login attempt for your account. To verify your identity and complete the login process, please use the verification code below:
                @endif
            </div>
            
            <!-- OTP Container -->
            <div class="otp-container">
                <div class="otp-label">Your Verification Code</div>
                <div class="otp-code">{{ $otpCode }}</div>
                <div class="otp-note">This code expires in {{ $expiresInMinutes }} minutes</div>
            </div>
            
            <!-- Instructions -->
            <div class="instructions">
                <h3>How to use this code:</h3>
                <ol>
                    <li>Return to the McClawis portal login/registration page</li>
                    <li>Enter this 6-digit code when prompted</li>
                    <li>Complete your {{ $type === 'registration' ? 'account setup' : 'login process' }}</li>
                </ol>
            </div>
            
            <div class="divider"></div>
            
            <!-- Security Warning -->
            <div class="warning">
                <div class="warning-title">Security Notice</div>
                <div class="warning-text">
                    If you did not request this {{ $type === 'registration' ? 'account registration' : 'login attempt' }}, please ignore this email and contact our IT support immediately. Never share this code with anyone.
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div class="footer-logo">McClawis Educational Institution</div>
            <div class="footer-text">Student Information System</div>
            <div class="footer-text">This is an automated message, please do not reply.</div>
            
            <div class="divider"></div>
            
            <div class="contact-info">
                For technical support, contact: support@mcclawis.edu.ph<br>
                Phone: (123) 456-7890 | Website: www.mcclawis.edu.ph
            </div>
        </div>
    </div>
</body>
</html>