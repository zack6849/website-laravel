<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\File
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File query()
 * @mixin \Eloquent
 * @property int $id
 * @property int $user_id
 * @property string $file_location
 * @property string $original_filename
 * @property string $mime
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereFileLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereMime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereOriginalFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\File whereUserId($value)
 * @property string $filename
 * @property int $size
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|File search($search)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereSize($value)
 */
class File extends Model
{
    public function user(){
        return $this->belongsTo(User::class);
    }
    
    public function scopeSearch(Builder $builder, $search){
        return $builder->where('filename', 'like', "%$search%")
            ->orWhere('original_filename', 'like', "%$search%")
            ->orWhere('mime', 'like', "%$search%");
    }
}
