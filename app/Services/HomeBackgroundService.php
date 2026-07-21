<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Backgrounds\BackgroundNormalizer;
use App\Services\Backgrounds\BackgroundSelector;

class HomeBackgroundService
{
    public function __construct(
        private readonly BackgroundSelector $selector,
        private readonly BackgroundNormalizer $normalizer,
    ) {
    }

    public function calculateCurrentBackgroundInfo(): array
    {
        $selection = $this->selector->select();

        return $this->normalizer->normalize(
            $selection['item'],
            [],
            $selection['key'],
        );
    }
}
