<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Exceptions\QRZAPIException;
use App\Services\QRZLogbookService;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\ServiceTest;

class QRZLogbookServiceTest extends TestCase
{

    use ServiceTest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setService(QRZLogbookService::class);
    }

    #[Test]
    public function throwsWhenQRZRespondsWithAnAuthFailure(): void
    {
        //QRZ responds HTTP 200 even when the request logically failed
        Http::fake([
            'logbook.qrz.com/*' => Http::response(
                'STATUS=AUTH&RESULT=AUTH&REASON=API+USER%3A+BOOK%3A+STATUS%3A+AUTH+REASON%3A+invalid+api+key&EXTENDED=',
                200
            ),
        ]);

        $this->expectException(QRZAPIException::class);
        $this->service->getLogbookEntries();
    }

    #[Test]
    public function parsesEntriesOnASuccessfulFetch(): void
    {
        $adif = "<QSO_DATE:8>20240101<TIME_ON:4>1200<CALL:4>W1AW<EOR>";
        Http::fake([
            'logbook.qrz.com/*' => Http::response(
                'RESULT=OK&COUNT=1&ADIF=' . urlencode($adif),
                200
            ),
        ]);

        $entries = $this->service->getLogbookEntries();

        $this->assertCount(1, $entries);
        $this->assertEquals('W1AW', $entries[0]['CALL']);
    }
}
