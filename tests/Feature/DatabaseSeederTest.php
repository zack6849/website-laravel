<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Support\BackgroundCssValue;
use Database\Seeders\BackgroundSeeder;
use Database\Seeders\DatabaseSeeder;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    public function testInitialBackgroundRowsAreCreatedByMigrations(): void
    {
        $this->assertDatabaseHas('backgrounds', [
            'key' => 'dino_toolbox',
            'title' => 'Dino Toolbox Mural',
            'image' => 'img/bg/dino_toolbox.jpg',
        ]);
        $this->assertDatabaseHas('backgrounds', [
            'key' => 'pier_night',
            'title' => 'St. Petersburg Pier',
            'image' => 'img/bg/pier_night.jpg',
        ]);
    }

    public function testDatabaseSeederCreatesAdminUser(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::where('email', 'zack@zcraig.me')->firstOrFail();

        $this->assertTrue($user->isAdmin());
        $this->assertTrue($user->horizon_access);
    }

    public function testSeededBackgroundCssValuesAreValid(): void
    {
        foreach ((new BackgroundSeeder())->backgrounds() as $background) {
            $this->assertTrue(
                BackgroundCssValue::isSize($background['size']),
                "Invalid size for [{$background['key']}].",
            );

            foreach ($background['position'] as $axis => $value) {
                $this->assertTrue(
                    BackgroundCssValue::isPositionToken($value),
                    "Invalid {$axis} position for [{$background['key']}].",
                );
            }

            foreach (($background['variants'] ?? []) as $breakpoint => $variant) {
                if (isset($variant['size'])) {
                    $this->assertTrue(
                        BackgroundCssValue::isSize($variant['size']),
                        "Invalid {$breakpoint} size for [{$background['key']}].",
                    );
                }

                foreach (($variant['position'] ?? []) as $axis => $value) {
                    $this->assertTrue(
                        BackgroundCssValue::isPositionToken($value),
                        "Invalid {$breakpoint} {$axis} position for [{$background['key']}].",
                    );
                }
            }
        }
    }
}
