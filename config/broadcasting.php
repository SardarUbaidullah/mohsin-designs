<?php

return [
    'default' => env('BROADCAST_DRIVER', 'pusher'),

    'connections' => [
        'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER', 'ap2'),
                'host' => 'api-'.env('PUSHER_APP_CLUSTER', 'ap2').'.pusher.com',
                'port' => 443,
                'scheme' => 'https',
                'useTLS' => true,
                'encrypted' => true,
                'forceTLS' => true,
                // ⭐⭐ CRITICAL: Add these options ⭐⭐
                'wsHost' => 'ws-'.env('PUSHER_APP_CLUSTER', 'ap2').'.pusher.com',
                'wsPort' => 443,
                'wssPort' => 443,
                'disableStats' => true,
                'enabledTransports' => ['ws', 'wss'],
            ],
        ],
    ],
];
