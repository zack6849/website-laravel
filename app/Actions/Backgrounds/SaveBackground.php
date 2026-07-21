<?php

declare(strict_types=1);

namespace App\Actions\Backgrounds;

use App\Models\Background;
use App\Services\Backgrounds\BackgroundImageStorage;
use App\Services\Backgrounds\BackgroundSelectionCache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Throwable;

class SaveBackground
{
    public function __construct(
        private readonly BackgroundImageStorage $images,
        private readonly BackgroundSelectionCache $cache,
        private readonly PinBackground $pinBackground,
    ) {
    }

    public function save(array $data, mixed $upload = null): Background
    {
        $imagePath = $this->resolveImagePath((string) ($data['image'] ?? ''), $upload);
        $uploadedPath = $upload !== null ? $imagePath : null;
        $oldManagedPaths = [];

        try {
            $background = DB::transaction(function () use ($data, $imagePath, &$oldManagedPaths): Background {
                $attributes = [
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'image' => $imagePath,
                    'overlay' => $data['overlay'],
                    'size' => $data['size'],
                    'position' => $data['position'],
                    'variants' => $data['variants'],
                    'schedule' => $data['schedule'],
                    'enabled' => $data['enabled'],
                    'weight' => $data['weight'],
                    'pinned' => $data['pinned'],
                ];

                if ($data['background_id'] === null) {
                    $attributes['key'] = $this->uniqueKey($data['title']);
                    $background = Background::create($attributes);
                } else {
                    $background = Background::findOrFail($data['background_id']);
                    if ($background->enabled && ! (bool) $attributes['enabled']) {
                        $background->ensureCanDisable('Cannot disable the only enabled background.');
                    }

                    $oldManagedPaths = $this->images->managedPublicPathsFor($background);
                    $background->update($attributes);
                }

                if ($data['pinned']) {
                    return $this->pinBackground->setPinned($background, true);
                }

                $this->cache->forget();

                return $background->refresh();
            });
        } catch (Throwable $exception) {
            if ($uploadedPath !== null) {
                $this->deleteIfUnreferenced($uploadedPath);
            }

            throw $exception;
        }

        $currentManagedPaths = $this->images->managedPublicPathsFor($background);

        $this->deleteUnreferenced(array_diff($oldManagedPaths, $currentManagedPaths));

        return $background;
    }

    private function deleteIfUnreferenced(string $publicPath): void
    {
        $this->deleteUnreferenced([$publicPath]);
    }

    private function deleteUnreferenced(array $publicPaths): void
    {
        try {
            $this->images->deleteUnreferenced($publicPaths);
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    private function resolveImagePath(string $imagePath, mixed $upload): string
    {
        if ($upload !== null) {
            return $this->images->store($upload);
        }

        $imagePath = trim($imagePath);

        if ($imagePath === '') {
            throw new InvalidArgumentException('A background image path or upload is required.');
        }

        return $imagePath;
    }

    private function uniqueKey(string $title): string
    {
        $base = Str::slug($title) ?: 'background';
        $key = $base;
        $suffix = 1;

        while (Background::where('key', $key)->exists()) {
            $key = $base . '-' . (++$suffix);
        }

        return $key;
    }
}
