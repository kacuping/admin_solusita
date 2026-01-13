<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FcmService
{
    public function sendToToken(string $token, string $title, string $body, array $data = []): void
    {
        $serverKey = config('services.fcm.server_key');
        if (!$serverKey) {
            return;
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

        Http::withHeaders([
            'Authorization' => 'key=' . $serverKey,
            'Content-Type' => 'application/json',
        ])->post('https://fcm.googleapis.com/fcm/send', $payload);
        
        
    }
}
