<?php

declare(strict_types=1);

namespace App\Actions\Backgrounds;

use App\Models\Background;
use App\Services\Backgrounds\BackgroundImageStorage;
use App\Services\Backgrounds\BackgroundSelectionCache;
use Illuminate\Support\Facades\DB;
use Throwable;

class DeleteBackground
{
    public function __construct(
        private readonly BackgroundImageStorage $images,
        private readonly BackgroundSelectionCache $cache,
    ) {
    }

    public function delete(Background $background): void
    {
        $background->ensureCanDisable('Cannot delete the only enabled background.');

        $managedPaths = $this->images->managedPublicPathsFor($background);

        DB::transaction(fn () => $background->delete());

        try {
            $this->images->deleteUnreferenced($managedPaths);
        } catch (Throwable $exception) {
            report($exception);
        }

        $this->cache->forget();
    }
}
