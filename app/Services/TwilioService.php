<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class TwilioService
{
    /**
     * Looks up a phone number
     * @param string $phoneNumber the phone number
     * @param bool $bustCache if true, ignore cache and fetch latest
     * @return array|false the response from the lookup or false on failure
     */
    public function lookupNumber(string $phoneNumber, bool $bustCache = false): false|array
    {
        $phoneNumber = $this->normalizePhoneNumber($phoneNumber);
        if (!$this->hasCachedResponseFor($phoneNumber) || $bustCache) {
            return $this->lookupPhoneNumber($phoneNumber);
        }
        return Cache::get($this->getCacheKey($phoneNumber));
    }

    /**
     * Normalizes a phone number, remove anything other than numbers, dashes, and plus signs
     * @param string $phoneNumber
     * @return array|string|null
     */
    public function normalizePhoneNumber(string $phoneNumber): array|string|null
    {
        return $this->e614PhoneNumber(preg_replace('/([^\d\-+])/', '', $phoneNumber));
    }

    public function e614PhoneNumber(string $phoneNumber): string
    {
        if (strlen($phoneNumber) == 10) {
            return "1$phoneNumber";
        }
        return $phoneNumber;
    }

    public function hasCachedResponseFor($phone_number): bool
    {
        return Cache::has($this->getCacheKey($phone_number));
    }

    /**
     * @param $phone_number
     * Looks up the phone number and caches the result
     */
    private function lookupPhoneNumber($phone_number): false|array
    {
        try {
            $client = new Client(config('twilio.sid'), config('twilio.token'));
            $result = $client->lookups->v1->phoneNumbers($phone_number)->fetch([
                'type' => ['carrier', 'caller-name'],
                'addOns' => [
                    'ekata_reverse_phone',
                ]
            ]);
        } catch (ConfigurationException|TwilioException $e) {
            return false;
        }
        $response = $result->toArray();
        Cache::forever($this->getCacheKey($phone_number), $response);
        return $response;
    }

    private function getCacheKey($phone_number): string
    {
        return "lookups" . md5($phone_number);
    }

    /**
     * @todo: this is disgusting, fix it
     */
    public function extractData($response): array
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

    public function toSms($data): string
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
