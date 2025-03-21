<?php

namespace App\Services;

use App\Models\POTAPark;
use App\Models\POTAParkType;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class ParksOnTheAirService extends ServiceProvider
{

    private string $baseUrl = 'https://api.pota.app';

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
        if ($parkData !== null) {
            return $parkData;
        }

        $apiRequest = $this->buildRequest()->get("/park/$parkReference");
        if (!$apiRequest->successful() || $apiRequest->json() == null) {
            \Log::warning("Failed to get park info from POTA API: " . $apiRequest->body());
            return false;
        }

        $apiData = $apiRequest->json();
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
    }
}
