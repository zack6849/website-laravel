<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Twilio\TwiML\MessagingResponse;

class HomeScanController extends Controller
{

    public $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:92.0) Gecko/20100101 Firefox/92.0';

    public function search(Request $request)
    {
        $sms_body = $request->Body;
        $matches = [];
        $twilio_response = new MessagingResponse();

        preg_match("#http.+homesnap\.com.+$#", $sms_body, $matches);
        if (!empty($matches)) {
            Log::info("By URL");
            $listing_id = $this->getListingIdByURL($matches[0]);
        } else {
            Log::info("By Address");
            $listing_id = $this->getListingIdByAddress($sms_body);
        }
        if ($listing_id == null) {
            $twilio_response->message('Failed to find a listing link or locate a listing by address');
        }
        Log::info("LID:" . var_export($listing_id, true));
        $twilio_response->message($this->getSummaryByListingID($listing_id));
        return $twilio_response;
    }
    
    public function searchByAddress(Request $request){
        $address = $request->address;
        $listing_id = $this->getListingIdByAddress($address);
        return $this->getHomeSnapInfoByListingId($listing_id);
    }

    function getHomeSnapInfoByListingId($listingId)
    {
        $key = 'hsinfobyid'.$listingId;
        if (\Cache::has($key)) {
            return \Cache::get($key);
        }
        $response = Http::withHeaders([
            'User-Agent' => $this->userAgent
        ])->withBody(json_encode([
            'listingID' => $listingId
        ], JSON_PRETTY_PRINT), 'application/json')
            ->post('https://www.homesnap.com/service/Listings/GetDetails');

        if ($response->status() !== 200) {
            return "Failed to get info for listing id $listingId: " . $response->body() ." Status Code ".$response->status();
        }
        $data = json_decode($response->body(), true)['d'];
        $new_details = collect($data['Details'])->mapWithKeys(function ($detail) {
            return [
                $detail['Name'] => collect($detail['Fields'])->mapWithKeys(function ($array) {
                    return [$array['Name'] => $array['Value']];
                })
            ];
        });
        $data['Details'] = $new_details;
        \Cache::put($key, $data);
        return  $data;
    }

    function getListingIdByURL($url)
    {
        $path = parse_url($url, PHP_URL_PATH);
        $key = 'hslistingidfromurl'.$path;
        if (\Cache::has($key)) {
            return \Cache::get($key);
        }
        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:92.0) Gecko/20100101 Firefox/92.0',
        ])->withBody(json_encode([
            'url' => $path,
        ], JSON_PRETTY_PRINT), 'application/json')->post('https://www.homesnap.com/service/PropertyAddresses/GetByUrl');

        if ($response->status() !== 200) {
            return "Failed to get listing ID from url $url: " . $response->body() . " Status Code: ".$response->status();
        }
        $data = json_decode($response->body(), true);
        $listing_id = Arr::get($data, 'd.Listing.ID');
        \Cache::set($key, $listing_id);
        return $listing_id;
    }

    function getListingIdByAddress($address)
    {
        $key = 'hslistingidfromaddr'.$address;
        if (\Cache::has($key)) {
            return \Cache::get($key);
        }
        $response = Http::withHeaders([
            'User-Agent' => $this->userAgent,
        ])->withBody(json_encode([
            'text' => $address . " fl",
            'skip' => 0,
            'submit' => true,
            'take' => 8,
            'polygonType' => 1,
        ], JSON_PRETTY_PRINT), 'application/json')
            ->post('https://www.homesnap.com/service/Misc/Search');
        if ($response->status() !== 200) {
            return null;
        }
        $response_data = json_decode($response->body(), true);
        $listing_id = Arr::get($response_data, 'd.Properties.0.Listing.ID', 'No Listing ID found in response: '. json_encode($response_data, JSON_PRETTY_PRINT));
        if($listing_id !== null){
            \Cache::put($key, $listing_id);
        }
        return $listing_id;
    }

    function getSummaryByListingID($listing_id)
    {
        $data = collect($this->getHomeSnapInfoByListingId($listing_id));
        if(!empty($data['ContractDate'])){
            $data['ContractDate'] = date('c', $this->homesnapDateToDate($data['ContractDate']));
        }
        
        $beds = Arr::get($data, 'Details.Interior Features.Bedrooms Total');
        $baths = Arr::get($data, 'Details.Interior Features.Bathrooms Full');
        $construction_year = Arr::get($data,'YearBuilt');
        $sqft = Arr::get($data, 'SqFt');
        $cooling_source = Arr::get($data, 'Details.Utilities.Cooling');
        $heating_source = Arr::get($data, 'Details.Utilities.Heating');
        $sewer = Arr::get($data, 'Details.Utilities.Sewer');
        $water_source = Arr::get($data, 'Details.Utilities.Water Source');
        $flooring_info = Arr::get($data, 'Details.Interior Features.Flooring');
        $appliances = Arr::get($data, 'Details.Interior Features.Appliances Included');
        $foundation = Arr::get($data, 'Details.Interior Features.Foundation Details');
        $yearly_tax = Arr::get($data, 'Details.Tax Info.Tax Annual Amount');
        $homesteaded = Arr::get($data, 'Details.Homestead', 'Unknown');
        $full_address = implode(" ", [$data['FullStreetAddress'], $data['DefaultParentArea']['Name'], $data['Zip']]);
        $construction_material = Arr::get($data, 'Details.Listing Details.Construction Materials', 'Unknown');
        $floodzone_code = Arr::get($data, 'Details.Exterior Features.Flood Zone Code');
        $summary = <<<EOF
        Report For: $full_address
        
        $sqft Sq. Ft house w/ $beds bed(s) and $baths bath(s) Built in $construction_year
        The exterior is $construction_material, and the foundation is $foundation
        Flood Zone Code: $floodzone_code
        Tax: $yearly_tax yearly, homesteaded: $homesteaded
        HVAC: Cooling $cooling_source, Heating: $heating_source
        Sewer/Water: Sewer: $sewer, Water Source: $water_source
        Floors: $flooring_info
        Under Contract?: {$data['ContractDate']}
EOF;

        
//        $data = [
//            'Address' => ,
//            'Construction Mat.' => ,
//            'Short Summary' => "$sqft Sq. Ft $beds bed(s), $baths bath(s) home built in $construction_year",
//            'Flood Zone Code' => ,
//            'HVAC' => "Cooling: $cooling_source, Heating: $heating_source",
//            'Water/Sewer' => "Sewer: $sewer, Water Source: $water_source",
//            "Tax" => "$yearly_tax a year, homesteaded? $homesteaded",
//            'Current Asking' => @money_format('%i', $data['CurrentPrice']),
//            'Annual Tax' =>  @money_format('%i', $additional_info['Tax Info']['Tax Annual Amount']),
//            'Original Asking' => @money_format('%i', $data['OriginalPrice']),
//            'Agent' => $data['ListingAgentFullName'].' w/ '.$data['ListingBrokerName'],
//            'Under Contract' => $data['ContractDate'] !== null ? 'as of ' . $data['ContractDate'] : 'N',
//            'Year Built ' => $data['YearBuilt'],
//        ];
//        $summary = "";
//        foreach ($data as $key => $value){
//            $summary .= "$key: $value\r\n";
//        }
        return $summary;
    }

    function homesnapDateToDate($date_string)
    {
        $matches = [];
        preg_match('#(\d+)#', $date_string, $matches);
        return $matches[0] / 1000;
    }
}
