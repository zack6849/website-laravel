<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Jobs\QRZLogbookImport;
use App\Models\Callsign;
use App\Models\LogbookEntry;
use App\Services\ParksOnTheAirService;
use App\Services\QRZLogbookService;
use Illuminate\Support\Facades\Cache;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QRZLogbookImportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        //the 'array' cache driver persists for the whole PHPUnit process, not per-test;
        //without this, a 'logbook' key warmed by an earlier test would make
        //QRZLogbookService::getLogbookEntries() never get called here
        Cache::forget('logbook');
    }

    #[Test]
    public function rollsBackAllChangesWhenARecordFailsToImport(): void
    {
        $callsign = Callsign::factory()->create(['name' => 'N0CALL', 'country' => 'USA']);
        $existing = LogbookEntry::factory()->create([
            'from_callsign' => $callsign->id,
            'to_callsign' => $callsign->id,
            'frequency' => 14.250,
            'band' => '20m',
            'mode' => 'SSB',
            'from_grid' => 'FN20',
            'from_coordinates' => '40.00000,-75.00000',
            'from_latitude' => '40.00000',
            'from_longitude' => '-75.00000',
            'to_latitude' => '41.00000',
            'to_longitude' => '-72.00000',
        ]);

        $goodRecord = $this->adifRecord(['COMMENT' => 'Nice contact']);
        // this record references a POTA park, which will trigger the mocked failure below
        $badRecord = $this->adifRecord(['COMMENT' => 'Activated K-1234', 'CALL' => 'K2ABC']);

        $this->mock(QRZLogbookService::class, function (MockInterface $mock) use ($goodRecord, $badRecord) {
            $mock->shouldReceive('getLogbookEntries')->once()->andReturn([$goodRecord, $badRecord]);
        });
        $this->mock(ParksOnTheAirService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getParkInfo')
                ->with('K-1234')
                ->andThrow(new \RuntimeException('POTA API unavailable'));
        });

        try {
            dispatch_sync(resolve(QRZLogbookImport::class));
            $this->fail('Expected the import to throw when a record fails.');
        } catch (\RuntimeException $e) {
            $this->assertEquals('POTA API unavailable', $e->getMessage());
        }

        // the pre-existing entry must survive, and neither of the two imported records
        // (including the one that didn't fail) should have been persisted
        $this->assertDatabaseCount('logbook_entries', 1);
        $this->assertDatabaseHas('logbook_entries', ['id' => $existing->id]);
    }

    private function adifRecord(array $overrides = []): array
    {
        return array_merge([
            'CALL' => 'W1AW',
            'STATION_CALLSIGN' => 'N0CALL',
            'COUNTRY' => 'USA',
            'MY_COUNTRY' => 'USA',
            'MY_LAT' => 'N40 00.000',
            'MY_LON' => 'W75 00.000',
            'LAT' => 'N41 00.000',
            'LON' => 'W72 00.000',
            'FREQ' => '14.250',
            'BAND' => '20m',
            'MODE' => 'SSB',
            'RST_SENT' => '59',
            'RST_RCVD' => '59',
            'MY_GRIDSQUARE' => 'FN20',
            'GRIDSQUARE' => 'FN31',
            'DISTANCE' => '100',
            'COMMENT' => '',
            'QSO_DATE' => '20240101',
            'TIME_ON' => '1200',
        ], $overrides);
    }
}
