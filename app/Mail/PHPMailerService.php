<?php

namespace App\Mail;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class PHPMailerService
{
    public function sendEmail($toEmail, $subject, $body): bool
    {
        $mail = new PHPMailer(true);

        try {
            $mail->SMTPDebug = 0;

            $mail->isSMTP();
            $mail->Host = env('MAIL_HOST', 'smtp.gmail.com');
            $mail->SMTPAuth = true;
            $mail->Username = env('MAIL_USERNAME');
            $mail->Password = env('MAIL_PASSWORD');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = env('MAIL_PORT', 587);

            $mail->CharSet = 'UTF-8';

            $fromEmail = env('MAIL_FROM_ADDRESS', env('MAIL_USERNAME'));
            $fromName = env('MAIL_FROM_NAME', 'EventAps');

            $mail->setFrom($fromEmail, $fromName);
            $mail->addReplyTo($fromEmail, $fromName);

            $mail->addAddress($toEmail);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = strip_tags($body);

            $mail->send();

            return true;

        } catch (Exception $e) {
            return false;
        }
    }
}
