<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\FileFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

/**
 * App\File
 *
 * @property int $id
 * @property int $user_id
 * @property string $file_location
 * @property string $original_filename
 * @property string $mime
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $filename
 * @property int $size
 * @property-read string $delete_url
 * @property-read string $url
 * @property-read User $user
 * @method static FileFactory factory($count = null, $state = [])
 * @method static Builder<static>|File forUser($user)
 * @method static Builder<static>|File newModelQuery()
 * @method static Builder<static>|File newQuery()
 * @method static Builder<static>|File query()
 * @method static Builder<static>|File search($search)
 * @method static Builder<static>|File whereCreatedAt($value)
 * @method static Builder<static>|File whereFileLocation($value)
 * @method static Builder<static>|File whereFilename($value)
 * @method static Builder<static>|File whereId($value)
 * @method static Builder<static>|File whereMime($value)
 * @method static Builder<static>|File whereOriginalFilename($value)
 * @method static Builder<static>|File whereSize($value)
 * @method static Builder<static>|File whereUpdatedAt($value)
 * @method static Builder<static>|File whereUserId($value)
 * @mixin \Eloquent
 */
class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_location',
        'filename',
        'original_filename',
        'mime',
        'size',
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

    public function getUrlAttribute() : string
    {
        return sprintf("%s/%s/%s",
            config('upload.storage.public_url_prefix'),
            config('upload.storage.path'),
            $this->filename
        );
    }

    public function getDeleteUrlAttribute(): string
    {
        return URL::temporarySignedRoute('file.delete', now()->addMinutes(5), ['file' => $this]);
    }
}
