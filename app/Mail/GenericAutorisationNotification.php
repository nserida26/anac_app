<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Version e-mail des notifications de workflow autorisation, normalement envoyées
 * par WhatsApp (DtaAutorisationNotificationService). Même contenu texte, mis en forme
 * simplement en HTML — voir DtaAutorisationNotificationService::notify().
 */
class GenericAutorisationNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $subjectLine;
    public string $bodyText;

    public function __construct(string $subjectLine, string $bodyText)
    {
        $this->subjectLine = $subjectLine;
        $this->bodyText = $bodyText;
    }

    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
            ->replyTo('survol.dta@anac.mr', 'ANAC - Direction du Transport Aérien')
            ->subject($this->subjectLine)
            ->view('admin.emails.generic_notification')
            ->withSwiftMessage(function ($message) {
                $headers = $message->getHeaders();
                $headers->addTextHeader('List-Unsubscribe', '<mailto:survol.dta@anac.mr?subject=Unsubscribe>');
                $headers->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
                $headers->addTextHeader('X-Auto-Response-Suppress', 'OOF, AutoReply');
                $headers->addTextHeader('Auto-Submitted', 'auto-generated');
            });
    }
}
