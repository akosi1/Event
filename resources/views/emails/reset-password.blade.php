<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #0a0a0a;">
    @php
        // Convert logo to base64
        $logoPath = public_path('images/logo.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoData = file_get_contents($logoPath);
            $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
        }
        
        // Convert background image to base64
        $bgPath = public_path('images/mcc background2.jpg');
        $bgBase64 = '';
        if (file_exists($bgPath)) {
            $bgData = file_get_contents($bgPath);
            $bgBase64 = 'data:image/jpeg;base64,' . base64_encode($bgData);
        }
    @endphp

    <div style="margin: 0; padding: 40px 0; background-color: #0a0a0a;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
            <tr>
                <td align="center">
                    <!-- Main Container -->
                    <table role="presentation" width="650" cellpadding="0" cellspacing="0" border="0" style="max-width: 650px; width: 100%;">
                        <tr>
                            <td style="position: relative; padding: 0;">
                                
                                <!-- Background Image Layer -->
                                <div style="background-image: url('{{ $bgBase64 }}'); background-size: cover; background-position: center; background-repeat: no-repeat; border-radius: 0; overflow: hidden;">
                                    
                                    <!-- Purple Overlay -->
                                    <div style="background: linear-gradient(135deg, rgba(30, 20, 60, 0.90), rgba(60, 30, 90, 0.94), rgba(50, 20, 80, 0.92)); padding: 0;">
                                        
                                        <!-- Header Section -->
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="padding: 50px 40px 40px; text-align: center;">
                                                    <!-- Logo -->
                                                    @if($logoBase64)
                                                    <img src="{{ $logoBase64 }}" alt="MCC Logo" width="130" style="display: block; margin: 0 auto 30px; width: 130px; height: auto;">
                                                    @endif
                                                    
                                                    <!-- Titles -->
                                                    <h1 style="margin: 0 0 8px; color: #ffffff; font-size: 48px; font-weight: 800; letter-spacing: 2px; text-transform: uppercase; line-height: 1.2; font-family: 'Segoe UI', Tahoma, sans-serif;">
                                                        MCC EVENT & PORTFOLIO
                                                    </h1>
                                                    <h2 style="margin: 0 0 20px; color: #ff7b54; font-size: 42px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase; line-height: 1.2; font-family: 'Segoe UI', Tahoma, sans-serif;">
                                                        ORGANIZER
                                                    </h2>
                                                    <p style="margin: 0; color: rgba(255, 255, 255, 0.95); font-size: 15px; font-weight: 500; letter-spacing: 3px; text-transform: uppercase;">
                                                        Madridejos Community College
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Content Card -->
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="padding: 0 40px 50px;">
                                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background: rgba(255, 255, 255, 0.97); border-radius: 20px;">
                                                        <tr>
                                                            <td style="padding: 50px 45px;">
                                                                <h2 style="margin: 0 0 25px; color: #2d1b4e; font-size: 32px; font-weight: 800; text-align: center; letter-spacing: -0.5px;">
                                                                    Reset Password Request
                                                                </h2>
                                                                
                                                                <p style="margin: 0 0 20px; color: #4b5563; font-size: 16px; line-height: 1.6; text-align: center; font-weight: 500;">
                                                                    Hello,
                                                                </p>
                                                                
                                                                <p style="margin: 0 0 35px; color: #4b5563; font-size: 16px; line-height: 1.7; text-align: center;">
                                                                    You are receiving this email because we received a password reset request for your account. Click the button below to reset your password:
                                                                </p>

                                                                <!-- Reset Button -->
                                                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin: 40px 0;">
                                                                    <tr>
                                                                        <td align="center">
                                                                            <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                                                                <tr>
                                                                                    <td style="background: #1a1a2e; border-radius: 14px; box-shadow: 0 4px 15px rgba(26, 26, 46, 0.5);">
                                                                                        <a href="{{ $url }}" style="display: inline-block; padding: 18px 50px; color: #ffffff; text-decoration: none; font-size: 18px; font-weight: 900; letter-spacing: 1px; text-transform: uppercase; font-family: 'Segoe UI', Tahoma, sans-serif;">
                                                                                            RESET PASSWORD
                                                                                        </a>
                                                                                    </td>
                                                                                </tr>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                </table>

                                                                <!-- Expiry Notice -->
                                                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin: 32px 0;">
                                                                    <tr>
                                                                        <td style="padding: 20px 22px; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-left: 5px solid #f59e0b; border-radius: 12px;">
                                                                            <p style="margin: 0; color: #92400e; font-size: 15px; line-height: 1.7; font-weight: 600;">
                                                                                ⏰ <strong>Important:</strong> This password reset link will expire in <strong>{{ $count }} minutes</strong>. Please reset your password as soon as possible.
                                                                            </p>
                                                                        </td>
                                                                    </tr>
                                                                </table>

                                                                <p style="margin: 32px 0 0; color: #6b7280; font-size: 14px; line-height: 1.7; text-align: center;">
                                                                    If you did not request a password reset, no further action is required. Your password will remain unchanged.
                                                                </p>
                                                                
                                                                <!-- Alternative Link -->
                                                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top: 32px;">
                                                                    <tr>
                                                                        <td style="padding: 20px; background: #f9fafb; border-radius: 12px;">
                                                                            <p style="margin: 0 0 10px; color: #6b7280; font-size: 13px; text-align: center;">
                                                                                If you're having trouble clicking the button, copy and paste the URL below into your web browser:
                                                                            </p>
                                                                            <p style="margin: 0; color: #2d1b4e; font-size: 12px; word-break: break-all; text-align: center;">
                                                                                {{ $url }}
                                                                            </p>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Footer -->
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="padding: 40px 40px 55px;">
                                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background: rgba(255, 255, 255, 0.08); border-radius: 14px; border: 1px solid rgba(255, 255, 255, 0.15);">
                                                        <tr>
                                                            <td style="padding: 32px; text-align: center;">
                                                                <p style="margin: 0 0 14px; color: rgba(255, 255, 255, 0.98); font-size: 15px; line-height: 1.6; font-weight: 500;">
                                                                    Need help? Contact us at <a href="mailto:support@mcc-epo.com" style="color: #ffd93d; text-decoration: none; font-weight: 700;">support@mcc-epo.com</a>
                                                                </p>
                                                                <p style="margin: 0; color: rgba(255, 255, 255, 0.85); font-size: 13px; line-height: 1.6;">
                                                                    © {{ date('Y') }} Madridejos Community College. All rights reserved.
                                                                </p>
                                                                <div style="margin-top: 22px; padding-top: 22px; border-top: 1px solid rgba(255, 255, 255, 0.18);">
                                                                    <p style="margin: 0; color: rgba(255, 255, 255, 0.75); font-size: 12px; line-height: 1.5;">
                                                                        This is an automated message. Please do not reply to this email.
                                                                    </p>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>

                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>

                    <!-- Bottom Notice -->
                    <table role="presentation" width="650" cellpadding="0" cellspacing="0" border="0" style="max-width: 650px; margin-top: 28px;">
                        <tr>
                            <td style="padding: 0 20px; text-align: center;">
                                <p style="margin: 0; color: #9ca3af; font-size: 13px; line-height: 1.6;">
                                    You received this email because a password reset was requested for your MCC Event & Portfolio Organizer account.
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>