<?php

namespace App\Http\Controllers;

use App\Http\Requests\HamAlertSpotStoreRequest;
use App\Jobs\PostHamAlertSpotToDiscordJob;
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
        dispatch(new PostHamAlertSpotToDiscordJob($spot));
        return $spot;
    }

    public function index()
    {
        $spots = HamAlertSpot::where('created_at', '>=', now()->subDay())
            ->orderByDesc('created_at')
            ->get()
            ->unique('callsign')
            ->values();
        return $spots->transform(function ($spot) {
            return ['summary' => $this->formatSpot($spot), 'latest_spot' => $spot->toArray()];
        });
    }

    private function formatSpot(HamAlertSpot $spot)
    {
        return <<<EOL
        {$spot->callsign} was spotted by {$spot->spotter_callsign} on {$spot->band} {$spot->mode} on {$spot->frequency} <t:{$spot->created_at->unix()}:R>
        EOL;
    }
}
