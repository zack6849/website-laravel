<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\HamAlertSpot;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

class PostHamAlertSpotToDiscordJob implements ShouldQueue
{
    use Queueable;

    public int $backoff = 5;

    public function __construct(public HamAlertSpot $spot)
    {
    }

    public function handle(): void
    {
        Http::retry(3, 100)->post(config('services.discord.webhook_uri'), [
            'content' => $this->spot->toDiscordSummary(),
        ]);
    }
}
