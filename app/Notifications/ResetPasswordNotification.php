<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = $this->resetUrl($notifiable);
        
        return (new MailMessage)
            ->subject('Reset Password Request')
            ->view('emails.reset-password', [
                'url' => $url,
                'notifiable' => $notifiable,
                'count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')
            ]);
    }
}