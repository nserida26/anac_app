<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// app/Http/Controllers/WhatsAppController.php
use App\Services\WhatsAppService;

class WhatsAppController extends Controller
{
    protected $whatsApp;

    public function __construct(WhatsAppService $whatsApp)
    {
        $this->whatsApp = $whatsApp;
    }

    public function send(Request $request)
    {
        $response = $this->whatsApp->sendMessage(
            $request->input('phone'),
            $request->input('message')
        );

        if (!$response['success']) {
            return response()->json([
                'error' => $response['error'] ?? 'Failed to send message'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message_id' => $response['message_id']
        ]);
    }
    // app/Http/Controllers/WhatsAppController.php

    public function sendRichMessage(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string'
        ]);

        $response = $this->whatsApp->sendRichMessage(
            $validated['phone'],
            $validated['message'],
        );

        return response()->json($response);
    }


    public function status()
    {
        return response()->json(
            $this->whatsApp->checkInstanceStatus()
        );
    }
}
