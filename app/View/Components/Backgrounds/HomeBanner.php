<?php

declare(strict_types=1);

namespace App\View\Components\Backgrounds;

use App\Services\Backgrounds\BackgroundNormalizer;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Renders the homepage banner. Used both full-bleed on the public homepage
 * (fed by HomeBackgroundService's already-normalized selection) and as the
 * shrunk live preview in the admin background form (fed by unsaved,
 * loosely-shaped form state), so all the URL/variant resolution needed to
 * support both shapes lives here instead of in the Blade template.
 */
class HomeBanner extends Component
{
    /** @var array{url: string, position: array{x: string, y: string}, size: string} */
    public array $baseVariant;

    /** @var array{url: string, position: array{x: string, y: string}, size: string} */
    public array $smVariant;

    /** @var array{url: string, position: array{x: string, y: string}, size: string} */
    public array $lgVariant;

    public string $previewBreakpoint;
    public string $frameClasses;
    public string $surfaceClasses;
    public string $contactId;
    public string $contactHeadingId;
    public string $infoDescriptionId;

    /**
     * @param  array<string, mixed>  $background
     */
    public function __construct(
        private readonly BackgroundNormalizer $normalizer,
        public array $background = [],
        public bool $fullBleed = true,
        string $previewBreakpoint = 'lg',
        public ?string $emptyMessage = null,
    ) {
        $this->previewBreakpoint = $this->sanitizeBreakpoint($previewBreakpoint);

        $position = ['x' => '50%', 'y' => '34%'];

        if (is_array($this->background['position'] ?? null)) {
            $position = $this->background['position'];
        }

        $variants = [];

        if (is_array($this->background['variants'] ?? null)) {
            $variants = $this->background['variants'];
        }

        $size = $this->background['size'] ?? 'cover';
        $url = $this->resolveUrl($this->background, asset(BackgroundNormalizer::DEFAULT_IMAGE));

        $this->baseVariant = $this->normalizeVariant($variants['base'] ?? [], [
            'url' => $url,
            'position' => $position,
            'size' => $size,
        ]);

        $this->smVariant = $this->normalizeVariant($variants['sm'] ?? [], $this->baseVariant);
        $this->lgVariant = $this->normalizeVariant($variants['lg'] ?? [], $this->smVariant);

        $this->frameClasses = $this->buildFrameClasses();
        $this->surfaceClasses = $this->buildSurfaceClasses();

        $this->contactId = 'contact';
        $this->contactHeadingId = 'contact-heading';
        $this->infoDescriptionId = 'background-info-description';

        if (! $this->fullBleed) {
            $this->contactId = 'background-preview-contact';
            $this->contactHeadingId = 'background-preview-contact-heading';
            $this->infoDescriptionId = 'background-preview-info-description';
        }
    }

    public function render(): View
    {
        return view('components.backgrounds.home-banner');
    }

    private function sanitizeBreakpoint(string $breakpoint): string
    {
        if (in_array($breakpoint, ['base', 'sm', 'lg'], true)) {
            return $breakpoint;
        }

        return 'lg';
    }

    private function buildFrameClasses(): string
    {
        if ($this->fullBleed) {
            return 'top-banner-frame top-banner-frame-full';
        }

        return 'top-banner-frame top-banner-frame-preview top-banner-preview-' . $this->previewBreakpoint;
    }

    private function buildSurfaceClasses(): string
    {
        if ($this->fullBleed) {
            return 'top-banner-surface';
        }

        return 'top-banner-surface top-banner-surface-preview';
    }

    /**
     * Each breakpoint falls back to the previous one, so leaving `lg` unset
     * inherits whatever `sm` resolved to rather than jumping back to `base`.
     *
     * @param  array<string, mixed>  $variant
     * @param  array{url: string, position: array{x: string, y: string}, size: string}  $fallback
     * @return array{url: string, position: array{x: string, y: string}, size: string}
     */
    private function normalizeVariant(array $variant, array $fallback): array
    {
        $url = $this->resolveUrl($variant, $fallback['url']);

        $position = $fallback['position'];

        if (is_array($variant['position'] ?? null)) {
            if (isset($variant['position']['x'])) {
                $position['x'] = $variant['position']['x'];
            }

            if (isset($variant['position']['y'])) {
                $position['y'] = $variant['position']['y'];
            }
        }

        $size = $fallback['size'];

        if (isset($variant['size'])) {
            $size = $variant['size'];
        }

        return [
            'url' => $url,
            'position' => $position,
            'size' => $size,
        ];
    }

    /**
     * A pre-resolved `url` (e.g. a Livewire temporary upload URL, which isn't
     * a real storage path `asset()` could resolve) always wins. Otherwise the
     * `image` path is resolved through BackgroundNormalizer, falling back to
     * $fallbackUrl when there's no usable image.
     *
     * @param  array<string, mixed>  $source
     */
    private function resolveUrl(array $source, string $fallbackUrl): string
    {
        if (array_key_exists('url', $source)) {
            return (string) $source['url'];
        }

        $image = $source['image'] ?? null;

        if (! is_string($image) || trim($image) === '') {
            return $fallbackUrl;
        }

        return $this->normalizer->resolveAssetUrl($image);
    }
}
