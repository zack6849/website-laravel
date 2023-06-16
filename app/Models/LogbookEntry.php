<?php

namespace App\Models;

use Database\Factories\LogbookEntryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Models\Callsign $callee
 * @property-read \App\Models\Callsign $station
 * @method static LogbookEntryFactory factory($count = null, $state = [])
 * @method static Builder|LogbookEntry newModelQuery()
 * @method static Builder|LogbookEntry newQuery()
 * @method static Builder|LogbookEntry query()
 * @method static Builder|LogbookEntry whereBand($value)
 * @method static Builder|LogbookEntry whereComments($value)
 * @method static Builder|LogbookEntry whereCreatedAt($value)
 * @method static Builder|LogbookEntry whereDistance($value)
 * @method static Builder|LogbookEntry whereFrequency($value)
 * @method static Builder|LogbookEntry whereFromCallsign($value)
 * @method static Builder|LogbookEntry whereFromCoordinates($value)
 * @method static Builder|LogbookEntry whereFromGrid($value)
 * @method static Builder|LogbookEntry whereFromLatitude($value)
 * @method static Builder|LogbookEntry whereFromLongitude($value)
 * @method static Builder|LogbookEntry whereId($value)
 * @method static Builder|LogbookEntry whereMode($value)
 * @method static Builder|LogbookEntry whereRstReceived($value)
 * @method static Builder|LogbookEntry whereRstSent($value)
 * @method static Builder|LogbookEntry whereToCallsign($value)
 * @method static Builder|LogbookEntry whereToCoordinates($value)
 * @method static Builder|LogbookEntry whereToGrid($value)
 * @method static Builder|LogbookEntry whereToLatitude($value)
 * @method static Builder|LogbookEntry whereToLongitude($value)
 * @method static Builder|LogbookEntry whereUpdatedAt($value)
 * @property int|null $park_id
 * @property string $category
 * @method static Builder|LogbookEntry whereCategory($value)
 * @method static Builder|LogbookEntry whereParkId($value)
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
