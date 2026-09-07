<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendFlightAuthorization extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $autorisation;

    public function __construct($autorisation)
    {
        $this->autorisation = $autorisation;
    }

    public function build()
    {
        set_time_limit(400);

        return $this->from(config('mail.from.address'), config('mail.from.name'))
            ->replyTo('survol.dta@anac.mr', 'ANAC - Direction du Transport Aérien')
            ->subject('Autorisation de vol ' . $this->autorisation->code_autorisation . ' - ANAC')
            ->view('admin.emails.flight_authorization_email')
            ->text('admin.emails.flight_authorization_email_text')
            ->withSwiftMessage(function ($message) {
                // En-têtes qui améliorent la délivrabilité et limitent le classement en spam
                $headers = $message->getHeaders();
                $headers->addTextHeader('List-Unsubscribe', '<mailto:survol.dta@anac.mr?subject=Unsubscribe>');
                $headers->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
                $headers->addTextHeader('X-Auto-Response-Suppress', 'OOF, AutoReply');
                $headers->addTextHeader('Auto-Submitted', 'auto-generated');
            });
    }
}
