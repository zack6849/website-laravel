<?php

return [
    'sid' => env('TWILIO_SID', 'not set'),
    'token' => env('TWILIO_TOKEN', 'not set'),
    'number' => env('TWILIO_NUMBER', 'not set'),
    'public_rate_limit' => env('TWILIO_PUBLIC_RATE_LIMIT', 3),
    'public_decay_rate' => env('TWILIO_PUBLIC_DECAY_RATE', 86400),
    // Twilio's SDK defaults to a 60s cURL timeout with no override elsewhere;
    // an uncached lookup chains through Twilio's Lookup API + the Ekata
    // reverse-phone add-on, so bound it to something a web request can
    // actually wait on instead of hanging for up to a minute.
    'lookup_timeout_seconds' => env('TWILIO_LOOKUP_TIMEOUT_SECONDS', 20),
    'lookup_connect_timeout_seconds' => env('TWILIO_LOOKUP_CONNECT_TIMEOUT_SECONDS', 5),
];

