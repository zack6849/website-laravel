<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;
use j4nr6n\ADIF\Parser;

class QRZLogbookProvider extends ServiceProvider
{

    private string $baseUrl;
    private mixed $token;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->baseUrl = 'https://logbook.qrz.com';
        $this->token = config('services.qrz.key');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(QRZLogbookProvider::class, function ($app) {
            return new QRZLogbookProvider($app);
        });
    }

    public function getLogbookEntries(){
        if(\Storage::exists('qrzlogbook.txt')){
            $adifString = \Storage::get('qrzlogbook.txt');
            return $this->parseLogbookEntries($adifString);
        }
        $response = Http::asForm()->post('https://logbook.qrz.com/api', [
            'KEY' => config('services.qrz.key'),
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
