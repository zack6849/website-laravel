<?php

namespace App\Http\Controllers;

use App\Http\Requests\HamAlertSpotStoreRequest;
use App\Models\HamAlertSpot;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

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
        Http::retry(3, 100)->post(config('services.discord.webhook_uri'), [
            'content' => $this->formatSpot($spot)
        ]);
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
        {$spot->callsign} was spotted by {$spot->spotter_callsign} on {$spot->band} {$spot->mode} on {$spot->frequency} <t:{$spot->created_at->unix()}:R>
        EOL;
    }
}
