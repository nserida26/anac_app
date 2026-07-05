<?php

namespace App\Services\WhatsApp;

class WhatsAppService
{
    protected $provider;

    public function __construct(WhatsAppInterface $provider)
    {
        $this->provider = $provider;
    }

    public function sendMessage(string $to, string $message): array
    {
        return $this->provider->sendText($to, $message);
    }
}
