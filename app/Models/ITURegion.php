<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ITURegion
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\ITURegionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ITURegion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ITURegion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ITURegion query()
 * @method static \Illuminate\Database\Eloquent\Builder|ITURegion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ITURegion whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ITURegion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ITURegion whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ITURegion whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ITURegion extends Model
{
    use HasFactory;

    protected $table = 'itu_regions';
}
