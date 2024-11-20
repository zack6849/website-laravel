<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Twilio\Rest\Client;

class TwilioService
{
    public function lookupNumber($phone_number)
    {
        $cache_key = "lookups" . md5($phone_number);
        if (!Cache::has($cache_key)) {
            $client = new Client(config('twilio.sid'), config('twilio.token'));
            $result = $client->lookups->v1->phoneNumbers($phone_number)->fetch([
                'type' => ['carrier', 'caller-name'],
                'addOns' => [
                    'ekata_reverse_phone',
                ]
            ]);
            $response = $result->toArray();
            Cache::forever($cache_key, $response);
        }
        return Cache::get($cache_key);
    }

    public function extractData($response)
    {
        $response_data = [
            "possible_owners" => [
                \Arr::get($response, 'callerName.caller_name', 'Unknown Name'),
            ],
            "country" => \Arr::get($response, 'countryCode'),
            "carrier" => \Arr::get($response, 'carrier.name'),
            "type" => \Arr::get($response, 'carrier.type'),
            "associated_people" => [],
            "associated_addresses" => []
        ];

        //if the addons fetch worked
        if (\Arr::get($response, 'addOns.status', 'Fail') == 'successful') {
            //if ekata returned something
            if (array_key_exists('ekata_reverse_phone', $response['addOns']['results']) && is_array($response['addOns']['results']['ekata_reverse_phone'])) {
                //and it returned a success code
                if (\Arr::get($response, 'addOns.results.ekata_reverse_phone.status', 'Fail') == 'successful') {
                    $ekata_data = $response['addOns']['results']['ekata_reverse_phone']['result'];
                    if (array_key_exists('belongs_to', $ekata_data)) {
                        $potential_owner = $ekata_data['belongs_to'];
                        $response_data['possible_owners'] = [
                            "name" => \Arr::get($potential_owner, 'name', 'Unknown'),
                            "type" => \Arr::get($potential_owner, 'type', 'Unknown'),
                            "gender" => \Arr::get($potential_owner, 'gender', 'Unknown'),
                            "age" => \Arr::get($potential_owner, 'age', sprintf("Between %s and %s years old",
                                \Arr::get($potential_owner, 'age_range.from', 'Unknown'),
                                \Arr::get($potential_owner, 'age_range.to', 'Unknown')
                            )),
                        ];
                    }

                    if (array_key_exists('associated_people', $ekata_data)) {
                        $people = $ekata_data['associated_people'];
                        foreach ($people as $person) {
                            $response_data['associated_people'][] = [
                                'name' => $person['name'],
                                'relation' => $person['relation']
                            ];
                        }
                    }
                    if (array_key_exists('current_addresses', $ekata_data)) {
                        foreach ($ekata_data['current_addresses'] as $associated_address) {
                            $response_data['associated_addresses'][] = [
                                'street' => \Arr::get($associated_address, 'street_line_1', 'Unknown Street'),
                                'city' => \Arr::get($associated_address, 'city', 'Unknown City'),
                                'country' => \Arr::get($associated_address, 'country_code', 'Unknown Country'),
                                'state' => \Arr::get($associated_address, 'state_code', 'Unknown State')
                            ];
                        }
                    }
                }
            }
        }
        return $response_data;
    }

    public function toSms($data)
    {
        $possible_owners = implode(", ", $data['possible_owners']);
        $associated = [];
        foreach ($data['associated_people'] as $person) {
            $associated[] = $person['name'] . (!empty($person['relation']) ? " (Possible Relation: {$person['relation']})" : "");
        }
        $response = "Likely Owner: \n - $possible_owners\n";
        if (array_key_exists('associated_addresses', $data)) {
            $addresses = [];
            foreach ($data['associated_addresses'] as $address) {
                $addresses[] = implode(" ", [
                    $address['street'],
                    $address['city'],
                    $address['state'],
                    $address['country']
                ]);
            }
            if (!empty($addresses)) {
                $response .= "Likely Addresses: \n";
                foreach ($addresses as $address) {
                    $response .= " - $address\n";
                }
            }
        }
        $response .= "Other Associated Names:\n";
        foreach ($associated as $associated_name) {
            $response .= " - $associated_name\n";
        }
        return $response;
    }
}
