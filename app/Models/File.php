<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\File
 *
 * @method static Builder|File newModelQuery()
 * @method static Builder|File newQuery()
 * @method static Builder|File query()
 * @property int $id
 * @property int $user_id
 * @property string $file_location
 * @property string $original_filename
 * @property string $mime
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|File whereCreatedAt($value)
 * @method static Builder|File whereFileLocation($value)
 * @method static Builder|File whereId($value)
 * @method static Builder|File whereMime($value)
 * @method static Builder|File whereOriginalFilename($value)
 * @method static Builder|File whereUpdatedAt($value)
 * @method static Builder|File whereUserId($value)
 * @property string $filename
 * @property int $size
 * @property-read User $user
 * @method static Builder|File search($search)
 * @method static Builder|File whereFilename($value)
 * @method static Builder|File whereSize($value)
 * @method static Builder|File forUser($user)
 * @method static \Database\Factories\FileFactory factory($count = null, $state = [])
 * @mixin \Eloquent
 */
class File extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
        'user_id',
        'file_location',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeSearch(Builder $builder, $search): Builder
    {
        return $builder->where('filename', 'like', "%$search%")
            ->orWhere('original_filename', 'like', "%$search%")
            ->orWhere('mime', 'like', "%$search%");
    }

    public function scopeForUser(Builder $builder, $user): Builder
    {
        return $builder->where('user_id', '=', $user->id);
    }
}
