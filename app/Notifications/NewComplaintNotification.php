<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewComplaintNotification extends Notification
{
    use Queueable;

    public $complaint;
    public function __construct($complaint)
    {
        $this->complaint = $complaint;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Complaint Submitted')
            ->greeting('Hello ' . $notifiable->first_name . ',')
            ->line('A new complaint has been submitted by a citizen.')
            ->line('Complaint Type: ' . $this->complaint->type)
            ->action('View Complaint', url('/complaints/' . $this->complaint->id))
            ->line('Please review it as soon as possible.');
    }

    public function toArray($notifiable)
    {
        $first_name = $this->complaint->citizen->user->first_name;
        $last_name = $this->complaint->citizen->user->last_name;

        return [
            'complaint_id' => $this->complaint->id,
            'type' => $this->complaint->type,
            'message' => 'A new complaint has been submitted by ' . $first_name . ' ' . $last_name,
        ];
    }
}
