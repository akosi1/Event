<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h2 style="color: #333;">Events & Portfolio Organizer</h2>

        <h3 style="color: #007bff;">OTP Verification Code</h3>

        <p>Hello,</p>
        <p>Thank you for signing up! Your One-Time Password (OTP) for account verification is:</p>

        <div style="text-align: center; margin: 30px 0;">
            <span style="font-size: 28px; font-weight: bold; color: #000;">{{ $otp }}</span>
        </div>

        <p>This OTP is valid for the next <strong>10 minutes</strong>. Please use it to complete your account verification.</p>

        <p>If you did not request this OTP, you can safely ignore this email.</p>

        <p>Regards,<br>
        <strong>Events & Portfolio Organizer</strong></p>
    </div>
</body>
</html>
