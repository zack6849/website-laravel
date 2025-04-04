<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CallsignFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Callsign
 *
 * @property int $id
 * @property string $name
 * @property string $country
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static CallsignFactory factory($count = null, $state = [])
 * @method static Builder<static>|Callsign newModelQuery()
 * @method static Builder<static>|Callsign newQuery()
 * @method static Builder<static>|Callsign query()
 * @method static Builder<static>|Callsign whereCountry($value)
 * @method static Builder<static>|Callsign whereCreatedAt($value)
 * @method static Builder<static>|Callsign whereId($value)
 * @method static Builder<static>|Callsign whereName($value)
 * @method static Builder<static>|Callsign whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Callsign extends Model
{
    use HasFactory;
    protected $guarded = [];
}
