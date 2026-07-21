<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BackgroundSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('backgrounds')->insertOrIgnore($this->backgroundRows());
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function backgroundRows(): array
    {
        $now = now();

        return array_map(
            fn (array $background): array => [
                'key' => $background['key'],
                'title' => $background['title'],
                'description' => $background['description'],
                'image' => $background['image'],
                'overlay' => $background['overlay'],
                'size' => $background['size'],
                'position' => json_encode($background['position'], JSON_THROW_ON_ERROR),
                'variants' => isset($background['variants'])
                    ? json_encode($background['variants'], JSON_THROW_ON_ERROR)
                    : null,
                'schedule' => null,
                'enabled' => true,
                'weight' => 1,
                'pinned' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            $this->backgrounds(),
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function backgrounds(): array
    {
        return [
            [
                'key' => 'dino_toolbox',
                'title' => 'Dino Toolbox Mural',
                'image' => 'img/bg/dino_toolbox.jpg',
                'description' => 'A picture of a local mural I took in town, I always thought his hat was very good.',
                'position' => ['x' => '50.4%', 'y' => '101.1%'],
                'overlay' => 0.66,
                'size' => '186% auto',
                'variants' => [
                    'base' => [
                        'position' => ['x' => '50.4%', 'y' => '101.1%'],
                        'size' => '186% auto',
                    ],
                    'sm' => [
                        'position' => ['x' => '30.9%', 'y' => '28.8%'],
                        'size' => '96% auto',
                    ],
                    'lg' => [
                        'position' => ['x' => '-5.8%', 'y' => '16.9%'],
                        'size' => '114% auto',
                    ],
                ],
            ],
            [
                'key' => 'abstract_mural',
                'title' => 'Abstract Mural',
                'image' => 'img/bg/bg_abstract_mural.jpg',
                'description' => 'Another cool local mural',
                'position' => ['x' => '50%', 'y' => '50%'],
                'overlay' => 0.25,
                'size' => 'cover',
                'variants' => [
                    'base' => [
                        'position' => ['x' => '50%', 'y' => '50%'],
                        'size' => 'cover',
                    ],
                    'lg' => [
                        'position' => ['x' => '10%'],
                        'size' => '100%',
                    ],
                ],
            ],
            [
                'key' => 'st_pete',
                'title' => 'St. Petersburg Sign',
                'image' => 'img/bg/bg_est_st_pete.jpg',
                'description' => 'doesn\'t get much more local than this! RIP civeche!',
                'position' => ['x' => '94.6%', 'y' => '43.9%'],
                'overlay' => 0.55,
                'size' => '100% auto',
                'variants' => [
                    'base' => [
                        'position' => ['x' => '94.6%', 'y' => '43.9%'],
                        'size' => '100% auto',
                    ],
                    'sm' => [
                        'position' => ['x' => '1.8%'],
                        'size' => '100% auto',
                    ],
                    'lg' => [
                        'position' => ['x' => '26.6%', 'y' => '56.8%'],
                        'size' => '62.5% auto',
                    ],
                ],
            ],
            [
                'key' => 'lake_eola',
                'title' => 'Lake Eola',
                'image' => 'img/bg/bg_lake_eola.jpg',
                'description' => 'A cool bird I caught drying out his wings in orlando at lake eola park',
                'position' => ['x' => '33.1%', 'y' => '-1.4%'],
                'overlay' => 0.64,
                'size' => '242.5% auto',
                'variants' => [
                    'base' => [
                        'position' => ['x' => '33.1%', 'y' => '-1.4%'],
                        'size' => '242.5% auto',
                    ],
                    'sm' => [
                        'size' => '102% auto',
                    ],
                    'lg' => [
                        'position' => ['x' => '41.7%', 'y' => '48.2%'],
                    ],
                ],
            ],
            [
                'key' => 'sea_turtle',
                'title' => 'Sea Turtle',
                'image' => 'img/bg/bg_sea_turtle.jpg',
                'description' => 'I thought the detail you could see was pretty cool in this one',
                'position' => ['x' => '23.4%', 'y' => '35.3%'],
                'overlay' => 0.70,
                'size' => '269.5% auto',
                'variants' => [
                    'base' => [
                        'position' => ['x' => '23.4%', 'y' => '35.3%'],
                        'size' => '269.5% auto',
                    ],
                    'sm' => [
                        'position' => ['y' => '42.8%'],
                        'size' => '113% auto',
                    ],
                    'lg' => [
                        'position' => ['y' => '20.1%'],
                        'size' => '100% auto',
                    ],
                ],
            ],
            [
                'key' => 'pier_night',
                'title' => 'St. Petersburg Pier',
                'image' => 'img/bg/pier_night.jpg',
                'description' => 'Night shot of the St. Petersburg Pier.',
                'position' => ['x' => '50%', 'y' => '106.3%'],
                'overlay' => 0.6,
                'size' => 'cover',
            ],
            [
                'key' => 'clownfish',
                'title' => 'Clownfish',
                'image' => 'img/bg/bg_clownfish.jpg',
                'description' => 'A little clownfish peeking out from some aquarium anemones, I liked how much color was hiding in this one.',
                'position' => ['x' => '32%', 'y' => '54.7%'],
                'overlay' => 0.33,
                'size' => '251.5% auto',
                'variants' => [
                    'base' => [
                        'position' => ['x' => '32%', 'y' => '54.7%'],
                        'size' => '251.5% auto',
                    ],
                    'sm' => [
                        'position' => ['x' => '36.3%', 'y' => '71.9%'],
                        'size' => '174.5% auto',
                    ],
                    'lg' => [
                        'position' => ['x' => '2.9%', 'y' => '60.1%'],
                        'size' => '100% auto',
                    ],
                ],
            ],
            [
                'key' => 'dtsp_bokeh',
                'title' => 'Downtown St. Petersburg',
                'image' => 'img/bg/bg_dtsp_bokeh.jpg',
                'description' => 'A blurry downtown St. Pete shot at night, sometimes the out of focus ones have the best mood.',
                'position' => ['x' => '71.9%', 'y' => '0'],
                'overlay' => 0,
                'size' => '300% auto',
                'variants' => [
                    'base' => [
                        'position' => ['x' => '71.9%', 'y' => '0'],
                        'size' => '300% auto',
                    ],
                    'sm' => [
                        'position' => ['y' => '16.9%'],
                        'size' => '106% auto',
                    ],
                    'lg' => [
                        'position' => ['x' => '96.8%', 'y' => '40.6%'],
                        'size' => '114% auto',
                    ],
                ],
            ],
            [
                'key' => 'jellyfish_neon',
                'title' => 'Jellyfish Neon',
                'image' => 'img/bg/bg_jellyfish_neon.jpg',
                'description' => 'Some very glowy jellyfish that looked almost fake in person, the little green tips were too cool not to photograph.',
                'position' => ['x' => '28.8%', 'y' => '97.8%'],
                'overlay' => 0.66,
                'size' => '164.5% auto',
                'variants' => [
                    'base' => [
                        'position' => ['x' => '28.8%', 'y' => '97.8%'],
                        'size' => '164.5% auto',
                    ],
                    'sm' => [
                        'position' => ['x' => '20.1%', 'y' => '67.6%'],
                        'size' => '220% auto',
                    ],
                    'lg' => [
                        'position' => ['y' => '59%'],
                        'size' => '112% auto',
                    ],
                ],
            ],
        ];
    }
}
