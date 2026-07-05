<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoginRequestApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $loginUrl;

    /**
     * Create a new notification instance.
     *
     * @param string $loginUrl
     */
    public function __construct(string $loginUrl)
    {
        $this->loginUrl = $loginUrl;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail']; // Send via both database and email
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Login Request Has Been Approved')
            ->line('Your request to access a user account has been approved.')
            ->action('Complete Login', $this->loginUrl)
            ->line('This link will expire in 15 minutes.')
            ->line('If you did not request this access, please contact your administrator immediately.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'message' => 'Your login request has been approved',
            'action' => 'Complete login',
            'url' => $this->loginUrl,
            'expiry' => now()->addMinutes(15)->toDateTimeString()
        ];
    }
}
