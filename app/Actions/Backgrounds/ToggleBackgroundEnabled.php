<?php

declare(strict_types=1);

namespace App\Actions\Backgrounds;

use App\Models\Background;
use App\Services\Backgrounds\BackgroundSelectionCache;

class ToggleBackgroundEnabled
{
    public function __construct(
        private readonly BackgroundSelectionCache $cache,
    ) {
    }

    public function toggle(Background $background): Background
    {
        $background->ensureCanDisable('Cannot disable the only enabled background.');

        $background->enabled = ! $background->enabled;
        $background->save();

        $this->cache->forget();

        return $background;
    }
}
