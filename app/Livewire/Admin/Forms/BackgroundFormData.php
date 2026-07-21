<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Forms;

use App\Enums\BackgroundScheduleRuleType;
use App\Models\Background;
use App\Rules\BackgroundSchedule;
use App\Services\Backgrounds\BackgroundImageStorage;
use App\Services\Backgrounds\BackgroundNormalizer;
use App\Support\BackgroundCssValue;
use Illuminate\Validation\Rule;
use Livewire\Form;

class BackgroundFormData extends Form
{
    public ?int $backgroundId = null;

    public string $title = '';
    public ?string $description = null;
    public string $image = '';
    public mixed $upload = null;

    public float $overlay = 0.68;
    public int $weight = 1;
    public bool $enabled = true;
    public bool $pinned = false;

    public array $position = ['x' => '50%', 'y' => '50%'];
    public string $size = 'cover';

    public array $sm = ['x' => '', 'y' => '', 'size' => ''];
    public array $lg = ['x' => '', 'y' => '', 'size' => ''];

    public array $schedule = [];

    public function fillFromBackground(Background $background): void
    {
        $this->backgroundId = $background->id;
        $this->title = $background->title;
        $this->description = $background->description;
        $this->image = $background->image;
        $this->overlay = (float) $background->overlay;
        $this->weight = (int) $background->weight;
        $this->enabled = (bool) $background->enabled;
        $this->pinned = (bool) $background->pinned;
        $this->size = $background->size ?: 'cover';
        $this->position = array_merge(
            ['x' => '50%', 'y' => '50%'],
            is_array($background->position) ? $background->position : [],
        );

        $variants = is_array($background->variants) ? $background->variants : [];
        $this->sm = $this->hydrateVariant($variants['sm'] ?? []);
        $this->lg = $this->hydrateVariant($variants['lg'] ?? []);
        $this->schedule = is_array($background->schedule) ? $background->schedule : [];
    }

    public function addRule(string $type): void
    {
        $type = BackgroundScheduleRuleType::tryFrom($type) ?? BackgroundScheduleRuleType::Date;

        $this->schedule[] = match ($type) {
            BackgroundScheduleRuleType::Date => ['type' => $type->value, 'month' => 12, 'day' => 25],
            BackgroundScheduleRuleType::Weekday => ['type' => $type->value, 'days' => []],
            BackgroundScheduleRuleType::Range => ['type' => $type->value, 'start' => '', 'end' => ''],
        };
    }

    public function removeRule(int $index): void
    {
        unset($this->schedule[$index]);
        $this->schedule = array_values($this->schedule);
    }

    public function hasImage(): bool
    {
        return $this->upload !== null || trim($this->image) !== '';
    }

    public function toActionData(): array
    {
        return [
            'background_id' => $this->backgroundId,
            'title' => trim($this->title),
            'description' => $this->nullableString($this->description),
            'image' => trim($this->image),
            'overlay' => (float) $this->overlay,
            'size' => trim($this->size),
            'position' => [
                'x' => trim((string) ($this->position['x'] ?? '50%')),
                'y' => trim((string) ($this->position['y'] ?? '50%')),
            ],
            'variants' => $this->buildVariants(),
            'schedule' => $this->buildSchedule(),
            'enabled' => (bool) $this->enabled,
            'weight' => (int) $this->weight,
            'pinned' => (bool) $this->pinned,
        ];
    }

    public function previewUrl(): ?string
    {
        if ($this->upload !== null) {
            try {
                return $this->upload->temporaryUrl();
            } catch (\Throwable) {
                return null;
            }
        }

        $image = trim($this->image);

        if ($image === '') {
            return null;
        }

        return app(BackgroundNormalizer::class)->resolveAssetUrl($image);
    }

    public function previewStyle(string $breakpoint): array
    {
        $x = BackgroundCssValue::normalizePositionToken($this->position['x'] ?? null, '50%');
        $y = BackgroundCssValue::normalizePositionToken($this->position['y'] ?? null, '50%');
        $size = BackgroundCssValue::normalizeSize($this->size, 'cover');

        $cascade = match ($breakpoint) {
            'sm' => ['sm'],
            'lg' => ['sm', 'lg'],
            default => [],
        };

        foreach ($cascade as $variant) {
            $values = $this->{$variant};
            $x = BackgroundCssValue::normalizePositionToken($values['x'] ?? null, $x);
            $y = BackgroundCssValue::normalizePositionToken($values['y'] ?? null, $y);
            $size = BackgroundCssValue::normalizeSize($values['size'] ?? null, $size);
        }

        return ['x' => $x, 'y' => $y, 'size' => $size];
    }

