<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\DXCCEntityFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\DXCCEntity
 *
 * @property int $id
 * @property string $name
 * @property string $country_code
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static DXCCEntityFactory factory($count = null, $state = [])
 * @method static Builder<static>|DXCCEntity newModelQuery()
 * @method static Builder<static>|DXCCEntity newQuery()
 * @method static Builder<static>|DXCCEntity query()
 * @method static Builder<static>|DXCCEntity whereCountryCode($value)
 * @method static Builder<static>|DXCCEntity whereCreatedAt($value)
 * @method static Builder<static>|DXCCEntity whereId($value)
 * @method static Builder<static>|DXCCEntity whereName($value)
 * @method static Builder<static>|DXCCEntity whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DXCCEntity extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $table = 'dxcc_entities';
}
