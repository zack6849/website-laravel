<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\POTAPark;
use App\Services\ParksOnTheAirService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\ServiceTest;

class ParksOnTheAirServiceTest extends TestCase
{
    use ServiceTest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setService(ParksOnTheAirService::class);
    }

    private function fakeParkResponse(string $reference): array
    {
        return [
            'parkId' => 3061,
            'reference' => $reference,
            'name' => 'Walter Umphrey',
            'latitude' => 29.763,
            'longitude' => -93.899,
            'grid4' => 'EL39',
            'grid6' => 'EL39bs',
            'parktypeId' => 101,
            'parktypeDesc' => 'State Park',
            'active' => 1,
            'parkComments' => null,
            'locationName' => 'Texas',
            'firstActivationDate' => '2018-02-08',
        ];
    }

    #[Test]
    public function legacyPrefixReferencesResolveAgainstTheCanonicalPrefixOnly(): void
    {
        Http::fake([
            'api.pota.app/park/K-3061' => Http::response(null),
            'api.pota.app/park/US-3061' => Http::response($this->fakeParkResponse('US-3061')),
        ]);

        $park = $this->service->getParkInfo('K-3061');

        $this->assertInstanceOf(POTAPark::class, $park);
        $this->assertSame('US-3061', $park->reference);
        // the raw "K-3061" form is known to never resolve, so it should never be requested
        Http::assertNotSent(fn ($request) => str_contains($request->url(), '/park/K-3061'));
        Http::assertSentCount(1);
    }

    #[Test]
    public function referencesFromEntitiesThatWereRenumberedUseTheExplicitCrosswalkNotTheGenericPrefixSwap(): void
    {
        // K-0066 is Virgin Islands park VI-0001, not US-0066 - Puerto Rico,
        // Guam, and a handful of other former-"K" territories were split into
        // their own entity and renumbered from scratch, not just re-prefixed.
        Http::fake([
            'api.pota.app/park/VI-0001' => Http::response($this->fakeParkResponse('VI-0001')),
        ]);

        $park = $this->service->getParkInfo('K-0066');

        $this->assertInstanceOf(POTAPark::class, $park);
        $this->assertSame('VI-0001', $park->reference);
        Http::assertNotSent(fn ($request) => str_contains($request->url(), '/park/US-0066'));
        Http::assertSentCount(1);
    }

    #[Test]
    public function aSubsequentLookupForTheSameLegacyReferenceHitsTheLocalCacheOnly(): void
    {
        Http::fake([
            'api.pota.app/park/US-3061' => Http::response($this->fakeParkResponse('US-3061')),
        ]);

        $this->service->getParkInfo('K-3061');
        $this->reloadService();
        $this->service->getParkInfo('K-3061');

        Http::assertSentCount(1);
    }

    #[Test]
    public function unresolvableReferencesAreNegativelyCachedInsteadOfRetriedEveryImport(): void
    {
        Http::fake([
            'api.pota.app/park/BOGUS-1' => Http::response(null),
        ]);

        $first = $this->service->getParkInfo('BOGUS-1');
        $this->reloadService();
        $second = $this->service->getParkInfo('BOGUS-1');

        $this->assertFalse($first);
        $this->assertFalse($second);
        Http::assertSentCount(1);
    }

    #[Test]
    public function aTransientFailureIsNotNegativelyCachedAndIsRetriedOnTheNextImport(): void
    {
        // A 5xx/connection-level failure isn't proof the reference is bad -
        // unlike a 200 with a null body, which POTA uses specifically to mean
        // "this reference doesn't exist."
        Http::fake([
            'api.pota.app/park/US-9999' => Http::response(null, 503),
        ]);

        $first = $this->service->getParkInfo('US-9999');
        $this->reloadService();
        $second = $this->service->getParkInfo('US-9999');

        $this->assertFalse($first);
        $this->assertFalse($second);
        // both calls should have gone out over the network - neither was negative-cached
        Http::assertSentCount(2);
    }

    protected function tearDown(): void
    {
        Cache::flush();
        parent::tearDown();
    }
}
