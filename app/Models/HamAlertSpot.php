<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\HamAlertSpot
 *
 * @method static Builder|HamAlertSpot newModelQuery()
 * @method static Builder|HamAlertSpot newQuery()
 * @method static Builder|HamAlertSpot query()
 * @property int $id
 * @property string $callsign
 * @property string $frequency
 * @property string $band
 * @property string $mode
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|HamAlertSpot whereBand($value)
 * @method static Builder|HamAlertSpot whereCallsign($value)
 * @method static Builder|HamAlertSpot whereCreatedAt($value)
 * @method static Builder|HamAlertSpot whereFrequency($value)
 * @method static Builder|HamAlertSpot whereId($value)
 * @method static Builder|HamAlertSpot whereMode($value)
 * @method static Builder|HamAlertSpot whereUpdatedAt($value)
 * @property string $spotter_callsign
 * @property string $spotter_entity
 * @method static Builder|HamAlertSpot whereSpotterCallsign($value)
 * @method static Builder|HamAlertSpot whereSpotterEntity($value)
 * @mixin \Eloquent
 */
class HamAlertSpot extends Model
{
    protected $guarded = [];
}
