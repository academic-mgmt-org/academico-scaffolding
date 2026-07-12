<?php

return [
    'host' => env('GATEWAY_GRPC_HOST', 'academia-dev.eastus2.cloudapp.azure.com:50050'),
    'timeout_ms' => (int) env('GATEWAY_GRPC_TIMEOUT_MS', 10_000),
    'max_notifications' => (int) env('GATEWAY_MAX_NOTIFICATIONS', 500),
    'smoke' => [
        'username' => env('GATEWAY_SMOKE_USERNAME'),
        'password' => env('GATEWAY_SMOKE_PASSWORD'),
    ],
];
