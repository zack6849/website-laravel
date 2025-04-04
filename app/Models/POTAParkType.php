<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\POTAParkType
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder<static>|POTAParkType newModelQuery()
 * @method static Builder<static>|POTAParkType newQuery()
 * @method static Builder<static>|POTAParkType query()
 * @method static Builder<static>|POTAParkType whereCreatedAt($value)
 * @method static Builder<static>|POTAParkType whereId($value)
 * @method static Builder<static>|POTAParkType whereName($value)
 * @method static Builder<static>|POTAParkType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class POTAParkType extends Model
{
    protected $guarded = [];
    protected $table = 'pota_park_types';
}