    public function previewBackground(): array
    {
        $url = $this->previewUrl() ?? '';
        $variants = [];

        foreach (['base', 'sm', 'lg'] as $breakpoint) {
            $style = $this->previewStyle($breakpoint);
            $variants[$breakpoint] = [
                'url' => $url,
                'position' => ['x' => $style['x'], 'y' => $style['y']],
                'size' => $style['size'],
            ];
        }

        return [
            'title' => trim($this->title) !== '' ? trim($this->title) : 'Background title',
            'description' => $this->nullableString($this->description),
            'url' => $url,
            'overlay' => (float) $this->overlay,
            'position' => $variants['base']['position'],
            'size' => $variants['base']['size'],
            'variants' => $variants,
        ];
    }

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image' => ['nullable', 'string', 'max:2048'],
            'upload' => BackgroundImageStorage::uploadRules(),
            'overlay' => ['numeric', 'min:0', 'max:1'],
            'weight' => ['integer', 'min:1', 'max:1000'],
            'enabled' => ['boolean'],
            'pinned' => ['boolean'],
            'position.x' => ['required', 'string', 'max:16', $this->backgroundPositionRule()],
            'position.y' => ['required', 'string', 'max:16', $this->backgroundPositionRule()],
            'size' => ['required', 'string', 'max:32', $this->backgroundSizeRule()],
            'sm.x' => ['nullable', 'string', 'max:16', $this->backgroundPositionRule()],
            'sm.y' => ['nullable', 'string', 'max:16', $this->backgroundPositionRule()],
            'sm.size' => ['nullable', 'string', 'max:32', $this->backgroundSizeRule()],
            'lg.x' => ['nullable', 'string', 'max:16', $this->backgroundPositionRule()],
            'lg.y' => ['nullable', 'string', 'max:16', $this->backgroundPositionRule()],
            'lg.size' => ['nullable', 'string', 'max:32', $this->backgroundSizeRule()],
            'schedule' => ['array', new BackgroundSchedule()],
            'schedule.*.type' => ['nullable', Rule::enum(BackgroundScheduleRuleType::class)],
        ];
    }

    private function backgroundPositionRule(): \Closure
    {
        return static function (string $attribute, mixed $value, \Closure $fail): void {
            if ($value === null || trim((string) $value) === '') {
                return;
            }

            if (! BackgroundCssValue::isPositionToken($value)) {
                $fail('The ' . $attribute . ' must be a valid background position.');
            }
        };
    }

    private function backgroundSizeRule(): \Closure
    {
        return static function (string $attribute, mixed $value, \Closure $fail): void {
            if ($value === null || trim((string) $value) === '') {
                return;
            }

            if (! BackgroundCssValue::isSize($value)) {
                $fail('The ' . $attribute . ' must be a valid background size.');
            }
        };
    }

    private function hydrateVariant(array $variant): array
    {
        return [
            'x' => $variant['position']['x'] ?? '',
            'y' => $variant['position']['y'] ?? '',
            'size' => $variant['size'] ?? '',
        ];
    }

    private function buildVariants(): array
    {
        $variants = [
            'base' => [
                'position' => [
                    'x' => trim((string) ($this->position['x'] ?? '50%')),
                    'y' => trim((string) ($this->position['y'] ?? '50%')),
                ],
                'size' => trim($this->size),
            ],
        ];

        foreach (['sm' => $this->sm, 'lg' => $this->lg] as $breakpoint => $values) {
            $override = [];

            if (($values['x'] ?? '') !== '' || ($values['y'] ?? '') !== '') {
                $override['position'] = array_filter([
                    'x' => trim((string) ($values['x'] ?? '')),
                    'y' => trim((string) ($values['y'] ?? '')),
                ], static fn ($value) => $value !== '');
            }

            if (($values['size'] ?? '') !== '') {
                $override['size'] = trim((string) $values['size']);
            }

            if ($override !== []) {
                $variants[$breakpoint] = $override;
            }
        }

        return $variants;
    }

    private function buildSchedule(): ?array
    {
        $rules = [];

        foreach ($this->schedule as $rule) {
            if (! is_array($rule)) {
                continue;
            }

            $type = is_string($rule['type'] ?? null) ? BackgroundScheduleRuleType::tryFrom($rule['type']) : null;

            switch ($type) {
                case BackgroundScheduleRuleType::Date:
                    $rules[] = [
                        'type' => $type->value,
                        'month' => (int) $rule['month'],
                        'day' => (int) $rule['day'],
                    ];
                    break;
                case BackgroundScheduleRuleType::Weekday:
                    $days = array_values(array_unique(array_map('intval', (array) ($rule['days'] ?? []))));
                    $rules[] = ['type' => $type->value, 'days' => $days];
                    break;
                case BackgroundScheduleRuleType::Range:
                    $rules[] = [
                        'type' => $type->value,
                        'start' => (string) $rule['start'],
                        'end' => (string) $rule['end'],
                    ];
                    break;
            }
        }

        return $rules === [] ? null : $rules;
    }

    private function nullableString(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }
}
