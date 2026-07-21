<?php

declare(strict_types=1);

namespace App\Actions\Backgrounds;

use App\Models\Background;
use App\Services\Backgrounds\BackgroundSelectionCache;
use Illuminate\Support\Facades\DB;

class PinBackground
{
    public function __construct(
        private readonly BackgroundSelectionCache $cache,
    ) {
    }

    public function toggle(Background $background): Background
    {
        return $this->setPinned($background, ! $background->pinned);
    }

    public function setPinned(Background $background, bool $pinned): Background
    {
        return DB::transaction(function () use ($background, $pinned): Background {
            if ($pinned) {
                Background::query()
                    ->where('id', '!=', $background->getKey())
                    ->where('pinned', true)
                    ->update(['pinned' => false]);
            }

            $background->pinned = $pinned;
            $background->save();

            $this->cache->forget();

            return $background->refresh();
        });
    }
}
