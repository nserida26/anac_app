<?php

namespace App\Services\WhatsApp\Providers;

use App\Services\WhatsApp\WhatsAppInterface;
use Illuminate\Support\Facades\Http;
use UltraMsg\WhatsAppApi;

class UltramsgService implements WhatsAppInterface
{
    protected $client;

    public function __construct()
    {
        $this->client = new WhatsAppApi(
            config('services.ultramsg.instance_id'),
            config('services.ultramsg.token')
        );
    }

    // app/Services/WhatsApp/Providers/UltramsgService.php

    public function sendText(string $to, string $message): array
    {
        // First check instance status
        $status = $this->getInstanceStatus();

        if ($status['status']['accountStatus']['status'] !== 'authenticated') {
            return [
                'status' => 'failed',
                //'message' => $status['message'],
                //'solution' => $status['solution']
            ];
        }

        // Rest of your send logic
        $response = $this->client->sendChatMessage($to, $message);

        return [
            'status' => $response,
            //'message_id' => $response['id'] ?? null
        ];
    }

    private function getInstanceStatus(): array
    {
        $response = Http::get("https://api.ultramsg.com/" . config('services.ultramsg.instance_id') . "/instance/status", [
            'token' => config('services.ultramsg.token')
        ])->json();
        //dd($response);

        $statusMap = [
            //'open' => ['message' => 'Instance is ready', 'solution' => null],
            'close' => ['message' => 'Instance disconnected', 'solution' => 'Reconnect in Ultramsg dashboard'],
            'connecting' => ['message' => 'Instance connecting', 'solution' => 'Wait 2-3 minutes'],
            'pending' => ['message' => 'Pending activation', 'solution' => 'Scan QR code in dashboard'],
            'stopped' => ['message' => 'Instance suspended', 'solution' => 'Renew subscription'],
        ];

        return [
            'status' => $response['status'] ?? 'unknown',
            //'message' => $statusMap[$response['status']]['message'] ?? 'Unknown status',
            //'solution' => $statusMap[$response['status']]['solution'] ?? 'Contact Ultramsg support',
            //'qr' => $response['qr'] ?? null
        ];
    }

    public function sendTemplate(string $to, string $template, array $params = []): array
    {
        $response = $this->client->sendTemplateMessage(
            $to,
            $template,
            $params['components'] ?? []
        );

        return [
            'status' => isset($response['sent']) && $response['sent'] ? 'success' : 'failed',
            'message_id' => $response['id'] ?? null
        ];
    }

    public function getMessageStatus(string $messageId): array
    {
        $response = $this->client->getMessageStatus($messageId);

        return [
            'status' => $response['status'] ?? 'unknown',
            'timestamp' => $response['timestamp'] ?? null
        ];
    }
}
