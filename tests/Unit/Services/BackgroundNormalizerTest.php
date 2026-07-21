<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\Backgrounds\BackgroundNormalizer;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BackgroundNormalizerTest extends TestCase
{
    public function refreshDatabase(): void
    {
        // This test only exercises array normalization and doesn't need a database.
    }

    #[Test]
    public function backgroundDefinitionsAreExpandedIntoResponsiveVariants(): void
    {
        $background = $this->normalizer()->normalize([
            'title' => 'Single',
            'image' => 'img/bg/pier_night.jpg',
            'position' => [
                'x' => '12%',
                'y' => '34%',
            ],
            'size' => 'auto 180%',
        ], [], 'single');

        $this->assertSame('single', $background['key']);
        $this->assertSame('12%', $background['variants']['base']['position']['x']);
        $this->assertSame('34%', $background['variants']['sm']['position']['y']);
        $this->assertSame('auto 180%', $background['variants']['lg']['size']);
        $this->assertSame(asset('img/bg/pier_night.jpg'), $background['variants']['base']['url']);
    }

    #[Test]
    public function responsiveVariantOverridesCanSwapCropSettingsPerBreakpoint(): void
    {
        $background = $this->normalizer()->normalize([
            'title' => 'Single',
            'image' => 'img/bg/pier_night.jpg',
            'position' => [
                'x' => '50%',
                'y' => '50%',
            ],
            'size' => 'cover',
            'variants' => [
                'base' => [
                    'position' => [
                        'x' => '18%',
                        'y' => '22%',
                    ],
                ],
                'sm' => [
                    'size' => '110% auto',
                ],
                'lg' => [
                    'image' => 'img/bg/bg_dtsp_bokeh.jpg',
                    'position' => [
                        'x' => '77%',
                        'y' => '61%',
                    ],
                    'size' => '100% auto',
                ],
            ],
        ], [], 'single');

        $this->assertSame('18%', $background['variants']['base']['position']['x']);
        $this->assertSame('110% auto', $background['variants']['sm']['size']);
        $this->assertSame(asset('img/bg/bg_dtsp_bokeh.jpg'), $background['variants']['lg']['url']);
        $this->assertSame('77%', $background['variants']['lg']['position']['x']);
        $this->assertSame('61%', $background['variants']['lg']['position']['y']);
    }

    #[Test]
    public function lgInheritsFromSmWhenLgHasNoOverrideOfItsOwn(): void
    {
        $background = $this->normalizer()->normalize([
            'title' => 'Single',
            'image' => 'img/bg/pier_night.jpg',
            'position' => [
                'x' => '50%',
                'y' => '50%',
            ],
            'size' => 'cover',
            'variants' => [
                'sm' => [
                    'position' => [
                        'x' => '18%',
                        'y' => '22%',
                    ],
                    'size' => '110% auto',
                ],
            ],
        ], [], 'single');

        $this->assertSame('18%', $background['variants']['sm']['position']['x']);
        $this->assertSame('18%', $background['variants']['lg']['position']['x']);
        $this->assertSame('22%', $background['variants']['lg']['position']['y']);
        $this->assertSame('110% auto', $background['variants']['lg']['size']);
    }

    #[Test]
    public function invalidBackgroundCssValuesAreNormalizedBeforeRendering(): void
    {
        $background = $this->normalizer()->normalize([
            'title' => 'Single',
            'image' => 'img/bg/pier_night.jpg',
            'position' => [
                'x' => '50%;background:red',
                'y' => '72%',
            ],
            'size' => 'cover;background:red',
            'variants' => [
                'lg' => [
                    'position' => [
                        'x' => 'right',
                        'y' => 'url(https://example.test/a)',
                    ],
                    'size' => 'calc(100%)',
                ],
            ],
        ], [], 'single');

        $this->assertSame('50%', $background['variants']['base']['position']['x']);
        $this->assertSame('72%', $background['variants']['base']['position']['y']);
        $this->assertSame('cover', $background['variants']['base']['size']);
        $this->assertSame('right', $background['variants']['lg']['position']['x']);
        $this->assertSame('72%', $background['variants']['lg']['position']['y']);
        $this->assertSame('cover', $background['variants']['lg']['size']);
    }

    private function normalizer(): BackgroundNormalizer
    {
        return resolve(BackgroundNormalizer::class);
    }
}
