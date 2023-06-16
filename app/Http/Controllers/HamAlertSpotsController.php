<?php

namespace App\Http\Controllers;

use App\Http\Requests\HamAlertSpotStoreRequest;
use App\Models\HamAlertSpot;
use Carbon\Carbon;

class HamAlertSpotsController extends Controller
{
    /**
     * @param HamAlertSpotStoreRequest $request
     * @return HamAlertSpot
     */
    public function save(HamAlertSpotStoreRequest $request)
    {
        $spot = new HamAlertSpot();
        $spot->callsign = $request->input('callsign');
        $spot->frequency = $request->input('frequency');
        $spot->mode = $request->input('modeDetail');
        $spot->band = $request->input('band');
        $spot->spotter_entity = $request->input('spotterEntity');
        $spot->spotter_callsign = $request->input('spotter');
        $spot->created_at = Carbon::parse($request->input('time'), 'UTC');
        $spot->save();
        return $spot;
    }

    public function index()
    {
        $records = HamAlertSpot::select('callsign')->distinct()->where('created_at', '>=', now()->subDay())->get();
        $spots = collect();
        foreach ($records as $record) {
            $latest = HamAlertSpot::whereCallsign($record->callsign)->latest()->first();
            $spots[$record->callsign] = $latest;
        }
        return $spots->sortByDesc('created_at')->transform(function ($spot) {
            return ['summary' => $this->formatSpot($spot), 'latest_spot' => $spot->toArray()];
        });
    }

    private function formatSpot(HamAlertSpot $spot)
    {
        return <<<EOL
        {$spot->callsign} was last spotted by {$spot->spotter_callsign} on {$spot->mode} at {$spot->created_at->toTimeString()} UTC ({$spot->created_at->diffForHumans()})
        EOL;
    }
}
