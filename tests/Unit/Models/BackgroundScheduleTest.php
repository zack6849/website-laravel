<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Background;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BackgroundScheduleTest extends TestCase
{
    public function refreshDatabase(): void
    {
        // Pure in-memory model behavior; no persistence required.
    }

    #[Test]
    public function backgroundWithoutScheduleIsNeverConsideredScheduled(): void
    {
        $background = new Background(['schedule' => null]);

        $this->assertFalse($background->hasSchedule());
        $this->assertFalse($background->isScheduledFor(Carbon::create(2026, 12, 25)));
    }

    #[Test]
    public function recurringDateRuleMatchesRegardlessOfYear(): void
    {
        $background = new Background([
            'schedule' => [['type' => 'date', 'month' => 12, 'day' => 25]],
        ]);

        $this->assertTrue($background->isScheduledFor(Carbon::create(2026, 12, 25)));
        $this->assertTrue($background->isScheduledFor(Carbon::create(2030, 12, 25)));
        $this->assertFalse($background->isScheduledFor(Carbon::create(2026, 12, 24)));
    }

    #[Test]
    public function weekdayRuleMatchesConfiguredDays(): void
    {
        // 0 = Sunday ... 6 = Saturday
        $background = new Background([
            'schedule' => [['type' => 'weekday', 'days' => [6, 0]]],
        ]);

        $this->assertTrue($background->isScheduledFor(Carbon::create(2026, 7, 18))); // Saturday
        $this->assertTrue($background->isScheduledFor(Carbon::create(2026, 7, 19))); // Sunday
        $this->assertFalse($background->isScheduledFor(Carbon::create(2026, 7, 20))); // Monday
    }

    #[Test]
    public function rangeRuleMatchesInclusiveBounds(): void
    {
        $background = new Background([
            'schedule' => [['type' => 'range', 'start' => '2026-12-01', 'end' => '2026-12-31']],
        ]);

        $this->assertTrue($background->isScheduledFor(Carbon::create(2026, 12, 1)));
        $this->assertTrue($background->isScheduledFor(Carbon::create(2026, 12, 31, 23)));
        $this->assertFalse($background->isScheduledFor(Carbon::create(2027, 1, 1)));
    }
}
