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
 * @property string|null $qrz_logid
 * @property string|null $entry_key
 * @property int $from_callsign
 * @property int $to_callsign
 * @property string|null $to_city
 * @property string|null $to_state
 * @property string|null $to_county
 * @property float $frequency
 * @property string $band
 * @property string $mode
 * @property string|null $rst_sent
 * @property string|null $rst_received
 * @property string $from_grid
 * @property string|null $from_coordinates
 * @property string|null $from_latitude
 * @property string|null $from_longitude
 * @property string|null $to_grid
 * @property string|null $to_coordinates
 * @property string|null $to_latitude
 * @property string|null $to_longitude
 * @property int|null $distance distance in miles
 * @property string|null $comments
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $park_id
 * @property string $category
 * @property bool $hidden_from_public
 * @property-read Callsign $callee
 * @property-read POTAPark|null $park
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
 * @method static Builder<static>|LogbookEntry whereToCity($value)
 * @method static Builder<static>|LogbookEntry whereToCounty($value)
 * @method static Builder<static>|LogbookEntry whereToGrid($value)
 * @method static Builder<static>|LogbookEntry whereToLatitude($value)
 * @method static Builder<static>|LogbookEntry whereToLongitude($value)
 * @method static Builder<static>|LogbookEntry whereToState($value)
 * @method static Builder<static>|LogbookEntry whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LogbookEntry extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'hidden_from_public' => 'boolean',
    ];

    public function station(): BelongsTo
    {
        return $this->belongsTo(Callsign::class, 'from_callsign');
    }

    public function callee(): BelongsTo
    {
        return $this->belongsTo(Callsign::class, 'to_callsign');
    }

    public function park(): BelongsTo
    {
        return $this->belongsTo(POTAPark::class, 'park_id');
    }

    /**
     * @param Builder<LogbookEntry> $query
     */
    public function scopeVisibility(Builder $query, string $visibility): Builder
    {
        return $query
            ->when($visibility === 'hidden', fn (Builder $query) => $query->where('hidden_from_public', true))
            ->when($visibility === 'public', fn (Builder $query) => $query->where('hidden_from_public', false));
    }

    /**
     * @param Builder<LogbookEntry> $query
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        $search = trim($search);

        if ($search === '') {
            return $query;
        }

        $like = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $search) . '%';

        return $query->where(function (Builder $query) use ($like): void {
            $query->where('qrz_logid', 'like', $like)
                ->orWhere('band', 'like', $like)
                ->orWhere('mode', 'like', $like)
                ->orWhere('to_grid', 'like', $like)
                ->orWhere('comments', 'like', $like)
                ->orWhereHas('callee', function (Builder $query) use ($like): void {
                    $query->where('name', 'like', $like)
                        ->orWhere('country', 'like', $like);
                });
        });
    }
}
