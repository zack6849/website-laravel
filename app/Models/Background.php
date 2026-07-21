<?php

declare(strict_types=1);

namespace App\Models;

use App\Exceptions\Backgrounds\CannotDisableLastBackgroundException;
use App\Services\Backgrounds\BackgroundNormalizer;
use App\Services\Backgrounds\BackgroundScheduleMatcher;
use App\Services\Backgrounds\BackgroundSelectionCache;
use App\Support\BackgroundCssValue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string|null $key
 * @property string $title
 * @property string|null $description
 * @property string $image
 * @property float $overlay
 * @property string $size
 * @property array|null $position
 * @property array|null $variants
 * @property array|null $schedule
 * @property bool $enabled
 * @property int $weight
 * @property bool $pinned
 */
class Background extends Model
{
    protected $fillable = [
        'key',
        'title',
        'description',
        'image',
        'overlay',
        'size',
        'position',
        'variants',
        'schedule',
        'enabled',
        'weight',
        'pinned',
    ];

    protected $casts = [
        'overlay' => 'float',
        'position' => 'array',
        'variants' => 'array',
        'schedule' => 'array',
        'enabled' => 'boolean',
        'weight' => 'integer',
        'pinned' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => static::forgetSelectionCache());
        static::deleted(fn () => static::forgetSelectionCache());
    }

    public static function forgetSelectionCache(): void
    {
        app(BackgroundSelectionCache::class)->forget();
    }

    public function scopeEnabled(Builder $builder): Builder
    {
        return $builder->where('enabled', true);
    }

    /**
     * Whether at least one other enabled background exists besides the given id.
     * Used to stop an admin from disabling/deleting the last enabled background,
     * which would silently drop the homepage into its single hardcoded fallback.
     */
    public static function hasOtherEnabled(int $excludingId): bool
    {
        return static::query()->enabled()->where('id', '!=', $excludingId)->exists();
    }

    /**
     * Guard against disabling/deleting the last enabled background, which would
     * silently drop the homepage into its single hardcoded fallback.
     */
    public function ensureCanDisable(string $message): void
    {
        if ($this->enabled && ! static::hasOtherEnabled($this->id)) {
            throw new CannotDisableLastBackgroundException($message);
        }
    }

    /**
     * Resolved CSS values for the admin index card thumbnail.
     *
     * @return array{url: string, size: string, x: string, y: string}
     */
    public function thumbnailStyle(): array
    {
        $url = app(BackgroundNormalizer::class)->resolveAssetUrl($this->image);

        return [
            'url' => $url,
            'size' => BackgroundCssValue::normalizeSize($this->size, 'cover'),
            'x' => BackgroundCssValue::normalizePositionToken(data_get($this->position, 'x'), '50%'),
            'y' => BackgroundCssValue::normalizePositionToken(data_get($this->position, 'y'), '50%'),
        ];
    }

    /**
     * Shape this record for the selection service's normalization step.
     */
    public function toDefinition(): array
    {
        return array_filter(
            [
                'title' => $this->title,
                'image' => $this->image,
                'description' => $this->description,
                'position' => is_array($this->position) ? $this->position : null,
                'size' => $this->size,
                'overlay' => $this->overlay,
                'variants' => is_array($this->variants) ? $this->variants : null,
                'enabled' => $this->enabled,
            ],
            static fn ($value) => $value !== null,
        );
    }

    /**
     * True when this background has no schedule (always eligible) or at least one
     * of its schedule rules matches the given date.
     */
    public function isScheduledFor(Carbon $date): bool
    {
        return app(BackgroundScheduleMatcher::class)->matches($this->schedule, $date);
    }

    public function hasSchedule(): bool
    {
        return is_array($this->schedule) && $this->schedule !== [];
    }

}
