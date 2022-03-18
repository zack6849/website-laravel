<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\File
 *
 * @method static Builder|File newModelQuery()
 * @method static Builder|File newQuery()
 * @method static Builder|File query()
 * @mixin \Eloquent
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
 */
class File extends Model
{

    protected $guarded = [
        'id',
        'user_id',
        'file_location',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function scopeSearch(Builder $builder, $search){
        return $builder->where('filename', 'like', "%$search%")
            ->orWhere('original_filename', 'like', "%$search%")
            ->orWhere('mime', 'like', "%$search%");
    }

    /**
     * Find all files belonging to a user
     * @see User::files()
     * @param Builder $builder
     * @param $user
     * @return Builder
     */
    public function scopeForUser(Builder $builder, $user){
        return $builder->where('user_id', '=', $user->id);
    }
}
