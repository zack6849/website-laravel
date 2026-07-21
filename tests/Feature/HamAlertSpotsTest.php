<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\HamAlertSpot;
use App\Models\User;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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

    #[Test]
    public function invalidSpotTimesAreRejected(): void
    {
        Bus::fake();
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->postJson('/api/radio/spots', [
                'callsign' => 'K1ABC',
                'frequency' => '14.074',
                'band' => '20m',
                'modeDetail' => 'FT8',
                'time' => 'not-a-date',
                'spotterEntity' => 'USA',
                'spotter' => 'W9XYZ',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['time']);
        $this->assertDatabaseCount('ham_alert_spots', 0);
    }

    #[Test]
    public function hashedApiTokensAuthenticateApiRequests(): void
    {
        Bus::fake();
        $plainTextToken = Str::random(60);
        User::factory()->create(['api_token' => hash('sha256', $plainTextToken)]);

        $response = $this
            ->withHeader('Authorization', 'Bearer ' . $plainTextToken)
            ->postJson('/api/radio/spots', [
                'callsign' => 'K1ABC',
                'frequency' => '14.074',
                'band' => '20m',
                'modeDetail' => 'FT8',
                'time' => now()->toIso8601String(),
                'spotterEntity' => 'USA',
                'spotter' => 'W9XYZ',
            ]);

        $response->assertSuccessful();
        $this->assertDatabaseHas('ham_alert_spots', ['callsign' => 'K1ABC']);
    }

    #[Test]
    public function legacyPlaintextApiTokensAuthenticateDuringHashingRollout(): void
    {
        Bus::fake();
        $plainTextToken = Str::random(60);
        $user = User::factory()->create();

        DB::table('users')
            ->where('id', $user->id)
            ->update(['api_token' => $plainTextToken]);

        $response = $this
            ->withHeader('Authorization', 'Bearer ' . $plainTextToken)
            ->postJson('/api/radio/spots', [
                'callsign' => 'K1ABC',
                'frequency' => '14.074',
                'band' => '20m',
                'modeDetail' => 'FT8',
                'time' => now()->toIso8601String(),
                'spotterEntity' => 'USA',
                'spotter' => 'W9XYZ',
            ]);

        $response->assertSuccessful();
        $this->assertDatabaseHas('ham_alert_spots', ['callsign' => 'K1ABC']);
    }

    #[Test]
    public function newApiTokensAreStoredHashed(): void
    {
        $plainTextToken = Str::random(60);
        $user = User::factory()->create(['api_token' => $plainTextToken]);

        $this->assertSame(hash('sha256', $plainTextToken), $user->fresh()->api_token);
    }

    #[Test]
    public function apiTokenHashingMigrationHashesPlaintextRows(): void
    {
        $plainTextToken = Str::random(60);
        $user = User::factory()->create();

        DB::table('users')
            ->where('id', $user->id)
            ->update(['api_token' => $plainTextToken]);

        $this->rerunApiTokenHashingMigration();

        $this->assertSame(hash('sha256', $plainTextToken), $user->fresh()->api_token);
    }

    #[Test]
    public function apiTokenHashingMigrationDoesNotDoubleHashExistingHashes(): void
    {
        $plainTextToken = Str::random(60);
        $hashedToken = hash('sha256', $plainTextToken);
        $user = User::factory()->create(['api_token' => $hashedToken]);

        $this->rerunApiTokenHashingMigration();

        $this->assertSame($hashedToken, $user->fresh()->api_token);
    }

    private function rerunApiTokenHashingMigration(): void
    {
        $migration = include database_path('migrations/2026_07_19_000003_hash_existing_api_tokens.php');

        $migration->up();
    }
}
