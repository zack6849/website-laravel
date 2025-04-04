<?php

declare(strict_types=1);

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
 * @property int|null $park_id
 * @property string $category
 * @property-read Callsign $callee
 * @property-read Callsign $station
 * @method static LogbookEntryFactory factory($count = null, $state = [])
 * @method static Builder<static>|LogbookEntry newModelQuery()
 * @method static Builder<static>|LogbookEntry newQuery()
 * @method static Builder<static>|LogbookEntry query()
 * @method static Builder<static>|LogbookEntry whereBand($value)
 * @method static Builder<static>|LogbookEntry whereCategory($value)
 * @method static Builder<static>|LogbookEntry whereComments($value)
 * @method static Builder<static>|LogbookEntry whereCreatedAt($value)
 * @method static Builder<static>|LogbookEntry whereDistance($value)
 * @method static Builder<static>|LogbookEntry whereFrequency($value)
 * @method static Builder<static>|LogbookEntry whereFromCallsign($value)
 * @method static Builder<static>|LogbookEntry whereFromCoordinates($value)
 * @method static Builder<static>|LogbookEntry whereFromGrid($value)
 * @method static Builder<static>|LogbookEntry whereFromLatitude($value)
 * @method static Builder<static>|LogbookEntry whereFromLongitude($value)
 * @method static Builder<static>|LogbookEntry whereId($value)
 * @method static Builder<static>|LogbookEntry whereMode($value)
 * @method static Builder<static>|LogbookEntry whereParkId($value)
 * @method static Builder<static>|LogbookEntry whereRstReceived($value)
 * @method static Builder<static>|LogbookEntry whereRstSent($value)
 * @method static Builder<static>|LogbookEntry whereToCallsign($value)
 * @method static Builder<static>|LogbookEntry whereToCoordinates($value)
 * @method static Builder<static>|LogbookEntry whereToGrid($value)
 * @method static Builder<static>|LogbookEntry whereToLatitude($value)
 * @method static Builder<static>|LogbookEntry whereToLongitude($value)
 * @method static Builder<static>|LogbookEntry whereUpdatedAt($value)
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
