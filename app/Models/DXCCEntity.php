<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DXCCEntity
 *
 * @property int $id
 * @property string $name
 * @property string $country_code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\DXCCEntityFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|DXCCEntity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DXCCEntity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DXCCEntity query()
 * @method static \Illuminate\Database\Eloquent\Builder|DXCCEntity whereCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DXCCEntity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DXCCEntity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DXCCEntity whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DXCCEntity whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DXCCEntity extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $table = 'dxcc_entities';
}
