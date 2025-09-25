<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $otpCode;
    public string $studentName;
    public string $type;
    public int $expiresInMinutes;

    public function __construct(string $otpCode, string $studentName, string $type = 'registration', int $expiresInMinutes = 10)
    {
        $this->otpCode = $otpCode;
        $this->studentName = $studentName;
        $this->type = $type;
        $this->expiresInMinutes = $expiresInMinutes;
    }

    public function envelope(): Envelope
    {
        $subject = $this->type === 'registration' 
            ? 'Account Registration OTP - McClawis' 
            : 'Login Verification OTP - McClawis';
            
        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.otp-verification',
            text: 'emails.otp-verification-text'
        );
    }

    public function attachments(): array
    {
        return [];
    }
}