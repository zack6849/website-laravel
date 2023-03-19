<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\LogbookEntry;
use GeoJson\Feature\Feature;
use GeoJson\Geometry\Point;

class LogbookController extends Controller
{
    public function qsoGeoJson($band = '20m', $mode = 'SSB'){
        $features = [];
        LogbookEntry::where('band', $band)
            ->with(['station', 'callee'])
            ->where('mode', $mode)->get()->each(function(LogbookEntry $entry) use (&$features){
                $location = new Point([floatval($entry->to_longitude), floatval($entry->to_latitude)]);
                $features[] = new Feature($location, [
                    'description' => view('components.partials.qso-data', ['entry' => $entry])->render()
                ]);

            });
        return [
            'type' => 'FeatureCollection',
            'features' => $features
        ];
    }
}
