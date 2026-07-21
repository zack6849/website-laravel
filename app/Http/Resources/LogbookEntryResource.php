<?php

namespace App\Http\Resources;

use App\Models\LogbookEntry;
use App\Models\POTAPark;
use App\Services\Logbook\LogbookGeographyService;
use App\Services\Logbook\LogbookScoringService;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Throwable;

/**
 * @mixin LogbookEntry
 */
class LogbookEntryResource extends JsonResource
{
    public function __construct(
        $resource,
        private readonly LogbookGeographyService $geography = new LogbookGeographyService(),
        private readonly LogbookScoringService $scoring = new LogbookScoringService(),
    ) {
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $qsoTimestamp = $this->qsoTimestamp();
        $ageDays = $this->scoring->ageDays($qsoTimestamp);
        $park = $this->park();
        $toCity = $this->stringAttribute('to_city');
        $toState = $this->stringAttribute('to_state');
        $toCounty = $this->stringAttribute('to_county');
        $toCountry = $this->stringValue($this->callee->country);
        $parkName = $this->stringValue($park?->name);
        $parkReference = $this->stringValue($park?->reference);
        $parkLocation = $this->stringValue($park?->location);

        $value = array_merge(parent::toArray($request), [
            'to_callsign' => $this->callee->name,
            'to_country' => $toCountry,
            'to_city' => $toCity,
            'to_state' => $toState,
            'to_county' => $toCounty,
            'park_name' => $parkName,
            'park_reference' => $parkReference,
            'park_location' => $parkLocation,
            'display_location' => $this->displayLocation(
                $toCity,
                $toState,
                $toCountry,
                $parkName,
                $parkLocation,
            ),
            'qso_date' => $qsoTimestamp?->format('Y-m-d H:i:s'),
            'age_days' => $ageDays,
            'recency_score' => $this->scoring->recencyScore($ageDays),
        ]);

        $coordinates = $this->extractCoordinates();

        $bearing = null;
        if ($coordinates !== null) {
            $bearing = $this->geography->bearing(...$coordinates);
        }
        $value['bearing_degrees'] = $bearing;
        $value['bearing_cardinal'] = $bearing !== null ? $this->geography->cardinalDirection($bearing) : null;

        // Prefer the stored distance; fall back to estimating it from
        // coordinates when possible (mirrors the pre-refactor behavior).
        $storedDistance = $this->resource->getAttribute('distance');
        if (is_numeric($storedDistance) && (float) $storedDistance >= 0) {
            $value['distance'] = (int) round((float) $storedDistance);
            $value['distance_estimated'] = false;
        } elseif ($coordinates !== null) {
            $value['distance'] = (int) round($this->geography->haversineMiles(...$coordinates));
            $value['distance_estimated'] = true;
        } else {
            $value['distance'] = null;
            $value['distance_estimated'] = false;
        }

        $value['icon_size'] = 0.025;
        $value['icon'] = 'pin';
        if ($this->category === 'POTA') {
            $value['icon'] = 'tree';
            $value['icon_size'] = 0.25;
        }

        unset(
            $value['station'],
            $value['callee'],
            $value['park'],
            $value['qrz_logid'],
            $value['entry_key'],
            $value['hidden_from_public'],
            $value['from_callsign'],
            $value['from_grid'],
            $value['from_coordinates'],
            $value['from_latitude'],
            $value['from_longitude'],
        );

        return $value;
    }

    private function extractCoordinates(): ?array
    {
        $fromLatitude = $this->coordinate('from_latitude', -90, 90)
            ?? $this->fallbackOriginCoordinate('latitude', -90, 90);
        $fromLongitude = $this->coordinate('from_longitude', -180, 180)
            ?? $this->fallbackOriginCoordinate('longitude', -180, 180);
        $toLatitude = $this->coordinate('to_latitude', -90, 90);
        $toLongitude = $this->coordinate('to_longitude', -180, 180);

        if (
            $fromLatitude === null
            || $fromLongitude === null
            || $toLatitude === null
            || $toLongitude === null
        ) {
            return null;
        }

        return [$fromLatitude, $fromLongitude, $toLatitude, $toLongitude];
    }

    private function coordinate(string $attribute, float $minimum, float $maximum): ?float
    {
        return $this->normalizeCoordinate($this->resource->getAttribute($attribute), $minimum, $maximum);
    }

    private function fallbackOriginCoordinate(string $attribute, float $minimum, float $maximum): ?float
    {
        try {
            $value = config("radio.origin.{$attribute}");
        } catch (Throwable) {
            $value = null;
        }

        return $this->normalizeCoordinate($value, $minimum, $maximum);
    }

    private function normalizeCoordinate(mixed $value, float $minimum, float $maximum): ?float
    {
        if (! is_numeric($value)) {
            return null;
        }

        $coordinate = (float) $value;

        if ($coordinate < $minimum || $coordinate > $maximum) {
            return null;
        }

        return $coordinate;
    }

    private function qsoTimestamp(): ?CarbonInterface
    {
        $value = $this->resource->getAttribute('qso_date') ?: $this->created_at;

        if ($value instanceof CarbonInterface) {
            return $value;
        }

        if (empty($value)) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (Throwable) {
            return null;
        }
    }

    private function displayLocation(
        ?string $city,
        ?string $state,
        ?string $country,
        ?string $parkName,
        ?string $parkLocation,
    ): ?string {
        if ($city !== null && $state !== null) {
            return "{$city}, {$state}";
        }

        if ($city !== null && $country !== null) {
            return "{$city}, {$country}";
        }

        if ($parkName !== null && $parkLocation !== null) {
            return "{$parkName}, {$parkLocation}";
        }

        if ($parkName !== null) {
            return $parkName;
        }

        if ($state !== null && $country !== null) {
            return "{$state}, {$country}";
        }

        return $state ?? $country ?? $parkLocation;
    }

    private function park(): ?POTAPark
    {
        if ($this->resource instanceof LogbookEntry) {
            return $this->resource->relationLoaded('park')
                ? $this->resource->getRelation('park')
                : $this->resource->park;
        }

        $park = $this->resource->getAttribute('park');

        return $park instanceof POTAPark ? $park : null;
    }

    private function stringAttribute(string $attribute): ?string
    {
        return $this->stringValue($this->resource->getAttribute($attribute));
    }

    private function stringValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}

