<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;
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

        return $this->subject('Flight Authorization ' . $this->autorisation->code_autorisation)
            ->view('admin.emails.flight_authorization_email');
    }
}
