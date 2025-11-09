<?php

namespace App\Services;

use App\Models\Event;
use App\Models\User;
use App\Mail\PHPMailerService;
use Illuminate\Support\Facades\Log;

class EventNotificationService
{
    protected $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailerService();
    }

    /**
     * Notify all users about a new event
     */
    public function notifyNewEvent(Event $event)
    {
        try {
            // Get all active users
            $users = User::where('status', 'active')->get();

            $sentCount = 0;
            foreach ($users as $user) {
                // Check if event is available for user's department
                if ($event->isAvailableForUserDepartment($user->department)) {
                    if ($this->sendNewEventEmail($user, $event)) {
                        $sentCount++;
                    }
                }
            }

            Log::info("New event notifications sent for event: {$event->title} to {$sentCount} users");
            return $sentCount;
        } catch (\Exception $e) {
            Log::error("Failed to send event notifications: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Notify specific user about event update
     */
    public function notifyEventUpdate(Event $event, $message = null)
    {
        try {
            // Get all users who joined this event
            $joinedUsers = $event->joinedUsers;

            $sentCount = 0;
            foreach ($joinedUsers as $user) {
                if ($this->sendEventUpdateEmail($user, $event, $message)) {
                    $sentCount++;
                }
            }

            Log::info("Event update notifications sent for event: {$event->title} to {$sentCount} users");
            return $sentCount;
        } catch (\Exception $e) {
            Log::error("Failed to send event update notifications: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Notify users about event cancellation
     */
    public function notifyEventCancellation(Event $event)
    {
        try {
            $joinedUsers = $event->joinedUsers;

            $sentCount = 0;
            foreach ($joinedUsers as $user) {
                if ($this->sendEventCancellationEmail($user, $event)) {
                    $sentCount++;
                }
            }

            Log::info("Event cancellation notifications sent for event: {$event->title} to {$sentCount} users");
            return $sentCount;
        } catch (\Exception $e) {
            Log::error("Failed to send event cancellation notifications: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Send new event email to user
     */
    private function sendNewEventEmail(User $user, Event $event)
    {
        try {
            $subject = "New Event: {$event->title}";
            
            $eventDate = $event->date->format('F d, Y');
            $eventTime = $event->start_time ? $event->start_time->format('h:i A') : 'TBA';
            
            $body = $this->getEmailTemplate([
                'title' => 'New Event Available',
                'greeting' => "Hello {$user->first_name}!",
                'message' => "A new event has been created that you might be interested in.",
                'event_title' => $event->title,
                'event_description' => $event->description,
                'event_date' => $eventDate,
                'event_time' => $eventTime,
                'event_location' => $event->location,
                'event_department' => $event->department_names,
                'is_cancellation' => false,
                'badge_text' => 'NEW EVENT',
                'badge_color' => '#4f46e5',
            ]);

            return $this->mailer->sendEmail($user->email, $subject, $body);
        } catch (\Exception $e) {
            Log::error("Failed to send new event email to {$user->email}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send event update email to user
     */
    private function sendEventUpdateEmail(User $user, Event $event, $message = null)
    {
        try {
            $subject = "Event Updated: {$event->title}";
            
            $eventDate = $event->date->format('F d, Y');
            $eventTime = $event->start_time ? $event->start_time->format('h:i A') : 'TBA';
            
            $updateMessage = $message ?? "The event details have been updated. Please review the changes below.";
            
            $body = $this->getEmailTemplate([
                'title' => 'Event Update Notification',
                'greeting' => "Hello {$user->first_name}!",
                'message' => $updateMessage,
                'event_title' => $event->title,
                'event_description' => $event->description,
                'event_date' => $eventDate,
                'event_time' => $eventTime,
                'event_location' => $event->location,
                'event_department' => $event->department_names,
                'is_cancellation' => false,
                'is_update' => true,
                'badge_text' => 'EVENT UPDATE',
                'badge_color' => '#ffc107',
            ]);

            return $this->mailer->sendEmail($user->email, $subject, $body);
        } catch (\Exception $e) {
            Log::error("Failed to send update email to {$user->email}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send event cancellation email to user
     */
    private function sendEventCancellationEmail(User $user, Event $event)
    {
        try {
            $subject = "Event {$event->status}: {$event->title}";
            
            $cancelReason = $event->cancel_reason ?? "No reason provided";
            $statusText = ucfirst($event->status);
            
            $body = $this->getEmailTemplate([
                'title' => "Event {$statusText} Notice",
                'greeting' => "Hello {$user->first_name}!",
                'message' => "We regret to inform you that the following event has been {$event->status}.",
                'event_title' => $event->title,
                'event_description' => $event->description,
                'cancel_reason' => $cancelReason,
                'is_cancellation' => true,
                'badge_text' => strtoupper($event->status),
                'badge_color' => '#dc3545',
            ]);

            return $this->mailer->sendEmail($user->email, $subject, $body);
        } catch (\Exception $e) {
            Log::error("Failed to send cancellation email to {$user->email}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get email template
     */
    private function getEmailTemplate(array $data)
    {
        $isCancellation = $data['is_cancellation'] ?? false;
        $isUpdate = $data['is_update'] ?? false;
        
        // Convert logo to base64
        $logoPath = public_path('images/logo.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $imageData = file_get_contents($logoPath);
            $logoBase64 = 'data:image/png;base64,' . base64_encode($imageData);
        }
        
        // Convert background image to base64
        $bgPath = public_path('images/mcc background2.jpg');
        $bgBase64 = '';
        if (file_exists($bgPath)) {
            $bgData = file_get_contents($bgPath);
            $bgBase64 = 'data:image/jpeg;base64,' . base64_encode($bgData);
        }
        
        $badgeColor = $data['badge_color'] ?? '#4f46e5';
        $badgeText = $data['badge_text'] ?? 'NEW EVENT';

        // Build event details table
        $eventDetails = '';
        if (!$isCancellation) {
            $eventDetails = "
                <tr>
                    <td style='padding: 12px 18px; border-bottom: 1px solid #e5e7eb; width: 35%; font-weight: 700; color: #4b5563; font-size: 13px;'>
                        Date:
                    </td>
                    <td style='padding: 12px 18px; border-bottom: 1px solid #e5e7eb; color: #1f2937; font-size: 14px; font-weight: 500;'>
                        {$data['event_date']}
                    </td>
                </tr>
                <tr>
                    <td style='padding: 12px 18px; border-bottom: 1px solid #e5e7eb; font-weight: 700; color: #4b5563; font-size: 13px;'>
                        Time:
                    </td>
                    <td style='padding: 12px 18px; border-bottom: 1px solid #e5e7eb; color: #1f2937; font-size: 14px; font-weight: 500;'>
                        {$data['event_time']}
                    </td>
                </tr>
                <tr>
                    <td style='padding: 12px 18px; border-bottom: 1px solid #e5e7eb; font-weight: 700; color: #4b5563; font-size: 13px;'>
                        Location:
                    </td>
                    <td style='padding: 12px 18px; border-bottom: 1px solid #e5e7eb; color: #1f2937; font-size: 14px; font-weight: 500;'>
                        {$data['event_location']}
                    </td>
                </tr>
                <tr>
                    <td style='padding: 12px 18px; font-weight: 700; color: #4b5563; font-size: 13px;'>
                        Department:
                    </td>
                    <td style='padding: 12px 18px; color: #1f2937; font-size: 14px; font-weight: 500;'>
                        {$data['event_department']}
                    </td>
                </tr>
            ";
        } else {
            $eventDetails = "
                <tr>
                    <td colspan='2' style='padding: 20px; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-left: 5px solid #f59e0b; border-radius: 12px;'>
                        <p style='margin: 0 0 8px; font-weight: 700; color: #92400e; font-size: 15px;'>Reason:</p>
                        <p style='margin: 0; color: #b45309; font-size: 14px; line-height: 1.7;'>{$data['cancel_reason']}</p>
                    </td>
                </tr>
            ";
        }

        return "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>{$data['title']}</title>
        </head>
        <body style='margin: 0; padding: 0; font-family: \"Segoe UI\", Tahoma, Geneva, Verdana, sans-serif; background-color: #0a0a0a;'>
            <div style='margin: 0; padding: 40px 0; background-color: #0a0a0a;'>
                <table role='presentation' width='100%' cellpadding='0' cellspacing='0' border='0' align='center'>
                    <tr>
                        <td align='center'>
                            <!-- Main Container -->
                            <table role='presentation' width='650' cellpadding='0' cellspacing='0' border='0' style='max-width: 650px; width: 100%;'>
                                <tr>
                                    <td style='position: relative; padding: 0;'>
                                        
                                        <!-- Background Image Layer -->
                                        <div style='background-image: url(\"{$bgBase64}\"); background-size: cover; background-position: center; background-repeat: no-repeat; border-radius: 0; overflow: hidden;'>
                                            
                                            <!-- Purple Overlay -->
                                            <div style='background: linear-gradient(135deg, rgba(30, 20, 60, 0.90), rgba(60, 30, 90, 0.94), rgba(50, 20, 80, 0.92)); padding: 0;'>
                                                
                                                <!-- Header Section -->
                                                <table role='presentation' width='100%' cellpadding='0' cellspacing='0' border='0'>
                                                    <tr>
                                                        <td style='padding: 50px 40px 40px; text-align: center;'>
                                                            <!-- Logo -->
                                                            " . ($logoBase64 ? "<img src='{$logoBase64}' alt='MCC Logo' width='130' style='display: block; margin: 0 auto 30px; width: 130px; height: auto;'>" : "") . "
                                                            
                                                            <!-- Titles -->
                                                            <h1 style='margin: 0 0 5px; color: #ffffff; font-size: 32px; font-weight: 900; letter-spacing: 1px; text-transform: uppercase; line-height: 1.3; font-family: \"Segoe UI\", Tahoma, sans-serif;'>
                                                                MCC EVENT &<br>PORTFOLIO
                                                            </h1>
                                                            <h2 style='margin: 0 0 15px; color: #ff7b54; font-size: 32px; font-weight: 900; letter-spacing: 1px; text-transform: uppercase; line-height: 1.3; font-family: \"Segoe UI\", Tahoma, sans-serif;'>
                                                                ORGANIZER
                                                            </h2>
                                                            <p style='margin: 0; color: rgba(255, 255, 255, 0.95); font-size: 13px; font-weight: 600; letter-spacing: 2px; text-transform: uppercase;'>
                                                                Madridejos Community College
                                                            </p>
                                                        </td>
                                                    </tr>
                                                </table>

                                                <!-- Content Card -->
                                                <table role='presentation' width='100%' cellpadding='0' cellspacing='0' border='0'>
                                                    <tr>
                                                        <td style='padding: 0 40px 50px;'>
                                                            <table role='presentation' width='100%' cellpadding='0' cellspacing='0' border='0' style='background: rgba(255, 255, 255, 0.97); border-radius: 20px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);'>
                                                                <tr>
                                                                    <td style='padding: 50px 45px;'>
                                                                        
                                                                        <!-- Badge -->
                                                                        <table role='presentation' width='100%' cellpadding='0' cellspacing='0' border='0' style='margin: 0 0 30px;'>
                                                                            <tr>
                                                                                <td align='center'>
                                                                                    <span style='background-color: {$badgeColor}; color: white; padding: 10px 28px; border-radius: 25px; font-size: 12px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; display: inline-block;'>
                                                                                        {$badgeText}
                                                                                    </span>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                        
                                                                        <h2 style='margin: 0 0 25px; color: #2d1b4e; font-size: 32px; font-weight: 800; text-align: center; letter-spacing: -0.5px;'>
                                                                            Event Notification
                                                                        </h2>
                                                                        
                                                                        <p style='margin: 0 0 20px; color: #4b5563; font-size: 16px; line-height: 1.6; text-align: center; font-weight: 500;'>
                                                                            {$data['greeting']}
                                                                        </p>
                                                                        
                                                                        <p style='margin: 0 0 35px; color: #4b5563; font-size: 16px; line-height: 1.7; text-align: center;'>
                                                                            {$data['message']}
                                                                        </p>

                                                                        <!-- Event Details Box -->
                                                                        <table role='presentation' width='100%' cellpadding='0' cellspacing='0' border='0' style='margin: 40px 0;'>
                                                                            <tr>
                                                                                <td>
                                                                                    <table role='presentation' width='100%' cellpadding='0' cellspacing='0' border='0' style='background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 18px; overflow: hidden; border: 2px solid {$badgeColor};'>
                                                                                        <tr>
                                                                                            <td style='padding: 35px 30px;'>
                                                                                                <h3 style='margin: 0 0 12px; color: #ff7b54; font-size: 22px; font-weight: 900; text-align: center; letter-spacing: 0.5px; text-transform: uppercase; font-family: \"Segoe UI\", Tahoma, sans-serif;'>
                                                                                                    {$data['event_title']}
                                                                                                </h3>
                                                                                                <p style='margin: 0 0 25px; color: #1f2937; font-size: 14px; line-height: 1.8; text-align: center; padding: 0 10px; font-weight: 500;'>
                                                                                                    {$data['event_description']}
                                                                                                </p>
                                                                                                
                                                                                                <!-- Event Details Table -->
                                                                                                <table role='presentation' width='100%' cellpadding='0' cellspacing='0' border='0' style='background-color: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb;'>
                                                                                                    {$eventDetails}
                                                                                                </table>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>
                                                                        </table>

                                                                        <p style='margin: 32px 0 0; color: #6b7280; font-size: 14px; line-height: 1.7; text-align: center;'>
                                                                            If you have any questions about this event, please contact our support team.
                                                                        </p>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>

                                                <!-- Footer -->
                                                <table role='presentation' width='100%' cellpadding='0' cellspacing='0' border='0'>
                                                    <tr>
                                                        <td style='padding: 40px 40px 55px;'>
                                                            <table role='presentation' width='100%' cellpadding='0' cellspacing='0' border='0' style='background: rgba(255, 255, 255, 0.08); border-radius: 14px; border: 1px solid rgba(255, 255, 255, 0.15);'>
                                                                <tr>
                                                                    <td style='padding: 32px; text-align: center;'>
                                                                        <p style='margin: 0 0 14px; color: rgba(255, 255, 255, 0.98); font-size: 15px; line-height: 1.6; font-weight: 500;'>
                                                                            Need help? Contact us at <a href='mailto:support@mcc-epo.com' style='color: #ffd93d; text-decoration: none; font-weight: 700;'>support@mcc-epo.com</a>
                                                                        </p>
                                                                        <p style='margin: 0; color: rgba(255, 255, 255, 0.85); font-size: 13px; line-height: 1.6;'>
                                                                            © " . date('Y') . " Madridejos Community College. All rights reserved.
                                                                        </p>
                                                                        <div style='margin-top: 22px; padding-top: 22px; border-top: 1px solid rgba(255, 255, 255, 0.18);'>
                                                                            <p style='margin: 0; color: rgba(255, 255, 255, 0.75); font-size: 12px; line-height: 1.5;'>
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
                            <table role='presentation' width='650' cellpadding='0' cellspacing='0' border='0' style='max-width: 650px; margin-top: 28px;'>
                                <tr>
                                    <td style='padding: 0 20px; text-align: center;'>
                                        <p style='margin: 0; color: #9ca3af; font-size: 13px; line-height: 1.6;'>
                                            You received this email because you are registered for MCC Event & Portfolio Organizer.
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
        ";
    }
}