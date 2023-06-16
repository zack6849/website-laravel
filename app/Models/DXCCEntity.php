<?php
declare(strict_types=1);

namespace App\Models;

use Database\Factories\DXCCEntityFactory;
use Illuminate\Database\Eloquent\Builder;
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
 * @method static DXCCEntityFactory factory($count = null, $state = [])
 * @method static Builder|DXCCEntity newModelQuery()
 * @method static Builder|DXCCEntity newQuery()
 * @method static Builder|DXCCEntity query()
 * @method static Builder|DXCCEntity whereCountryCode($value)
 * @method static Builder|DXCCEntity whereCreatedAt($value)
 * @method static Builder|DXCCEntity whereId($value)
 * @method static Builder|DXCCEntity whereName($value)
 * @method static Builder|DXCCEntity whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DXCCEntity extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $table = 'dxcc_entities';
}
