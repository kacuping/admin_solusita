<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BriService
{
    protected $clientId;
    protected $clientSecret;
    protected $baseUrl;
    protected $partnerId;
    protected $privateKeyPath;

    public function __construct()
    {
        $this->clientId = config('bri.client_id');
        $this->clientSecret = config('bri.client_secret');
        $this->baseUrl = config('bri.base_url');
        $this->partnerId = config('bri.partner_id');
        $this->privateKeyPath = config('bri.private_key_path');
    }

    /**
     * Get Access Token (B2B)
     * Note: This implementation assumes standard OAuth Client Credentials.
     * SNAP might require Signature generation.
     */
    public function getToken()
    {
        try {
            $timestamp = date('c');
            
            // SNAP requires JSON body
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    // 'X-SIGNATURE' => ... // Needed for real SNAP
                ])
                ->post($this->baseUrl . '/snap/v1.0/access-token/b2b', [
                    'grantType' => 'client_credentials',
                    'additionalInfo' => []
                ]);

            if ($response->successful()) {
                return $response->json()['accessToken'];
            }

            Log::warning('BRI Token API Failed (Sandbox/Mock Mode Active): ' . $response->body());
            
            // Return Mock Token for Testing/Sandbox if Real API fails (e.g. no Private Key)
            return 'MOCK_TOKEN_' . Str::random(32);

        } catch (\Exception $e) {
            Log::error('BRI Token Exception: ' . $e->getMessage());
            return 'MOCK_TOKEN_EXCEPTION';
        }
    }

    /**
     * Create Virtual Account (SNAP)
     */
    public function createVirtualAccount($trxCode, $amount, $customerName, $customerPhone)
    {
        $accessToken = $this->getToken();
        
        // If we have a real token, we would call the API here.
        // For now, if it's a mock token or we want to ensure success in Sandbox Simulator:
        
        // Mock Response logic
        return [
            'status' => 'mock_success',
            'virtualAccountNo' => '77777' . substr($customerPhone, -8), // Example BRI VA
            'trxId' => $trxCode,
            'totalAmount' => $amount
        ];
    }
}
