<?php

namespace App\Models;

use Database\Factories\ITURegionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\ITURegion
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static ITURegionFactory factory($count = null, $state = [])
 * @method static Builder|ITURegion newModelQuery()
 * @method static Builder|ITURegion newQuery()
 * @method static Builder|ITURegion query()
 * @method static Builder|ITURegion whereCreatedAt($value)
 * @method static Builder|ITURegion whereDescription($value)
 * @method static Builder|ITURegion whereId($value)
 * @method static Builder|ITURegion whereName($value)
 * @method static Builder|ITURegion whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ITURegion extends Model
{
    use HasFactory;

    protected $table = 'itu_regions';
}
