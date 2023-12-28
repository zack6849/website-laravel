<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => \App\Models\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook' => [
            'secret' => env('STRIPE_WEBHOOK_SECRET'),
            'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
        ],
    ],

    'flickr' => [
        'api_key' => env('FLICKR_API_KEY'),
        'gallery_uid' => env('FLICKR_GALLERY_USER')
    ],

    'mapbox' => [
        'token' => env('MAPBOX_API_TOKEN')
    ],

    'qrz' => [
        'key' => env('QRZ_API_KEY'),
    ],

    'discord' => [
        'webhook_uri' => env('DISCORD_WEBHOOK_URI')
    ],
    'petco' => [
        'application_id' => 'z1dq4rokCsjt7foJ4C6uMzN4YhSFgsSzbAQlBY2y',
        'session_token' => env('PETCO_SESSION_TOKEN'),
        'location' => env('PECO_LOCATION'),
        'search_radius' => env('PETCO_SEARCH_RADIUS', 100)
    ],
    'geoapify' => [
        'key' => env('GEOAPIFY_API_KEY')
    ]
];
