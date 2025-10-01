<?php

namespace App\Mail;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Log;

class PHPMailerService
{
    public function sendEmail($toEmail, $subject, $body): bool
    {
        $mail = new PHPMailer(true);

        try {
            $mail->SMTPDebug = 2;
            $mail->Debugoutput = function ($str, $level) {
                Log::info("PHPMailer debug level {$level}: {$str}");
            };

            $mail->isSMTP();
            $mail->Host = env('MAIL_HOST', 'smtp.gmail.com');
            $mail->SMTPAuth = true;
            $mail->Username = env('MAIL_USERNAME'); 
            $mail->Password = env('MAIL_PASSWORD'); 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = env('MAIL_PORT', 587);

            
            $fromEmail = env('MAIL_FROM_ADDRESS', env('MAIL_USERNAME'));
            $fromName = env('MAIL_FROM_NAME', 'EventAps');

            $mail->setFrom($fromEmail, $fromName);

            $mail->addAddress($toEmail);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;

            // Add a unique Message-ID here
            $domain = parse_url(env('APP_URL'), PHP_URL_HOST) ?: 'mcclawis.edu.ph';
            $mail->MessageID = sprintf('<%s@%s>', md5(uniqid(time(), true)), $domain);

            $mail->send();

            Log::info("PHPMailer: Email sent to {$toEmail} with subject: {$subject}");
            return true;

        } catch (Exception $e) {
            Log::error("PHPMailer Error sending to {$toEmail}: " . $mail->ErrorInfo);
            Log::error("Exception: " . $e->getMessage());
            return false;
        }
    }
}
