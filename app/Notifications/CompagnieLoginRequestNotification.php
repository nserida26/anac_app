<?php

namespace App\Notifications;

use App\Models\CompagnieLoginRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CompagnieLoginRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public CompagnieLoginRequest $request) {}

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Login Request from ' . $this->request->compagnieUser->compagnie->name)
            ->line('You have received a login request from your compagnie.')
            ->action('Review Request', route('user.login.requests'))
            ->line('This request will expire in 24 hours');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Login request from ' . $this->request->compagnieUser->compagnie->name,
            'request_id' => $this->request->id,
            'link' => route('user.login.requests')
        ];
    }
}
