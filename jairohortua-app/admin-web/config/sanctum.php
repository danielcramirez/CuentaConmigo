<?php

return [
    'sanctum' => [
        'expiration' => env('SANCTUM_TOKEN_EXPIRATION_MINUTES', 1440),
        'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', 'localhost,localhost:8000')),
    ],
];
