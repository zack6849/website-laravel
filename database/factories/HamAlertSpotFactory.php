<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\HamAlertSpot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HamAlertSpot>
 */
class HamAlertSpotFactory extends Factory
{
    protected $model = HamAlertSpot::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'callsign' => strtoupper($this->faker->bothify('#?#??')),
            'spotter_callsign' => strtoupper($this->faker->bothify('#?#??')),
            'frequency' => $this->faker->randomFloat(3, 1.8, 148),
            'band' => '20m',
            'spotter_entity' => 'USA',
            'mode' => 'SSB',
            'created_at' => now(),
        ];
    }
}
