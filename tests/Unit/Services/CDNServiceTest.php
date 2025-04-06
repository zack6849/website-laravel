<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\CDNService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\ServiceTest;

class CDNServiceTest extends TestCase
{

    use ServiceTest;
    private CDNService $service;
    private const BASE_URL = 'https://api.digitalocean.com/';

    public function setUp(): void
    {
        parent::setUp();
        $this->setService(CDNService::class);
    }

    #[Test]
    public function constructsClientCorrectly(): void
    {
        Http::fake();
        $endpointId = Str::uuid()->toString();
        $apiKey = Str::random(32);
        config(['services.digitalocean.key' => $apiKey]);
        //reload service so it picks up the new config
        $this->reloadService();
        $this->service->purgeCache($endpointId, '/test/path');
        Http::assertSent(function(Request $request) use ($apiKey, $endpointId){
            $expectedUrl = self::BASE_URL . 'v2/cdn/endpoints/' . $endpointId . '/cache';
            return $request->url() == $expectedUrl &&
                $request->hasHeader('Authorization', "Bearer $apiKey") &&
                $request->hasHeader('Accept', 'application/json') &&
                $request->method() == 'DELETE' &&
                $request->data() == ['files' => ['/test/path']];
        });
    }
}
