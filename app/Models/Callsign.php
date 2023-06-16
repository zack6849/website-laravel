<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Callsign
 *
 * @property int $id
 * @property string $name
 * @property string $country
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\CallsignFactory factory($count = null, $state = [])
 * @method static Builder|Callsign newModelQuery()
 * @method static Builder|Callsign newQuery()
 * @method static Builder|Callsign query()
 * @method static Builder|Callsign whereCountry($value)
 * @method static Builder|Callsign whereCreatedAt($value)
 * @method static Builder|Callsign whereId($value)
 * @method static Builder|Callsign whereName($value)
 * @method static Builder|Callsign whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Callsign extends Model
{
    use HasFactory;
    protected $guarded = [];
}
