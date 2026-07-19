<?php

declare(strict_types=1);

return [
    'cache' => [
        'key' => 'home-background',
        'ttl_seconds' => 3600,
    ],

    'defaults' => [
        'enabled' => true,
        'position' => [
            'x' => '50%',
            'y' => '50%',
        ],
        'overlay' => 0.68,
        'size' => 'cover',
    ],

    'items' => [
        'dino_toolbox' => [
            'title' => 'Dino Toolbox Mural',
            'image' => 'img/bg/dino_toolbox.jpg',
            'description' => 'A picture of a local mural I took in town, I always thought his hat was very good.',
            'position' => [
                'x' => '44%',
                'y' => '57%',
            ],
            'overlay' => 0.66,
            'size' => 'cover',
        ],
        'abstract_mural' => [
            'title' => 'Abstract Mural',
            'image' => 'img/bg/bg_abstract_mural.jpg',
            'description' => 'Another cool local mural',
            'position' => [
                'x' => '50%',
                'y' => '50%',
            ],
            'overlay' => 0.25,
            'size' => 'cover',
        ],
        'st_pete' => [
            'title' => 'St. Petersburg Sign',
            'image' => 'img/bg/bg_est_st_pete.jpg',
            'description' => 'doesn\'t get much more local than this! RIP civeche!',
            'position' => [
                'x' => '50%',
                'y' => '69%',
            ],
            'overlay' => 0.72,
            'size' => '100% auto',
        ],
        'lake_eola' => [
            'title' => 'Lake Eola',
            'image' => 'img/bg/bg_lake_eola.jpg',
            'description' => 'A cool bird I caught drying out his wings in orlando at lake eola park',
            'position' => [
                'x' => '50%',
                'y' => '112%',
            ],
            'overlay' => 0.64,
            'size' => '100% auto',
        ],
        'sea_turtle' => [
            'title' => 'Sea Turtle',
            'image' => 'img/bg/bg_sea_turtle.jpg',
            'description' => 'I thought the detail you could see was pretty cool in this one',
            'position' => [
                'x' => '52%',
                'y' => '177%',
            ],
            'overlay' => 0.70,
            'size' => 'cover',
        ],
        'pier_night' => [
            'title' => 'St. Petersburg Pier',
            'image' => 'img/bg/pier_night.jpg',
            'description' => 'Night shot of the St. Petersburg Pier.',
            'position' => [
                'x' => '50%',
                'y' => '106.3%',
            ],
            'overlay' => 0.6,
            'size' => 'cover',
        ],
        'clownfish' => [
            'title' => 'Clownfish',
            'image' => 'img/bg/bg_clownfish.jpg',
            'description' => 'A little clownfish peeking out from some aquarium anemones, I liked how much color was hiding in this one.',
            'position' => [
                'x' => '50%',
                'y' => '143%',
            ],
            'overlay' => 0.33,
            'size' => '100% auto',
        ],
        'dtsp_bokeh' => [
            'title' => 'Downtown St. Petersburg',
            'image' => 'img/bg/bg_dtsp_bokeh.jpg',
            'description' => 'A blurry downtown St. Pete shot at night, sometimes the out of focus ones have the best mood.',
            'position' => [
                'x' => '50%',
                'y' => '203%',
            ],
            'overlay' => 0,
            'size' => 'cover',
        ],
        'jellyfish_neon' => [
            'title' => 'Jellyfish Neon',
            'image' => 'img/bg/bg_jellyfish_neon.jpg',
            'description' => 'Some very glowy jellyfish that looked almost fake in person, the little green tips were too cool not to photograph.',
            'position' => [
                'x' => '0%',
                'y' => '87%',
            ],
            'overlay' => 0.66,
            'size' => '100% auto',
        ],
    ],
];
