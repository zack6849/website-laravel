<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\POTAPark
 *
 * @property int $id
 * @property string $name
 * @property string $reference
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string|null $grid4
 * @property string|null $grid6
 * @property int $park_type_id
 * @property int $active
 * @property string|null $comments
 * @property string|null $location
 * @property string $raw_data Raw data from the POTA API
 * @property Carbon|null $first_activation_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder<static>|POTAPark newModelQuery()
 * @method static Builder<static>|POTAPark newQuery()
 * @method static Builder<static>|POTAPark query()
 * @method static Builder<static>|POTAPark whereActive($value)
 * @method static Builder<static>|POTAPark whereComments($value)
 * @method static Builder<static>|POTAPark whereCreatedAt($value)
 * @method static Builder<static>|POTAPark whereFirstActivationAt($value)
 * @method static Builder<static>|POTAPark whereGrid4($value)
 * @method static Builder<static>|POTAPark whereGrid6($value)
 * @method static Builder<static>|POTAPark whereId($value)
 * @method static Builder<static>|POTAPark whereLatitude($value)
 * @method static Builder<static>|POTAPark whereLocation($value)
 * @method static Builder<static>|POTAPark whereLongitude($value)
 * @method static Builder<static>|POTAPark whereName($value)
 * @method static Builder<static>|POTAPark whereParkTypeId($value)
 * @method static Builder<static>|POTAPark whereRawData($value)
 * @method static Builder<static>|POTAPark whereReference($value)
 * @method static Builder<static>|POTAPark whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class POTAPark extends Model
{
    protected $table = 'pota_parks';
    protected $guarded = [];
    protected $casts = [
        'first_activation_at' => 'date',
    ];
}
