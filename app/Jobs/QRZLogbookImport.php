<?php

namespace App\Jobs;

use App\Models\Callsign;
use App\Models\LogbookEntry;
use App\Models\POTAPark;
use App\Providers\MainheadGridResolutionServiceProvider;
use App\Providers\ParksOnTheAirServiceProvider;
use App\Providers\QRZLogbookProvider;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class QRZLogbookImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private QRZLogbookProvider $logbookProvider;
    private ParksOnTheAirServiceProvider $potaProvider;
    private MainheadGridResolutionServiceProvider $gridProvider;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        MainheadGridResolutionServiceProvider $mainheadGridResolutionServiceProvider,
        QRZLogbookProvider                    $logbookProvider,
        ParksOnTheAirServiceProvider          $parksOnTheAirServiceProvider
    )
    {
        $this->logbookProvider = $logbookProvider;
        $this->potaProvider = $parksOnTheAirServiceProvider;
        $this->gridProvider = $mainheadGridResolutionServiceProvider;
        $records = $this->logbookProvider->getLogbookEntries();
        LogbookEntry::query()->whereNotNull('created_at')->delete();
        foreach ($records as $record) {
            $this->saveRecord($record);
        }
    }

    private function saveRecord(array $record) : void
    {
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
        $grid = $this->getGridsquare($record);
        list($theirLat, $theirLon) = $this->getLatLong($record);
        $entry = LogbookEntry::make([
            'from_callsign' => $myCall->id,
            'to_callsign' => $theirCall->id,
            'frequency' => Arr::get($record, 'FREQ'),
            'band' => strtoupper(Arr::get($record, 'BAND')),
            'mode' => strtoupper(Arr::get($record, 'MODE')),
            'rst_sent' => Arr::get($record, 'RST_SENT'),
            'rst_received' => Arr::get($record, 'RST_RCVD'),
            'from_grid' => Arr::get($record, 'MY_GRIDSQUARE'),
            'from_coordinates' => implode(',', [$myLat, $myLon]),
            'from_latitude' => $myLat,
            'from_longitude' => $myLon,
            'to_grid' => $grid,
            'to_coordinates' => implode(',', [$theirLat, $theirLon]),
            'to_latitude' => $theirLat,
            'to_longitude' => $theirLon,
            'distance' => Arr::get($record, 'DISTANCE'),
            'comments' => Arr::get($record, 'COMMENT')
        ]);
        $timestamp = Carbon::createFromFormat('YmdHi', $record['QSO_DATE'] . $record['TIME_ON']);
        $entry->created_at = $timestamp;
        $park = $this->getRelatedPark($record);
        if($park !== null){
            $entry->park_id = $park->id;
            $entry->category = "POTA";
        }
        $entry->save();
    }

    public function transformCoordinates($coordinates): string
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

    private function getGridSquare(array $adifEntry) : string
    {
        $grid = Arr::get($adifEntry, 'GRIDSQUARE', '');
        $park = $this->getRelatedPark($adifEntry);
        if($park !== null){
            if (!empty(trim($park->grid4))) {
                $grid = $park->grid4;
            }
            if (!empty(trim($park->grid6))) {
                $grid = $park->grid6;
            }
        }
        return $grid;
    }

    private function getLatLong(array $record) : array
    {
        $lat = $this->transformCoordinates(Arr::get($record, 'LAT'));
        $lon = $this->transformCoordinates(Arr::get($record, 'LON'));
        $park = $this->getRelatedPark($record);
        if ($park !== null) {
            $lat = $park->latitude;
            $lon = $park->longitude;
        }
        return [$lat, $lon];
    }

    public function getRelatedPark(array $adifEntry){
        $parks = $this->getParks($adifEntry);
        if(!empty($parks)){
            return array_shift($parks);
        }
        return null;
    }

    /**
     * @param array $adifEntry
     * @return POTAPark[] a list of parks in the comments field
     */
    public function getParks(array $adifEntry) : array
    {
        $comment = Arr::get($adifEntry, 'COMMENT', '');
        return $this->findValidParks($comment);
    }

    private function findValidParks(string $comment) : array
    {
        $realParks = [];
        if (preg_match('/([a-zA-Z]{1,}-\d+)/', $comment, $matches)) {
            $references = array_unique($matches);
            foreach ($references as $reference) {
                $park = $this->potaProvider->getParkInfo($reference);
                if ($park !== false) {
                    $realParks[] = $park;
                }
            }
        }
        return $realParks;
    }
}
