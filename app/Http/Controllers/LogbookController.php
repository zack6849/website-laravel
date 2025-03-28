<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\LogbookEntryResource;
use App\Models\LogbookEntry;
use GeoJson\Feature\Feature;
use GeoJson\Geometry\Point;
use Illuminate\Support\Collection;

class LogbookController extends Controller
{
    public function getGeoJSON($band = '20m', $mode = 'SSB'): array
    {
        $features = [];
        LogbookEntry::where('band', $band)
            ->with(['station', 'callee'])
            ->where('mode', $mode)->get()->each(function (LogbookEntry $entry) use (&$features) {
                $location = new Point([floatval($entry->to_longitude), floatval($entry->to_latitude)]);
                $resource = new LogbookEntryResource($entry);
                $features[] = new Feature($location, [
                    'description' => view('components.partials.qso-data', ['entry' => $entry])->render(),
                    ... $resource->toArray(request())]);

            });
        return [
            'type' => 'FeatureCollection',
            'features' => $features
        ];
    }

    public function getWorkedModes(): Collection
    {
        return LogbookEntry::select('mode')->distinct()->get()->pluck('mode');
    }

    public function getWorkedBands(): Collection
    {
        return LogbookEntry::select('band')->distinct()->get()->pluck('band');
    }
}
