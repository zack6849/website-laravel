<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\TwilioService;
use Illuminate\Http\Request;
use Twilio\TwiML\MessagingResponse;

class TwilioController extends Controller
{

    public function lookup($phone_number, TwilioService $twilio)
    {
        return $twilio->extractData($twilio->lookupNumber($phone_number));
    }

    public function rawLookup($phone_number, TwilioService $twilio)
    {
        return $twilio->lookupNumber($phone_number);
    }

    public function twilioResponse(Request $request, TwilioService $provider){
        $response = new MessagingResponse();
        $sms_body = $request->Body;
        $matches = [];
        preg_match_all('/\b\+?\d?\d{3}\s*-?\s*\d{3}\s*-?\s*\d{4}\b/', $sms_body, $matches);
        if (empty($matches) || empty($matches[0])) {
            $response->message("No phone number detected in message.");
            return $response;
        }
        $first_group = array_pop($matches);
        $number = array_pop($first_group);
        //if fed a local number, presume US prefix
        if (strlen($number) == 10) {
            $number = "1$number";
        }
        //if it's too small of a number, bail.
        if (strlen($number) <= 6) {
            $response->message("Number too short!");
            return $response;
        }
        $response->message($provider->toSms($provider->extractData($provider->lookupNumber($number))));
        return $response;
    }

}
