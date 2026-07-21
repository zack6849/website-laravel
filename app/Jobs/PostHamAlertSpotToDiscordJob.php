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
        $webhookUri = config('services.discord.webhook_uri');

        if (! is_string($webhookUri) || trim($webhookUri) === '') {
            return;
        }

        Http::timeout(5)->connectTimeout(3)->retry(3, 100)->post($webhookUri, [
            'content' => $this->spot->toDiscordSummary(),
            'allowed_mentions' => [
                'parse' => [],
            ],
        ]);
    }
}
