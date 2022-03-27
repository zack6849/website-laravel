<?php

namespace App\Http\Controllers;

use App\Providers\TwilioProvider;
use Illuminate\Http\Request;
use Twilio\TwiML\MessagingResponse;

class TwilioController extends Controller
{

    function lookup($phone_number, TwilioProvider $twilio){
        return $twilio->extractData($twilio->lookupNumber($phone_number));
    }

    function raw($phone_number, TwilioProvider $twilio){
        return $twilio->lookupNumber($phone_number);
    }

    function sms(Request $request, TwilioProvider $provider){
        $sms_body = $request->Body;
        $matches = [];
        preg_match_all('/\b\+?[0-9]?[0-9]{3}\s*-?\s*[0-9]{3}\s*-?\s*[0-9]{4}\b/',$sms_body,$matches);
        if(empty($matches)){
            return (new MessagingResponse)->message("Couldn't find phone number in message '$sms_body'");
        }
        $first_group = array_pop($matches);
        $number = array_pop($first_group);
        //if fed a local number, presume US prefix
        if(strlen($number) == 10){
            $number = "1$number";
        }
        return (new MessagingResponse())->message($provider->toSms($provider->extractData($provider->lookupNumber($number))));
    }

}
