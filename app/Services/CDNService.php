<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CDNService
{
    private const BASE_URL = 'https://api.digitalocean.com/';
    private $client;

    public function __construct()
    {
        $this->client = Http::baseUrl(static::BASE_URL)->withHeaders([
            'Authorization' => 'Bearer ' . config('services.digitalocean.key'),
            'Accept' => 'application/json',
        ]);
    }

    public function purgeCache(string $endpointId, string $path): bool
    {
        return $this->client->delete("/v2/cdn/endpoints/$endpointId/cache", [
            'files' => [$path]
        ])->successful();
    }
}
