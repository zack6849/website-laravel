<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;
use j4nr6n\ADIF\Parser;

class QRZLogbookService
{

    private string $baseUrl = 'https://logbook.qrz.com';
    private mixed $token = '';

    public function __construct()
    {
        $this->baseUrl = '';
        $this->token = config('services.qrz.key');
    }

    public function getLogbookEntries()
    {
        if (\Storage::exists('qrzlogbook.txt')) {
            $adifString = \Storage::get('qrzlogbook.txt');
            return $this->parseLogbookEntries($adifString);
        }
        $response = Http::asForm()->post($this->baseUrl . '/api', [
            'KEY' => $this->token,
            'ACTION' => 'FETCH'
        ]);
        \Storage::put('qrzlogbook.txt', $response->body());
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
