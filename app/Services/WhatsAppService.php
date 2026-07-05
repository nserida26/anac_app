<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $client;
    protected $instanceId;
    protected $token;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.ultramsg.com/',
            'timeout'  => 10.0,
        ]);

        $this->instanceId = config('services.ultramsg.instance_id');
        $this->token = config('services.ultramsg.token');
    }

    public function sendMessage(string $to, string $message): array
    {
        try {
            $response = $this->client->get("{$this->instanceId}/messages/chat", [
                'query' => [
                    'token' => $this->token,
                    'to' => $to,
                    'body' => $message
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            return [
                'success' => $data['sent'] ?? false,
                'message_id' => $data['id'] ?? null,
                'response' => $data
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp API Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    // In WhatsAppService.php
    public function sendWithAttachment(
        string $to,
        string $message,
        string $documentUrl,
        string $filename
    ): array {
        try {
            $response = $this->client->post("{$this->instanceId}/messages/document", [
                'form_params' => [
                    'token' => $this->token,
                    'to' => $to,
                    'filename' => $filename,
                    'document' => $documentUrl,
                    'caption' => $message
                ]
            ]);

            return $this->handleResponse($response);
        } catch (\Exception $e) {
            return $this->handleError($e);
        }
    }

    // app/Services/WhatsAppService.php

    public function sendRichMessage(string $to, string $message): array
    {
        try {
            // Format message with Markdown-like syntax
            $formattedMessage = $this->formatMessage($message);

            $payload = [
                'token' => $this->token,
                'to' => $to,
                'body' => $formattedMessage
            ];



            $response = $this->client->post("{$this->instanceId}/messages/chat", [
                'form_params' => $payload,
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ]
            ]);

            return $this->handleResponse($response);
        } catch (\Exception $e) {
            return $this->handleError($e);
        }
    }

    private function formatMessage(string $message): string
    {
        // Basic formatting (Ultramsg supports some Markdown)
        $replacements = [
            '/\*(.*?)\*/' => '*$1*',       // Bold
            '/\_(.*?)\_/' => '_$1_',       // Italic
            '/\~(.*?)\~/' => '~$1~',       // Strikethrough
            '/```(.*?)```/s' => '```$1```' // Monospace
        ];

        return preg_replace(array_keys($replacements), array_values($replacements), $message);
    }

    private function handleResponse($response): array
    {
        $data = json_decode($response->getBody(), true);

        return [
            'success' => $data['sent'] ?? false,
            'message_id' => $data['id'] ?? null,
            'response' => $data
        ];
    }

    private function handleError(\Exception $e): array
    {
        Log::error('WhatsApp Error: ' . $e->getMessage());

        return [
            'success' => false,
            'error' => $e->getMessage(),
            'solution' => 'Check formatting or API status'
        ];
    }


    public function checkInstanceStatus(): array
    {
        try {
            $response = $this->client->get("{$this->instanceId}/instance/status", [
                'query' => ['token' => $this->token]
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }
}
