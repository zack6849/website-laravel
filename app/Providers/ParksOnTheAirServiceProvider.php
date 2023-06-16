<?php

namespace App\Providers;

use App\Models\POTAPark;
use App\Models\POTAParkType;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;
use j4nr6n\ADIF\Parser;

class ParksOnTheAirServiceProvider extends ServiceProvider
{

    private string $baseUrl = 'https://api.pota.app';

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ParksOnTheAirServiceProvider::class, function ($app) {
            return new ParksOnTheAirServiceProvider($app);
        });
    }

    private function buildRequest(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)->acceptJson()->withHeaders([
            'Referer' => 'https://pota.app',
            'User-Agent' => 'Logbook Map (zcraig.me/qsos)'
        ]);
    }

    public function getParkInfo(string $parkReference): POTAPark|false
    {
        $parkData = POTAPark::where('reference', $parkReference)->first();
        if ($parkData == null) {
            $apiRequest = $this->buildRequest()->get("/park/$parkReference");
            if($apiRequest->successful()){
                $apiData = $apiRequest->json();
                if($apiData == null){
                    \Log::warning("Failed to get park info from POTA API for reference $parkReference: " . $apiRequest->body());
                    return false;
                }
                $parkType = POTAParkType::firstOrCreate(['name' => $apiData['parktypeDesc']], [
                    'id' => $apiData['parktypeId'],
                ]);
                $park = new POTAPark();
                $park->reference = $apiData['reference'];
                $park->name = $apiData['name'];
                $park->latitude = $apiData['latitude'];
                $park->longitude = $apiData['longitude'];
                $park->grid4 = $apiData['grid4'];
                $park->grid6 = $apiData['grid6'];
                $park->park_type_id = $parkType->id;
                $park->active = $apiData['active'];
                $park->comments = $apiData['parkComments'];
                $park->location = $apiData['locationName'];
                $park->first_activation_at = $apiData['firstActivationDate'];
                $park->raw_data = json_encode($apiData, JSON_PRETTY_PRINT);
                $park->save();
                return $park;
            }else{
                \Log::warning("Failed to get park info from POTA API: " . $apiRequest->body());
                return false;
            }
        }
        return $parkData;
    }
}
