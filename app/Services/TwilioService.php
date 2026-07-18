<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
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
     * Builds the rate limit key/limit/decay for a lookup request, keyed by
     * user when authenticated, otherwise by IP.
     * @return array{0: int|string, 1: int, 2: int}
     */
    public function getRateLimitConfiguration(?User $user, string $ip): array
    {
        $limit = config('twilio.public_rate_limit', 3);
        $decayRate = config('twilio.public_decay_rate', 86400);
        $key = $ip;
        if ($user !== null) {
            $key = $user->id;
            $limit = $user->lookup_limit ?? $limit;
            $decayRate = $user->lookup_decay_rate ?? $decayRate;
        }
        return [$key, $limit, $decayRate];
    }

    /**
     * Enforces the public lookup rate limit for the given user/IP, unless the
     * phone number's response is already cached.
     * @param bool|null $isCached pass the already-known cached status to skip the internal cache check
     * @param string|null $rateLimitKeyOverride bucket by this key instead of the user/IP-derived one
     *        (e.g. the sender's number for the SMS webhook, so each texter gets their own quota)
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function enforceRateLimit(string $phoneNumber, ?User $user, string $ip, ?bool $isCached = null, ?string $rateLimitKeyOverride = null): void
    {
        $isCached ??= $this->hasCachedResponseFor($this->normalizePhoneNumber($phoneNumber));
        if ($isCached) {
            return;
        }
        [$key, $limit, $decay] = $this->getRateLimitConfiguration($user, $ip);
        if ($rateLimitKeyOverride !== null) {
            $key = $rateLimitKeyOverride;
        }
        if (RateLimiter::tooManyAttempts($key, $limit)) {
            $availableAt = now()->addSeconds(RateLimiter::availableIn($key))->longAbsoluteDiffForHumans();
            abort(429, "Rate limit exceeded. Please try again in $availableAt.");
        }
        RateLimiter::increment($key, $decay);
    }

    /**
     * Normalizes, validates, rate-limits, and looks up a phone number in one step.
     * This is the single entry point every caller (web/API/SMS webhook/Livewire) should use,
     * so rate limiting can't be accidentally skipped by a new call site.
     *
     * The underlying fetch/cache always includes the paid Ekata identity/address tier, and is
     * shared by every caller regardless of trust level, so a given number is only ever billed
     * once no matter how many times (or by whom) it's looked up. What varies by caller is
     * whether that identity data is stripped from the *returned* value: $includeIdentityData
     * must only be true for trusted/authenticated requesters, since the redaction happens here,
     * before the result ever reaches a Livewire property or JSON response.
     * @param string|null $rateLimitKeyOverride bucket by this key instead of the user/IP-derived one
     * @param bool $includeIdentityData whether the returned data should include the identity tier
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function performLookup(string $phoneNumber, ?User $user, string $ip, ?string $rateLimitKeyOverride = null, bool $includeIdentityData = false): false|array
    {
        $phoneNumber = $this->normalizePhoneNumber($phoneNumber);
        if (strlen($phoneNumber) < 7 || strlen($phoneNumber) > 15) {
            abort(422, 'Invalid phone number.');
        }
        $isCached = $this->hasCachedResponseFor($phoneNumber);
        $this->enforceRateLimit($phoneNumber, $user, $ip, $isCached, $rateLimitKeyOverride);
        $data = $this->lookupNumber($phoneNumber, false, $isCached);
        if (!is_array($data)) {
            //normalize any non-array result (e.g. a cache entry evicted between the
            //hasCachedResponseFor() check and the Cache::get() read) to the declared false|array shape
            return false;
        }
        if ($includeIdentityData) {
            return $data;
        }
        return $this->stripIdentityData($data);
    }

    /**
     * Looks up a phone number. Always fetches/caches the full response, including the paid
     * Ekata identity/address add-on, so a given number is only ever billed once, ever, no
     * matter how many times or by whom it's subsequently requested.
     * @param string $phoneNumber the phone number
     * @param bool $bustCache if true, ignore cache and fetch latest
     * @param bool|null $isCached pass the already-known cached status to skip the internal cache check
     * @return array|false the response from the lookup or false on failure
     */
    public function lookupNumber(string $phoneNumber, bool $bustCache = false, ?bool $isCached = null): false|array
    {
        $phoneNumber = $this->normalizePhoneNumber($phoneNumber);
        $key = $this->getCacheKey($phoneNumber);
        $isCached ??= $this->hasCachedResponseFor($phoneNumber);
        if (!$isCached || $bustCache) {
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
     * Does a lookup on the phone number using Twilio, always including the paid Ekata
     * identity/address add-on. This is always fetched (and cached forever by lookupNumber())
     * regardless of who's asking, since the number is billed on first fetch either way; callers
     * that shouldn't see identity data get it stripped via performLookup()/stripIdentityData()
     * instead of avoiding the fetch, so a number is never billed twice for two different tiers.
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

    /**
     * Removes the paid Ekata identity/address add-on results from a raw Twilio lookup
     * response, leaving only the free carrier/line-type/CNAM data. Used to redact
     * performLookup()'s return value for unauthenticated/untrusted requesters, independent
     * of whether the underlying cached response includes the identity tier.
     */
    public function stripIdentityData(array $response): array
    {
        unset($response['addOns']);
        return $response;
    }

    /**
     * @param bool $includeIdentityData whether to surface the Ekata identity/address data
     *        even if present in $response. Defaults to false so callers can't accidentally
     *        leak identity data by forgetting to pass this explicitly.
     */
    public function extractData($response, bool $includeIdentityData = false): array
    {
        $response_data = $this->initializeResponseData($response);

        if ($includeIdentityData && $this->isAddOnFetchSuccessful($response)) {
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
