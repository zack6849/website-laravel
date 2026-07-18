<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\MainheadGridResolutionService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\ServiceTest;

class MainheadGridResolutionServiceTest extends TestCase
{
    use ServiceTest;

    private MainheadGridResolutionService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->setService(MainheadGridResolutionService::class);
    }

    #[Test]
    public function emptyGridReturnsAllNulls(): void
    {
        $this->assertEquals([null, null, null, null], $this->service->getGridSquare(''));
    }

    #[Test]
    public function oddLengthGridReturnsAllNulls(): void
    {
        $this->assertEquals([null, null, null, null], $this->service->getGridSquare('FN4'));
    }

    #[Test]
    public function validGridResolvesToSaneCoordinates(): void
    {
        [$lat, $lon] = $this->service->getGridSquare('FN42');
        $this->assertNotNull($lat);
        $this->assertNotNull($lon);
        $this->assertGreaterThanOrEqual(-90, $lat);
        $this->assertLessThanOrEqual(90, $lat);
        $this->assertGreaterThanOrEqual(-180, $lon);
        $this->assertLessThanOrEqual(180, $lon);
    }
}
