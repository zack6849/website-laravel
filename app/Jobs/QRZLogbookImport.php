<?php

namespace App\Jobs;

use App\Models\Callsign;
use App\Models\LogbookEntry;
use App\Providers\MainheadGridResolutionServiceProvider;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use j4nr6n\ADIF\Parser;

class QRZLogbookImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(MainheadGridResolutionServiceProvider $mainheadGridResolutionServiceProvider)
    {
        $response = Http::asForm()->post('https://logbook.qrz.com/api', [
            'KEY' => config('services.qrz.key'),
            'ACTION' => 'FETCH'
        ]);
        $responseText = str_replace(["&lt;", "&gt;", "\n"], ["<", ">", ""], $response->body());
        $responseText = preg_replace("/&(?![^\s=]+=[^\s=])/m", "{AMP}", $responseText);
        $responseText = htmlspecialchars_decode($responseText);
        $data = [];
        parse_str($responseText, $data);
        $adifData = str_replace('{AMP}', '&', $data['ADIF']);
        $records = (new Parser())->parse($adifData);
        LogbookEntry::query()->whereNotNull('created_at')->delete();
        foreach ($records as $record) {
            $theirCall = Callsign::query()->firstOrCreate([
                'name' => $record['CALL']
            ], [
                'country' => $record['COUNTRY']
            ]);
            $myCall = Callsign::firstOrCreate([
                'name' => $record['STATION_CALLSIGN']
            ], [
                'country' => $record['MY_COUNTRY']
            ]);
            $myLat = $this->transformCoordinates(Arr::get($record, 'MY_LAT'));
            $myLon = $this->transformCoordinates(Arr::get($record, 'MY_LON'));
            $theirLat = $this->transformCoordinates(Arr::get($record, 'LAT'));
            $theirLon = $this->transformCoordinates(Arr::get($record, 'LON'));
            $grid = Arr::get($record, 'GRIDSQUARE');
            if (strlen($grid) >= 4) {
                list($theirLat, $theirLon) = $mainheadGridResolutionServiceProvider->getGridSquare($grid);
            }
            $entry = LogbookEntry::make([
                'from_callsign' => $myCall->id,
                'to_callsign' => $theirCall->id,
                'frequency' => Arr::get($record, 'FREQ'),
                'band' => Arr::get($record, 'BAND'),
                'mode' => Arr::get($record, 'MODE'),
                'rst_sent' => Arr::get($record, 'RST_SENT'),
                'rst_received' => Arr::get($record, 'RST_RCVD'),
                'from_grid' => Arr::get($record, 'MY_GRIDSQUARE'),
                'from_coordinates' => implode(',', [$myLat, $myLon]),
                'from_latitude' => $myLat,
                'from_longitude' => $myLon,
                'to_grid' => Arr::get($record, 'GRIDSQUARE'),
                'to_coordinates' => implode(',', [$theirLat, $theirLon]),
                'to_latitude' => $theirLat,
                'to_longitude' => $theirLon,
                'distance' => Arr::get($record, 'DISTANCE'),
                'comments' => Arr::get($record, 'COMMENT')
            ]);
            $timestamp = Carbon::createFromFormat('YmdHi', $record['QSO_DATE'] . $record['TIME_ON']);
            $entry->created_at = $timestamp;
            $entry->save();
        }

    }

    public function transformCoordinates($coordinates)
    {
        $matches = [];
        preg_match('/([NSEW])(\d+)\s(\d+)\.?(\d+)/', $coordinates, $matches);
        array_shift($matches);
        $direction = array_shift($matches);
        if (in_array($direction, ['W', 'S'])) {
            $prefix = "-";
        } else {
            $prefix = '';
        }
        $mainLocator = array_shift($matches);
        return number_format("$prefix$mainLocator." . implode('', $matches), 5, '.', '');
    }
}
