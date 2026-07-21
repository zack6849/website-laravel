<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\LogbookEntry;
use Carbon\CarbonInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class LogbookEntryIdentity
{
    public function forEntry(LogbookEntry $entry): string
    {
        $entry->loadMissing(['station', 'callee']);

        return $this->forValues(
            $entry->station?->name,
            $entry->callee?->name,
            $entry->created_at,
            $entry->band,
            $entry->mode,
        );
    }

    public function forRecord(array $record): string
    {
        $timestamp = Carbon::createFromFormat(
            'YmdHi',
            Arr::get($record, 'QSO_DATE') . Arr::get($record, 'TIME_ON')
        );

        return $this->forValues(
            Arr::get($record, 'STATION_CALLSIGN'),
            Arr::get($record, 'CALL'),
            $timestamp,
            Arr::get($record, 'BAND'),
            Arr::get($record, 'MODE'),
        );
    }

    private function forValues(
        ?string $stationCallsign,
        ?string $contactCallsign,
        CarbonInterface|string|null $timestamp,
        ?string $band,
        ?string $mode,
    ): string {
        if (! $timestamp instanceof CarbonInterface) {
            $timestamp = Carbon::parse($timestamp);
        }

        return hash('sha256', implode('|', [
            strtoupper(trim((string) $stationCallsign)),
            strtoupper(trim((string) $contactCallsign)),
            $timestamp->format('Y-m-d H:i'),
            strtoupper(trim((string) $band)),
            strtoupper(trim((string) $mode)),
        ]));
    }
}
