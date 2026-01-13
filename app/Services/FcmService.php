<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FcmService
{
    public function sendToToken(string $token, string $title, string $body, array $data = []): void
    {
        $serverKey = config('services.fcm.server_key', env('FCM_SERVER_KEY'));
        if (!$serverKey) {
            return ['success' => false, 'message' => 'FCM server key not configured'];
        }

        $payload = [
            'to' => $token,
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
            'data' => $data,
            'priority' => 'high',
        ];

        $response = Http::withHeaders([
            'Authorization' => 'key=' . $serverKey,
            'Content-Type' => 'application/json',
        ])->post('https://fcm.googleapis.com/fcm/send', $payload);
        
        // Response handling intentionally omitted
    }
}
