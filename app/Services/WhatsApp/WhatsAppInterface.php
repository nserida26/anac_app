<?php

// app/Services/WhatsApp/WhatsAppInterface.php
namespace App\Services\WhatsApp;

interface WhatsAppInterface
{
    public function sendText(string $to, string $message): array;
    public function sendTemplate(string $to, string $template, array $params): array;
    public function getMessageStatus(string $messageId): array;
}
