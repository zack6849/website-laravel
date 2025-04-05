<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Exceptions\CachePurgeFailureException;
use App\Services\CDNService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class PurgeCDNCacheJob implements ShouldQueue
{
    use Queueable;

    public int $backoff = 5;

    public function retryUntil(): \DateTime
    {
        return now()->addMinutes(10);
    }

    /**
     * Create a new job instance.
     */
    public function __construct(public string $path)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(CDNService $service): void
    {
        $success = $service->purgeCache(
            config('services.digitalocean.cdn.id'),
            $this->path
        );
        if(!$success){
            $this->fail(new CachePurgeFailureException("Failed to purge CDN cache for path {$this->path}"));
        }
    }
}
