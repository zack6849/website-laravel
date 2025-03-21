<?php

namespace App\Http\Controllers;

use App\Services\HomeSnapListingService;
use Illuminate\Http\Request;
use Twilio\TwiML\MessagingResponse;

class HomeScanController extends Controller
{

    public function search(Request $request, HomeSnapListingService $listingService)
    {
        $smsBody = $request->Body;
        $response = new MessagingResponse();
        try{
            $matches = [];
            preg_match("#http.+homesnap\.com.+$#", $smsBody, $matches);
            if (!empty($matches)) {
                $listingId = $listingService->getListingIdFromHomesnapUri($matches[0]);
            } else {
                $listingId = $listingService->getListingIdForAddress($smsBody);
            }
            if ($listingId == null) {
                $response->message('Failed to find a listing link or locate a listing by address');
                return $response;
            }
            $listingData = $listingService->getHomeSnapListingInformation($listingId);
            $response->message($listingService->getSummary($listingData));
        }catch (\Exception $exception){
            $response->message($exception->getMessage());
        }
        return $response;
    }

    public function searchByAddress(Request $request, HomeSnapListingService $listingService){
        $address = $request->address;
        $listing_id = $listingService->getListingIdForAddress($address);
        return $listingService->getHomeSnapListingInformation($listing_id);
    }
}
