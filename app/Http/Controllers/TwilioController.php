<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Twilio\Rest\Client;

class TwilioController extends Controller
{

    function lookup(Request $request, $phone_number){
        $cache_key = "lookups". md5($phone_number);
        if(!Cache::has($cache_key)){
            $client = new Client(config('twilio.sid'), config('twilio.token'));
            $result = $client->lookups->v1->phoneNumbers($phone_number)->fetch([
                'type' => ['carrier','caller-name'],
                'addOns' => [
                    'ekata_reverse_phone',
                ]
            ]);
            $response = $result->toArray();
            Cache::forever($cache_key, $response);
        }

        $response = Cache::get($cache_key);
        $ekata_data = $response['addOns']['results']['ekata_reverse_phone']['result'];
        $people = $ekata_data['associated_people'];
        $people_map = [];
        foreach ($people as $person){
            $people_map[$person['id']] = [
                'name' => $person['name'],
                'relation' => $person['relation']
            ];
        }
        $other_owners = $ekata_data['belongs_to'];

        return [
            'possible_owners' => [
                $response['callerName']['caller_name'],
                $other_owners['name']
            ],
            'country' => $response['countryCode'],
            'carrier' => $response['carrier']['name'],
            'type' => $response['carrier']['type'],
            'associated_people' => $people_map
        ];
    }
}
