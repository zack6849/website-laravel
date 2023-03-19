<?php

namespace App\Models;

use Grpc\Call;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\LogbookEntry
 *
 * @property int $id
 * @property int $from_callsign
 * @property int $to_callsign
 * @property float $frequency
 * @property string $band
 * @property string $mode
 * @property string|null $rst_sent
 * @property string|null $rst_received
 * @property string $from_grid
 * @property string $from_coordinates
 * @property string $from_latitude
 * @property string $from_longitude
 * @property string|null $to_grid
 * @property string|null $to_coordinates
 * @property string $to_latitude
 * @property string $to_longitude
 * @property int|null $distance distance in miles
 * @property string|null $comments
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Callsign $callee
 * @property-read \App\Models\Callsign $station
 * @method static \Database\Factories\LogbookEntryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|LogbookEntry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LogbookEntry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LogbookEntry query()
 * @method static \Illuminate\Database\Eloquent\Builder|LogbookEntry whereBand($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogbookEntry whereComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogbookEntry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogbookEntry whereDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogbookEntry whereFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogbookEntry whereFromCallsign($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogbookEntry whereFromCoordinates($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogbookEntry whereFromGrid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogbookEntry whereFromLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogbookEntry whereFromLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogbookEntry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogbookEntry whereMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogbookEntry whereRstReceived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogbookEntry whereRstSent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogbookEntry whereToCallsign($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogbookEntry whereToCoordinates($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogbookEntry whereToGrid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogbookEntry whereToLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogbookEntry whereToLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LogbookEntry whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LogbookEntry extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function station(): BelongsTo
    {
        return $this->belongsTo(Callsign::class, 'from_callsign');
    }

    public function callee(): BelongsTo
    {
        return $this->belongsTo(Callsign::class, 'to_callsign');
    }
}
