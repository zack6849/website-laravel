<?php

namespace App\Http\Controllers;

use App\Providers\HomeSnapListingProvider;
use Illuminate\Http\Request;
use Twilio\TwiML\MessagingResponse;

class HomeScanController extends Controller
{

    public function search(Request $request, HomeSnapListingProvider $listingProvider)
    {
        $smsBody = $request->Body;
        $response = new MessagingResponse();
        try{
            $matches = [];
            preg_match("#http.+homesnap\.com.+$#", $smsBody, $matches);
            if (!empty($matches)) {
                $listingId = $listingProvider->getListingIdFromHomesnapUri($matches[0]);
            } else {
                $listingId = $listingProvider->getListingIdForAddress($smsBody);
            }
            if ($listingId == null) {
                $response->message('Failed to find a listing link or locate a listing by address');
                return $response;
            }
            $listingData = $listingProvider->getHomeSnapListingInformation($listingId);
            $response->message($listingProvider->getSummary($listingData));
        }catch (\Exception $exception){
            $response->message($exception->getMessage());
        }
        return $response;
    }

    public function searchByAddress(Request $request, HomeSnapListingProvider $listingProvider){
        $address = $request->address;
        $listing_id = $listingProvider->getListingIdForAddress($address);
        return $listingProvider->getHomeSnapListingInformation($listing_id);
    }
}
