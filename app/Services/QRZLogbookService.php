<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\QRZAPIException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;
use j4nr6n\ADIF\Parser;

class QRZLogbookService
{

    private string $baseUrl = '';
    private mixed $token = '';

    public function __construct()
    {
        $this->baseUrl = 'https://logbook.qrz.com';
        $this->token = config('services.qrz.key');
    }

    public function getLogbookEntries()
    {
        $response = Http::asForm()->post($this->baseUrl . '/api', [
            'KEY' => $this->token,
            'ACTION' => 'FETCH'
        ]);
        if(!$response->successful()){
            $response->throw();
        }
        //QRZ's API always responds HTTP 200, even on auth/logical failure, and
        //signals errors via STATUS/RESULT/REASON fields in the body instead
        parse_str($response->body(), $data);
        if (!isset($data['ADIF'])) {
            throw new QRZAPIException(
                'QRZ logbook fetch failed: ' . ($data['REASON'] ?? $response->body())
            );
        }
        return $this->parseLogbookEntries($response->body());
    }

    public function parseLogbookEntries($adifString){
        $responseText = str_replace(["&lt;", "&gt;", "\n"], ["<", ">", ""], $adifString);
        $responseText = preg_replace("/&(?![^\s=]+=[^\s=])/m", "{AMP}", $responseText);
        $responseText = htmlspecialchars_decode($responseText);
        $data = [];
        parse_str($responseText, $data);
        $adifData = str_replace('{AMP}', '&', $data['ADIF']);
        return (new Parser())->parse($adifData);
    }
}
