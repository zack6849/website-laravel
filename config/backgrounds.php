<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Home Banner Selection Cache
    |--------------------------------------------------------------------------
    |
    | How long (in seconds) the selected home banner background is cached for.
    | The cache is busted immediately whenever a Background model is saved or
    | deleted, so this only bounds how long a weighted-random pick "sticks"
    | between admin changes.
    |
    */

    'cache' => [
        'ttl_seconds' => (int) env('BACKGROUND_CACHE_TTL_SECONDS', 3600),
    ],

];
