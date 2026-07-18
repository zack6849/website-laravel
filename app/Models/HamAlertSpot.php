<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\HamAlertSpot
 *
 * @property int $id
 * @property string $callsign
 * @property string $spotter_callsign
 * @property string $frequency
 * @property string $band
 * @property string $spotter_entity
 * @property string $mode
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder<static>|HamAlertSpot newModelQuery()
 * @method static Builder<static>|HamAlertSpot newQuery()
 * @method static Builder<static>|HamAlertSpot query()
 * @method static Builder<static>|HamAlertSpot whereBand($value)
 * @method static Builder<static>|HamAlertSpot whereCallsign($value)
 * @method static Builder<static>|HamAlertSpot whereCreatedAt($value)
 * @method static Builder<static>|HamAlertSpot whereFrequency($value)
 * @method static Builder<static>|HamAlertSpot whereId($value)
 * @method static Builder<static>|HamAlertSpot whereMode($value)
 * @method static Builder<static>|HamAlertSpot whereSpotterCallsign($value)
 * @method static Builder<static>|HamAlertSpot whereSpotterEntity($value)
 * @method static Builder<static>|HamAlertSpot whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class HamAlertSpot extends Model
{
    use HasFactory;

    protected $guarded = [];
}
