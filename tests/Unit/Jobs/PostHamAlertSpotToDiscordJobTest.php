<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Jobs\PostHamAlertSpotToDiscordJob;
use App\Models\HamAlertSpot;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PostHamAlertSpotToDiscordJobTest extends TestCase
{
    #[Test]
    public function discordPostsCannotTriggerMentions(): void
    {
        Http::fake();
        config(['services.discord.webhook_uri' => 'https://discord.test/webhook']);

        $spot = HamAlertSpot::factory()->create([
            'callsign' => '@everyone',
            'spotter_callsign' => '@here',
        ]);

        (new PostHamAlertSpotToDiscordJob($spot))->handle();

        Http::assertSent(function (Request $request): bool {
            return $request->url() === 'https://discord.test/webhook'
                && $request['allowed_mentions'] === ['parse' => []];
        });
    }

    #[Test]
    public function missingDiscordWebhookSkipsPosting(): void
    {
        Http::fake();
        config(['services.discord.webhook_uri' => null]);

        (new PostHamAlertSpotToDiscordJob(HamAlertSpot::factory()->create()))->handle();

        Http::assertNothingSent();
    }
}
