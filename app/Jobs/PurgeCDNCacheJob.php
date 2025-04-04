<?php

namespace App\Jobs;

use App\Exceptions\CachePurgeFailureException;
use App\Services\CDNService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class PurgeCDNCacheJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

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

    /**
     * Determine the time to wait before retrying the job.
     */
    public function backoff(): array
    {
        return [1, 2, 4, 8, 16]; // Exponential backoff times in seconds
    }
}
