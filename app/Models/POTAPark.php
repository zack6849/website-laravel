<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\POTAPark
 *
 * @method static Builder|POTAPark newModelQuery()
 * @method static Builder|POTAPark newQuery()
 * @method static Builder|POTAPark query()
 * @property int $id
 * @property string $name
 * @property string $reference
 * @property string $latitude
 * @property string $longitude
 * @property string $grid4
 * @property string $grid6
 * @property int $park_type_id
 * @property int $active
 * @property string $comments
 * @property string $location
 * @property string $raw_data
 * @property string|null $first_activation_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|POTAPark whereActive($value)
 * @method static Builder|POTAPark whereComments($value)
 * @method static Builder|POTAPark whereCreatedAt($value)
 * @method static Builder|POTAPark whereFirstActivationAt($value)
 * @method static Builder|POTAPark whereGrid4($value)
 * @method static Builder|POTAPark whereGrid6($value)
 * @method static Builder|POTAPark whereId($value)
 * @method static Builder|POTAPark whereLatitude($value)
 * @method static Builder|POTAPark whereLocation($value)
 * @method static Builder|POTAPark whereLongitude($value)
 * @method static Builder|POTAPark whereName($value)
 * @method static Builder|POTAPark whereParkTypeId($value)
 * @method static Builder|POTAPark whereRawData($value)
 * @method static Builder|POTAPark whereReference($value)
 * @method static Builder|POTAPark whereUpdatedAt($value)
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
