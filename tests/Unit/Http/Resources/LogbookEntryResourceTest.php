<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\LogbookEntryResource;
use App\Models\POTAPark;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LogbookEntryResourceTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    #[Test]
    public function includesRecencyPropertiesForContactTimestamp(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-07-21 12:00:00'));
        $entry = $this->logbookEntry([
            'created_at' => Carbon::parse('2026-07-11 12:00:00'),
        ]);

        $resource = (new LogbookEntryResource($entry))->toArray(new Request());

        $this->assertSame('2026-07-11 12:00:00', $resource['qso_date']);
        $this->assertSame(10, $resource['age_days']);
        $this->assertSame(0.973, $resource['recency_score']);
        $this->assertSame('pin', $resource['icon']);
        $this->assertSame(0.025, $resource['icon_size']);
        $this->assertSame(100, $resource['distance']);
        $this->assertFalse($resource['distance_estimated']);
        $this->assertSame('United States', $resource['to_country']);
        $this->assertSame('United States', $resource['display_location']);
        $this->assertIsInt($resource['bearing_degrees']);
        $this->assertNotEmpty($resource['bearing_cardinal']);
        $this->assertArrayNotHasKey('from_callsign', $resource);
        $this->assertArrayNotHasKey('from_grid', $resource);
        $this->assertArrayNotHasKey('from_coordinates', $resource);
        $this->assertArrayNotHasKey('from_latitude', $resource);
        $this->assertArrayNotHasKey('from_longitude', $resource);
        $this->assertArrayNotHasKey('qrz_logid', $resource);
        $this->assertArrayNotHasKey('entry_key', $resource);
        $this->assertArrayNotHasKey('hidden_from_public', $resource);
    }

    #[Test]
    public function prefersExplicitQsoDateAndKeepsPotaTreeStyling(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-07-21 12:00:00'));
        $entry = $this->logbookEntry([
            'category' => 'POTA',
            'created_at' => Carbon::parse('2025-07-21 12:00:00'),
            'qso_date' => '2026-07-16 12:00:00',
        ]);

        $resource = (new LogbookEntryResource($entry))->toArray(new Request());

        $this->assertSame('2026-07-16 12:00:00', $resource['qso_date']);
        $this->assertSame(5, $resource['age_days']);
        $this->assertSame(0.986, $resource['recency_score']);
        $this->assertSame('tree', $resource['icon']);
        $this->assertSame(0.25, $resource['icon_size']);
    }

    #[Test]
    public function prefersCityAndStateForTheDisplayLocation(): void
    {
        $entry = $this->logbookEntry([
            'to_city' => 'Anchorage',
            'to_state' => 'AK',
            'to_county' => 'Anchorage',
        ]);

        $resource = (new LogbookEntryResource($entry))->toArray(new Request());

        $this->assertSame('Anchorage', $resource['to_city']);
        $this->assertSame('AK', $resource['to_state']);
        $this->assertSame('Anchorage', $resource['to_county']);
        $this->assertSame('Anchorage, AK', $resource['display_location']);
    }

    #[Test]
    public function usesPotaParkLocationWhenCityAndStateAreUnavailable(): void
    {
        $entry = $this->logbookEntry([
            'category' => 'POTA',
            'park' => new POTAPark([
                'name' => 'Historic Halifax',
                'reference' => 'US-6843',
                'location' => 'North Carolina',
            ]),
        ]);

        $resource = (new LogbookEntryResource($entry))->toArray(new Request());

        $this->assertSame('Historic Halifax', $resource['park_name']);
        $this->assertSame('US-6843', $resource['park_reference']);
        $this->assertSame('North Carolina', $resource['park_location']);
        $this->assertSame('Historic Halifax, North Carolina', $resource['display_location']);
        $this->assertArrayNotHasKey('park', $resource);
    }

    #[Test]
    public function includesBearingDirectionWithoutExposingStationCoordinates(): void
    {
        $entry = $this->logbookEntry([
            'from_latitude' => '0.00000',
            'from_longitude' => '0.00000',
            'to_latitude' => '0.00000',
            'to_longitude' => '90.00000',
            'distance' => 1500,
        ]);

        $resource = (new LogbookEntryResource($entry))->toArray(new Request());

        $this->assertSame(90, $resource['bearing_degrees']);
        $this->assertSame('E', $resource['bearing_cardinal']);
        $this->assertSame(1500, $resource['distance']);
        $this->assertFalse($resource['distance_estimated']);
        $this->assertArrayNotHasKey('from_latitude', $resource);
        $this->assertArrayNotHasKey('from_longitude', $resource);
    }

    #[Test]
    public function derivesDistanceWhenTheStoredDistanceIsMissing(): void
    {
        $entry = $this->logbookEntry([
            'from_latitude' => '0.00000',
            'from_longitude' => '0.00000',
            'to_latitude' => '0.00000',
            'to_longitude' => '1.00000',
            'distance' => null,
        ]);

        $resource = (new LogbookEntryResource($entry))->toArray(new Request());

        $this->assertSame(69, $resource['distance']);
        $this->assertTrue($resource['distance_estimated']);
        $this->assertSame(90, $resource['bearing_degrees']);
        $this->assertSame('E', $resource['bearing_cardinal']);
    }

    #[Test]
    public function fallsBackToTheConfiguredOriginWhenStationCoordinatesAreMissing(): void
    {
        config([
            'radio.origin.latitude' => 0,
            'radio.origin.longitude' => 0,
        ]);

        $entry = $this->logbookEntry([
            'from_latitude' => null,
            'from_longitude' => null,
            'to_latitude' => '0.00000',
            'to_longitude' => '1.00000',
            'distance' => null,
        ]);

        $resource = (new LogbookEntryResource($entry))->toArray(new Request());

        $this->assertSame(69, $resource['distance']);
        $this->assertTrue($resource['distance_estimated']);
        $this->assertSame(90, $resource['bearing_degrees']);
        $this->assertSame('E', $resource['bearing_cardinal']);
        $this->assertArrayNotHasKey('from_latitude', $resource);
        $this->assertArrayNotHasKey('from_longitude', $resource);
    }

    #[Test]
    public function omitsBearingDirectionWhenDestinationCoordinatesAreInvalid(): void
    {
        $entry = $this->logbookEntry([
            'from_latitude' => '40.00000',
            'from_longitude' => '-75.00000',
            'to_latitude' => 'not-a-coordinate',
            'to_longitude' => '-72.00000',
            'distance' => null,
        ]);

        $resource = (new LogbookEntryResource($entry))->toArray(new Request());

        $this->assertNull($resource['distance']);
        $this->assertFalse($resource['distance_estimated']);
        $this->assertNull($resource['bearing_degrees']);
        $this->assertNull($resource['bearing_cardinal']);
    }

    private function logbookEntry(array $attributes = []): object
    {
        $attributes = array_merge([
            'frequency' => 14.250,
            'band' => '20M',
            'mode' => 'SSB',
            'rst_sent' => '59',
            'rst_received' => '59',
            'from_grid' => 'FN20',
            'from_coordinates' => '40.00000,-75.00000',
            'from_latitude' => '40.00000',
            'from_longitude' => '-75.00000',
            'to_grid' => 'FN31',
            'to_coordinates' => '41.00000,-72.00000',
            'to_latitude' => '41.00000',
            'to_longitude' => '-72.00000',
            'distance' => 100,
            'comments' => 'Nice contact',
            'category' => 'default',
        ], $attributes);

        return new class($attributes) {
            public object $station;
            public object $callee;
            public mixed $created_at;
            public string $category;

            public function __construct(private array $attributes)
            {
                $this->station = (object) ['name' => 'N0CALL', 'country' => 'United States'];
                $this->callee = (object) ['name' => 'W1AW', 'country' => 'United States'];
                $this->created_at = $attributes['created_at'] ?? null;
                $this->category = $attributes['category'] ?? 'default';
            }

            public function getAttribute(string $key): mixed
            {
                return $this->attributes[$key] ?? null;
            }

            public function toArray(): array
            {
                return array_merge($this->attributes, [
                    'station' => ['name' => $this->station->name],
                    'callee' => ['name' => $this->callee->name],
                ]);
            }
        };
    }
}
