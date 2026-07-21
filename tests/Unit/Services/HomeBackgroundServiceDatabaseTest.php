<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Background;
use App\Services\Backgrounds\BackgroundSelectionCache;
use App\Services\HomeBackgroundService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HomeBackgroundServiceDatabaseTest extends TestCase
{
    private HomeBackgroundService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = resolve(HomeBackgroundService::class);
        Background::query()->delete();
        Cache::flush();
    }

    #[Test]
    public function databaseBackgroundsAreSelected(): void
    {
        $background = Background::create([
            'key' => 'db_one',
            'title' => 'DB One',
            'image' => 'img/bg/bg_clownfish.jpg',
            'position' => ['x' => '40%', 'y' => '60%'],
            'size' => 'cover',
        ]);

        $result = $this->service->calculateCurrentBackgroundInfo();

        $this->assertSame((string) $background->id, $result['key']);
        $this->assertSame('DB One', $result['title']);
    }

    #[Test]
    public function pinnedBackgroundOverridesEverythingElse(): void
    {
        Background::create([
            'key' => 'weighted',
            'title' => 'Weighted',
            'image' => 'img/bg/bg_clownfish.jpg',
            'weight' => 1000,
        ]);
        $pinned = Background::create([
            'key' => 'pinned',
            'title' => 'Pinned',
            'image' => 'img/bg/pier_night.jpg',
            'weight' => 1,
            'pinned' => true,
        ]);

        for ($i = 0; $i < 5; $i++) {
            Cache::flush();
            $result = $this->service->calculateCurrentBackgroundInfo();
            $this->assertSame((string) $pinned->id, $result['key']);
        }
    }

    #[Test]
    public function scheduledBackgroundIsPreferredOnAMatchingDay(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 12, 25, 12));

        Background::create([
            'key' => 'everyday',
            'title' => 'Everyday',
            'image' => 'img/bg/bg_clownfish.jpg',
        ]);
        $festive = Background::create([
            'key' => 'festive',
            'title' => 'Festive',
            'image' => 'img/bg/pier_night.jpg',
            'schedule' => [
                ['type' => 'date', 'month' => 12, 'day' => 25],
            ],
        ]);

        Cache::flush();
        $result = $this->service->calculateCurrentBackgroundInfo();

        $this->assertSame((string) $festive->id, $result['key']);

        Carbon::setTestNow();
    }

    #[Test]
    public function disabledBackgroundsAreNeverSelected(): void
    {
        $enabled = Background::create([
            'key' => 'enabled',
            'title' => 'Enabled',
            'image' => 'img/bg/pier_night.jpg',
        ]);
        Background::create([
            'key' => 'disabled',
            'title' => 'Disabled',
            'image' => 'img/bg/bg_clownfish.jpg',
            'enabled' => false,
        ]);

        for ($i = 0; $i < 5; $i++) {
            Cache::flush();
            $result = $this->service->calculateCurrentBackgroundInfo();
            $this->assertSame((string) $enabled->id, $result['key']);
        }
    }

    #[Test]
    public function fallsBackToEmergencyBackgroundWhenNoEnabledDatabaseRows(): void
    {
        Background::create([
            'key' => 'disabled',
            'title' => 'Disabled',
            'image' => 'img/bg/bg_clownfish.jpg',
            'enabled' => false,
        ]);

        $result = $this->service->calculateCurrentBackgroundInfo();

        $this->assertSame('pier_night', $result['key']);
        $this->assertSame('St. Petersburg Pier', $result['title']);
    }

    #[Test]
    public function nullSelectionResultsAreCached(): void
    {
        $calls = 0;
        $cache = resolve(BackgroundSelectionCache::class);

        $first = $cache->remember(function () use (&$calls): null {
            $calls++;

            return null;
        });
        $second = $cache->remember(function () use (&$calls): array {
            $calls++;

            return ['item' => [], 'key' => 'unexpected'];
        });

        $this->assertNull($first);
        $this->assertNull($second);
        $this->assertSame(1, $calls);
    }
}
