<?php

return [
    'client_id' => env('BRI_CLIENT_ID'),
    'client_secret' => env('BRI_CLIENT_SECRET'),
    'partner_id' => env('BRI_PARTNER_ID', 'YourPartnerID'), // Required for SNAP
    'private_key_path' => env('BRI_PRIVATE_KEY_PATH', storage_path('app/keys/private.key')),
    'public_key_path' => env('BRI_PUBLIC_KEY_PATH', storage_path('app/keys/public.pem')), // BRI Public Key
    'base_url' => env('BRI_BASE_URL', 'https://sandbox.partner.api.bri.co.id'),
];
