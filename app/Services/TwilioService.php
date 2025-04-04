<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class TwilioService
{

    public function __construct(
        private Client $twilioClient,
    )
    {
    }

    /**
     * Looks up a phone number
     * @param string $phoneNumber the phone number
     * @param bool $bustCache if true, ignore cache and fetch latest
     * @return array|false the response from the lookup or false on failure
     */
    public function lookupNumber(string $phoneNumber, bool $bustCache = false): false|array
    {
        $phoneNumber = $this->normalizePhoneNumber($phoneNumber);
        $key = $this->getCacheKey($phoneNumber);
        if (!$this->hasCachedResponseFor($phoneNumber) || $bustCache) {
            $data = $this->getTwilioInformationForPhoneNumber($phoneNumber);
            Cache::forever($key, $data);
            return $data;
        }
        return Cache::get($key);
    }

    /**
     * Normalizes a phone number, remove anything other than numbers, dashes, and plus signs
     * @param string $phoneNumber
     * @return array|string|null
     */
    public function normalizePhoneNumber(string $phoneNumber): array|string|null
    {
        return $this->e164Format(preg_replace('/(\D)/', '', $phoneNumber));
    }

    /**
     * Formats the given number to e614
     * This really just takes a 10-digit number and adds a 1 to the front for US numbers
     * @param string $phoneNumber
     * @return string
     */
    public function e164Format(string $phoneNumber): string
    {
        if (strlen($phoneNumber) == 10) {
            return "1$phoneNumber";
        }
        return $phoneNumber;
    }

    /**
     * Determine if we have a cached copy of the response for the given phone number
     * @param string $phoneNumber the number to look up, in e164 format
     * @return bool true if we have a cached response, false otherwise
     */
    public function hasCachedResponseFor(string $phoneNumber): bool
    {
        return Cache::has($this->getCacheKey($phoneNumber));
    }

    /**
     * Does a lookup on the phone number using Twilio
     * @param string $phoneNumber the number to look up, in e164 format
     * @return array the response from Twilio
     * @throws TwilioException
     */
    public function getTwilioInformationForPhoneNumber(string $phoneNumber): array
    {
        $result = $this->twilioClient->lookups->v1->phoneNumbers($phoneNumber)->fetch([
            'type' => ['carrier', 'caller-name'],
            'addOns' => [
                'ekata_reverse_phone',
            ]
        ]);
        return $result->toArray();
    }

    /**
     * Generates a cache key for the given phone number
     * (this is a SHA1 hash of the phone number)
     * @param string $phoneNumber the phone number to hash
     * @return string the cache key
     */
    public function getCacheKey(string $phoneNumber): string
    {
        return "twilio.lookups." . sha1($phoneNumber);
    }

    public function extractData($response): array
    {
        $response_data = $this->initializeResponseData($response);

        if ($this->isAddOnFetchSuccessful($response)) {
            $ekata_data = $this->getEkataData($response);
            if ($ekata_data) {
                $response_data['possible_owners'] = $this->getPossibleOwners($ekata_data);
                $response_data['associated_people'] = $this->getAssociatedPeople($ekata_data);
                $response_data['associated_addresses'] = $this->getAssociatedAddresses($ekata_data);
            }
        }

        return $response_data;
    }

    private function initializeResponseData($response): array
    {
        return [
            "possible_owners" => [
                Arr::get($response, 'callerName.caller_name', 'Unknown Name'),
            ],
            "country" => Arr::get($response, 'countryCode'),
            "carrier" => Arr::get($response, 'carrier.name'),
            "type" => Arr::get($response, 'carrier.type'),
            "associated_people" => [],
            "associated_addresses" => []
        ];
    }

    private function isAddOnFetchSuccessful($response): bool
    {
        return Arr::get($response, 'addOns.status', 'Fail') === 'successful';
    }

    private function getEkataData($response): ?array
    {
        if (array_key_exists('ekata_reverse_phone', $response['addOns']['results']) && is_array($response['addOns']['results']['ekata_reverse_phone'])) {
            if (Arr::get($response, 'addOns.results.ekata_reverse_phone.status', 'Fail') === 'successful') {
                return $response['addOns']['results']['ekata_reverse_phone']['result'];
            }
        }
        return null;
    }

    private function getPossibleOwners(array $ekata_data): array
    {
        $potential_owner = $ekata_data['belongs_to'] ?? [];
        return [
            "name" => Arr::get($potential_owner, 'name', 'Unknown'),
            "type" => Arr::get($potential_owner, 'type', 'Unknown'),
            "gender" => Arr::get($potential_owner, 'gender', 'Unknown'),
            "age" => Arr::get($potential_owner, 'age', sprintf("Between %s and %s years old",
                Arr::get($potential_owner, 'age_range.from', 'Unknown'),
                Arr::get($potential_owner, 'age_range.to', 'Unknown')
            )),
        ];
    }

    private function getAssociatedPeople(array $ekata_data): array
    {
        $people = $ekata_data['associated_people'] ?? [];
        return array_map(function ($person) {
            return [
                'name' => $person['name'],
                'relation' => $person['relation']
            ];
        }, $people);
    }

    private function getAssociatedAddresses(array $ekata_data): array
    {
        $addresses = $ekata_data['current_addresses'] ?? [];
        return array_map(function ($address) {
            return [
                'street' => Arr::get($address, 'street_line_1', 'Unknown Street'),
                'city' => Arr::get($address, 'city', 'Unknown City'),
                'country' => Arr::get($address, 'country_code', 'Unknown Country'),
                'state' => Arr::get($address, 'state_code', 'Unknown State')
            ];
        }, $addresses);
    }

    public function toSms(array $data): string
    {
        $response = "Likely Owner: \n";
        $response .= " - " . implode(", ", $data['possible_owners']) . "\n";

        if (!empty($data['associated_addresses'])) {
            $response .= "Likely Addresses: \n";
            foreach ($data['associated_addresses'] as $address) {
                $formattedAddress = (implode(" ", [
                    $address['street'],
                    $address['city'],
                    $address['state'],
                    $address['country']
                ]));
                $response .= " - " . $formattedAddress . "\n";
            }
        }

        $response .= "Other Associated Names:\n";

        if (!empty($data['associated_people'])) {
            foreach ($data['associated_people'] as $person) {
                $formattedPerson = $person['name'];
                if (!empty($person['relation'])) {
                    $formattedPerson .= " (Possible Relation: {$person['relation']})";
                }
                $response .= " - " . $formattedPerson . "\n";
            }
        }

        return $response;
    }
}
