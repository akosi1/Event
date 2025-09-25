<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>McClawis Verification</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6;">
    <p>Dear {{ $studentName }},</p>

    <p>
        {{ $type === 'registration' ? 'Account Registration' : 'Login Verification' }} - <strong>McClawis</strong>
    </p>

    <p>
        <strong>Your verification code:</strong> <span style="font-size: 18px; font-weight: bold;">{{ $otpCode }}</span><br>
        <small>This code will expire in {{ $expiresInMinutes }} minutes.</small>
    </p>

    <p>
        If you did not request this {{ $type === 'registration' ? 'registration' : 'login' }}, please contact our support team immediately.
    </p>

    <p>
        Regards,<br>
        <strong>McClawis Educational Institution</strong><br>
        📧 <a href="mailto:support@mcclawis.edu.ph">support@mcclawis.edu.ph</a>
    </p>
</body>
</html>
