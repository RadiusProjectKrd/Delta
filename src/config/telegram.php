<?php
return [
    'minecraft' => [
        'token' => env('TELEGRAM_MINECRAFT_TOKEN'),
        'broadcast_channel' => env('TELEGRAM_MINECRAFT_BROADCAST_CHANNEL'),
        'broadcast_thread' => env('TELEGRAM_MINECRAFT_BROADCAST_THREAD')
    ],
    'security' => [
        'token' => env('TELEGRAM_SECURITY_TOKEN'),
        'broadcast_channel' => env('TELEGRAM_SECURITY_BROADCAST_CHANNEL'),
        'broadcast_thread' => env('TELEGRAM_SECURITY_BROADCAST_THREAD')
    ]
];
