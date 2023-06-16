<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\POTAParkType
 *
 * @method static Builder|POTAParkType newModelQuery()
 * @method static Builder|POTAParkType newQuery()
 * @method static Builder|POTAParkType query()
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|POTAParkType whereCreatedAt($value)
 * @method static Builder|POTAParkType whereId($value)
 * @method static Builder|POTAParkType whereName($value)
 * @method static Builder|POTAParkType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class POTAParkType extends Model
{
    protected $guarded = [];
    protected $table = 'pota_park_types';
}
