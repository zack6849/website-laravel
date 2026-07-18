<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\HamAlertSpot;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HamAlertSpotsTest extends TestCase
{
    #[Test]
    public function onlyReturnsTheMostRecentSpotPerCallsign(): void
    {
        HamAlertSpot::factory()->create([
            'callsign' => 'K1ABC',
            'created_at' => now()->subHours(2),
        ]);
        $latest = HamAlertSpot::factory()->create([
            'callsign' => 'K1ABC',
            'created_at' => now()->subHour(),
        ]);
        $other = HamAlertSpot::factory()->create([
            'callsign' => 'W9XYZ',
            'created_at' => now()->subMinutes(30),
        ]);
        // outside the 1-day window, should be excluded entirely
        HamAlertSpot::factory()->create([
            'callsign' => 'N0OLD',
            'created_at' => now()->subDays(2),
        ]);

        $response = $this->getJson('/api/radio/spots');

        $response->assertOk();
        $callsigns = collect($response->json())->pluck('latest_spot.callsign');
        $this->assertCount(2, $callsigns);
        $this->assertContains('K1ABC', $callsigns);
        $this->assertContains('W9XYZ', $callsigns);
        $this->assertNotContains('N0OLD', $callsigns);

        $k1abcSpot = collect($response->json())->firstWhere('latest_spot.callsign', 'K1ABC');
        $this->assertEquals($latest->id, $k1abcSpot['latest_spot']['id']);
    }
}
