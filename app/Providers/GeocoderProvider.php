<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class GeocoderProvider extends ServiceProvider
{

    public function __construct($app)
    {
        parent::__construct($app);
        $this->baseUrl = 'https://api.geoapify.com/';
        $this->apiKey = config('services.geoapify.key');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(GeocoderProvider::class, function ($app) {
            return new GeocoderProvider($app);
        });
    }

    public function geocode($address){
        if(\Cache::has(sha1($address))){
            return \Cache::get(sha1($address));
        }
        $value =  Http::get('https://api.geoapify.com/v1/geocode/search', [
            'text' => $address,
            'format' => 'json',
            'apiKey' => $this->apiKey,
        ])->json();
        \Cache::put(sha1($address), $value, now()->addDay());
        return $value;
    }
}
