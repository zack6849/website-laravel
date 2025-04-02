<?php

return [
    'sid' => env('TWILIO_SID', ''),
    'token' => env('TWILIO_TOKEN', ''),
    'number' => env('TWILIO_NUMBER', ''),
    'public' => [
        'rate_limit' => env('TWILIO_PUBLIC_RATE_LIMIT', 3),
        'decay_rate' => env('TWILIO_PUBLIC_DECAY_RATE', 86400),
    ],
    'authenticated' => [
        'rate_limit' => env('TWILIO_AUTHENTICATED_RATE_LIMIT', 30),
        'decay_rate' => env('TWILIO_AUTHENTICATED_DECAY_RATE', 86400),
    ],
];

