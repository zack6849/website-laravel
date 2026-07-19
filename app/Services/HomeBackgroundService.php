<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class HomeBackgroundService
{
    private const DEFAULT_CACHE_KEY = 'home-background';
    private const DEFAULT_CACHE_TTL_SECONDS = 2;
    private const DEFAULT_IMAGE = 'img/bg/pier_night.jpg';
    private const DEFAULT_OVERLAY = 0.68;
    private const DEFAULT_SIZE = 'cover';
    private const FALLBACK_KEY = 'pier_night';
    private const FALLBACK_BACKGROUND = [
        'title' => 'St. Petersburg Pier',
        'image' => self::DEFAULT_IMAGE,
        'description' => 'Night shot of the St. Petersburg Pier.',
        'position' => [
            'x' => '50%',
            'y' => '34%',
        ],
        'overlay' => self::DEFAULT_OVERLAY,
        'size' => self::DEFAULT_SIZE,
    ];

    public function calculateCurrentBackgroundInfo(): array
    {
        $defaults = $this->defaults();
        $cacheTtlSeconds = max(1, (int) config('backgrounds.cache.ttl_seconds', self::DEFAULT_CACHE_TTL_SECONDS));
        $cacheKey = (string) config('backgrounds.cache.key', self::DEFAULT_CACHE_KEY);
        $items = $this->enabledBackgroundItems();

        if ($items->isEmpty()) {
            return $this->normalizeBackground(self::FALLBACK_BACKGROUND, $defaults, self::FALLBACK_KEY);
        }

        $selectedKey = $this->resolveSelectedKey($items, $cacheKey, $cacheTtlSeconds);

        return $this->normalizeBackground(
            $items->get($selectedKey),
            $defaults,
            $selectedKey,
        );
    }

    private function defaults(): array
    {
        $defaults = config('backgrounds.defaults', []);

        return is_array($defaults) ? $defaults : [];
    }

    private function enabledBackgroundItems(): Collection
    {
        return collect(config('backgrounds.items', []))
            ->filter(fn (mixed $background): bool => is_array($background)
                && ($background['enabled'] ?? true)
                && ! empty($background['image']));
    }

    private function resolveSelectedKey(Collection $items, string $cacheKey, int $cacheTtlSeconds): string
    {
        $ttl = now()->addSeconds($cacheTtlSeconds);
        $selectedKey = Cache::remember(
            $cacheKey,
            $ttl,
            fn (): string => (string) Arr::random($items->keys()->all()),
        );

        if (! is_string($selectedKey) || ! $items->has($selectedKey)) {
            $selectedKey = (string) $items->keys()->first();
            Cache::put($cacheKey, $selectedKey, $ttl);
        }

        return $selectedKey;
    }

    private function normalizeBackground(array $background, array $defaults, string $key): array
    {
        $background = array_replace_recursive($defaults, $background);
        $position = $background['position'] ?? [];

        $background['key'] = $key;
        $background['image'] = is_string($background['image'] ?? null)
            ? $background['image']
            : self::DEFAULT_IMAGE;
        $background['url'] = asset($background['image']);
        $background['overlay'] = max(0.0, min(1.0, (float) ($background['overlay'] ?? self::DEFAULT_OVERLAY)));
        $background['size'] = $this->normalizeBackgroundSize($background['size'] ?? self::DEFAULT_SIZE);
        $background['position'] = array_replace(
            ['x' => '50%', 'y' => '50%'],
            is_array($position) ? $position : [],
        );

        return $background;
    }

    private function normalizeBackgroundSize(mixed $size): string
    {
        if (! is_string($size)) {
            return self::DEFAULT_SIZE;
        }

        $size = trim($size);

        return $size !== '' ? $size : self::DEFAULT_SIZE;
    }
}
