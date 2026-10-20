<?php

return [
    'client_id' => env('EVERIFY_CLIENT_ID'),
    'client_secret' => env('EVERIFY_CLIENT_SECRET'),
    'auth_url' => env('EVERIFY_AUTH_URL'),
    'query_url' => env('EVERIFY_QUERY_URL'),
    'qr_url' => env('EVERIFY_QR_URL'),
    'api_keys' => env('EVERIFY_API_KEYS', ''),
    'allowed_ips' => array_filter(explode(',', env('EVERIFY_ALLOWED_IPS', ''))),
];