<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class HomeSnapListingService extends ServiceProvider
{

    public $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:92.0) Gecko/20100101 Firefox/92.0';

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(HomeSnapListingService::class, function ($app) {
            return new HomeSnapListingService($app);
        });
    }

    public function getListingIdForAddress($address, $allowCache = false)
    {
        $key = 'hslistingidfromaddr' . $address;
        if ($allowCache && \Cache::has($key)) {
            return \Cache::get($key);
        }
        $response = Http::withHeaders([
            'User-Agent' => $this->userAgent,
        ])->asJson()->post('https://www.homesnap.com/service/Misc/Search', [
            'text' => $address . " fl",
            'skip' => 0,
            'submit' => true,
            'take' => 8,
            'polygonType' => 1,
        ]);

        if ($response->status() !== 200) {
            throw new \Exception("Failed to find a homesnap listing for the given address");
        }
        $response_data = json_decode($response->body(), true);
        $listing_id = Arr::get($response_data, 'd.Properties.0.Listing.ID', Arr::get($response_data, 'd.Properties.0.ID', false));
        if ($listing_id === false) {
            throw new \Exception("Failed to extract listing ID from response: {$response->body()}, code: {$response->status()}");
        }
        \Cache::put($key, $listing_id);
        return $listing_id;
    }

    public function getListingIdFromHomesnapUri($allowCache = true)
    {
        $path = parse_url($url, PHP_URL_PATH);
        $key = 'hslistingidfromurl' . $path;
        if ($allowCache && \Cache::has($key)) {
            return \Cache::get($key);
        }
        $response = Http::asJson()->withHeaders([
            'User-Agent' => $this->userAgent,
        ])->post('https://www.homesnap.com/service/PropertyAddresses/GetByUrl', [
            'url' => $path,
        ]);

        if ($response->status() !== 200) {
            throw new \Exception("Failed to get listing ID from url '$url', response code: {$response->status()}");
        }
        $data = json_decode($response->body(), true);
        $listingId = Arr::get($data, 'd.Listing.ID', false);
        //don't cache an invalid response.
        if ($listingId === false) {
            throw new \Exception("Failed to get listing ID from data: " . json_encode($data));
        }
        \Cache::set($key, $listingId);
        return $listingId;
    }

    public function getHomeSnapListingInformation($listingId, $allowCache = true)
    {
        $key = 'hsinfobyid' . $listingId;
        if ($allowCache && \Cache::has($key)) {
            return \Cache::get($key);
        }
        $response = Http::withHeaders([
            'User-Agent' => $this->userAgent
        ])->asJson()->post('https://www.homesnap.com/service/Listings/GetDetails', [
            'listingID' => $listingId
        ]);

        if ($response->status() !== 200) {
            throw new \Exception("Failed to get info for listing id $listingId, response: {$response->body()}, code: {$response->status()}");
        }
        $data = json_decode($response->body(), true);
        $listingData = Arr::get($data, 'd', false);
        if ($listingData === false) {
            throw new \Exception("Failed to get listing info from listing ID $listingId, response: {$response->body()}, code: {$response->status()}");
        }
        //map the weird schema to a key value array
        $new_details = collect($listingData['Details'])->mapWithKeys(function ($detail) {
            return [
                $detail['Name'] => collect($detail['Fields'])->mapWithKeys(function ($array) {
                    return [$array['Name'] => $array['Value']];
                })
            ];
        });
        $listingData['Details'] = $new_details;
        \Cache::put($key, $listingData);
        return $listingData;
    }

    public function getSummary($data){
        if (!empty($data['ContractDate'])) {
            $data['ContractDate'] = date('c', $this->homesnapDateToDate($data['ContractDate']));
        } else {
            $data['ContractDate'] = 'Unknown.';
        }

        $beds = Arr::get($data, 'Details.Interior Features.Bedrooms Total');
        $baths = Arr::get($data, 'Details.Interior Features.Bathrooms Full');
        $construction_year = Arr::get($data, 'YearBuilt');
        $squareFootage = Arr::get($data, 'SqFt');
        $interiorCoolingSource = Arr::get($data, 'Details.Utilities.Cooling');
        $interiorHeatingSource = Arr::get($data, 'Details.Utilities.Heating');
        $sewerType = Arr::get($data, 'Details.Utilities.Sewer');
        $waterSource = Arr::get($data, 'Details.Utilities.Water Source');
        $floorCovering = Arr::get($data, 'Details.Interior Features.Flooring');
        $includedApplicances = Arr::get($data, 'Details.Interior Features.Appliances Included');
        $foundation = Arr::get($data, 'Details.Interior Features.Foundation Details');
        $annualPropertyTax = Arr::get($data, 'Details.Tax Info.Tax Annual Amount');
        $homesteadInfo = Arr::get($data, 'Details.Homestead', 'Unknown');
        $streetAddress = Arr::get($data, 'FullStreetAddress', 'No Address');
        $area = Arr::get($data, 'DefaultParentArea.Name', 'No Area Name');
        $zip = Arr::get($data, 'Zip');
        $localizedAddress = implode(" ", [$streetAddress, $area, $zip]);
        $exteriorWallMaterial = Arr::get($data, 'Details.Listing Details.Construction Materials', 'Unknown');
        $floodzoneType = Arr::get($data, 'Details.Exterior Features.Flood Zone Code');

        return <<<EOF
        Report For: $localizedAddress

        $squareFootage Sq. Ft house w/ $beds bed(s) and $baths bath(s) Built in $construction_year
        The exterior is $exteriorWallMaterial, and the foundation is $foundation
        Flood Zone Code: $floodzoneType
        Tax: $annualPropertyTax yearly, homesteaded: $homesteadInfo
        HVAC: Cooling $interiorCoolingSource, Heating: $interiorHeatingSource
        Sewer/Water: Sewer: $sewerType, Water Source: $waterSource
        Floors: $floorCovering
        Under Contract?: {$data['ContractDate']}
EOF;
    }

    private function homesnapDateToDate($dateString)
    {
        $matches = [];
        preg_match('#(\d+)#', $dateString, $matches);
        return $matches[0] / 1000;
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
