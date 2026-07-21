<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Actions\Backgrounds\SaveBackground;
use App\Exceptions\Backgrounds\CannotDisableLastBackgroundException;
use App\Livewire\Admin\Forms\BackgroundFormData;
use App\Models\Background;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class BackgroundForm extends Component
{
    use WithFileUploads;

    public BackgroundFormData $form;
    public string $previewBreakpoint = 'base';
    public array $focus = [
        'base' => ['x' => 50.0, 'y' => 50.0, 'size' => 100.0],
        'sm' => ['x' => 50.0, 'y' => 50.0, 'size' => 100.0],
        'lg' => ['x' => 50.0, 'y' => 50.0, 'size' => 100.0],
    ];

    public function mount(?int $backgroundId = null): void
    {
        if ($backgroundId !== null) {
            $this->form->fillFromBackground(Background::findOrFail($backgroundId));
        }

        $this->syncFocusFromForm();
    }

    public function addRule(string $type): void
    {
        $this->form->addRule($type);
    }

    public function removeRule(int $index): void
    {
        $this->form->removeRule($index);
    }

    public function updated(string $property, mixed $value): void
    {
        if (str_starts_with($property, 'focus.')) {
            $this->writeFocusValue($property, $value);
            return;
        }

        if (in_array($property, [
            'form.position.x',
            'form.position.y',
            'form.sm.x',
            'form.sm.y',
            'form.lg.x',
            'form.lg.y',
            'form.size',
            'form.sm.size',
            'form.lg.size',
        ], true)) {
            $this->syncFocusFromForm();
        }
    }

    public function save()
    {
        $this->form->validate();

        if (! $this->form->hasImage()) {
            $this->addError('form.image', 'Upload an image or provide an image path/URL.');
            return null;
        }

        try {
            resolve(SaveBackground::class)->save($this->form->toActionData(), $this->form->upload);
        } catch (CannotDisableLastBackgroundException $exception) {
            $this->addError('form.enabled', $exception->getMessage());

            return null;
        }

        session()->flash('status', 'Background saved.');

        return redirect()->route('admin.backgrounds.index');
    }

    #[Computed]
    public function previewUrl(): ?string
    {
        return $this->form->previewUrl();
    }

    #[Computed]
    public function previewStyle(): array
    {
        return $this->form->previewStyle($this->previewBreakpoint);
    }

    #[Computed]
    public function previewBackground(): array
    {
        return $this->form->previewBackground();
    }

    public function render(): View
    {
        return view('livewire.admin.background-form');
    }

    private function writeFocusValue(string $property, mixed $value): void
    {
        $percent = $this->formatFocusPercent($value);

        match ($property) {
            'focus.base.x' => $this->form->position['x'] = $percent,
            'focus.base.y' => $this->form->position['y'] = $percent,
            'focus.base.size' => $this->form->size = $this->formatSizePercent($value),
            'focus.sm.x' => $this->form->sm['x'] = $percent,
            'focus.sm.y' => $this->form->sm['y'] = $percent,
            'focus.sm.size' => $this->form->sm['size'] = $this->formatSizePercent($value),
            'focus.lg.x' => $this->form->lg['x'] = $percent,
            'focus.lg.y' => $this->form->lg['y'] = $percent,
            'focus.lg.size' => $this->form->lg['size'] = $this->formatSizePercent($value),
            default => null,
        };
    }

    private function syncFocusFromForm(): void
    {
        $baseX = $this->percentNumber($this->form->position['x'] ?? null, 50.0);
        $baseY = $this->percentNumber($this->form->position['y'] ?? null, 50.0);
        $baseSize = $this->sizePercentNumber($this->form->size ?? null, 100.0);
        $smX = $this->percentNumber($this->form->sm['x'] ?? null, $baseX);
        $smY = $this->percentNumber($this->form->sm['y'] ?? null, $baseY);
        $smSize = $this->sizePercentNumber($this->form->sm['size'] ?? null, $baseSize);

        $this->focus = [
            'base' => ['x' => $baseX, 'y' => $baseY, 'size' => $baseSize],
            'sm' => ['x' => $smX, 'y' => $smY, 'size' => $smSize],
            'lg' => [
                'x' => $this->percentNumber($this->form->lg['x'] ?? null, $smX),
                'y' => $this->percentNumber($this->form->lg['y'] ?? null, $smY),
                'size' => $this->sizePercentNumber($this->form->lg['size'] ?? null, $smSize),
            ],
        ];
    }

    private function percentNumber(mixed $value, float $fallback): float
    {
        if (! is_string($value) && ! is_numeric($value)) {
            return $fallback;
        }

        $value = trim((string) $value);

        if (! str_ends_with($value, '%')) {
            return $fallback;
        }

        $number = filter_var(rtrim($value, '%'), FILTER_VALIDATE_FLOAT);

        if ($number === false) {
            return $fallback;
        }

        return $this->clamp((float) $number, -50.0, 250.0);
    }

    private function formatFocusPercent(mixed $value): string
    {
        return $this->formatPercent($value, 50.0, -50.0, 250.0, '%');
    }

    private function sizePercentNumber(mixed $value, float $fallback): float
    {
        if (! is_string($value) && ! is_numeric($value)) {
            return $fallback;
        }

        if (preg_match('/\A(\d+(?:\.\d+)?)%(?:\s+auto)?\z/i', trim((string) $value), $matches) !== 1) {
            return $fallback;
        }

        return $this->clamp((float) $matches[1], 25.0, 300.0);
    }

    private function formatSizePercent(mixed $value): string
    {
        return $this->formatPercent($value, 100.0, 25.0, 300.0, '% auto');
    }

    private function formatPercent(mixed $value, float $default, float $min, float $max, string $suffix): string
    {
        $number = $this->clamp(is_numeric($value) ? (float) $value : $default, $min, $max);
        $formatted = rtrim(rtrim(number_format($number, 1, '.', ''), '0'), '.');

        return $formatted . $suffix;
    }

    private function clamp(float $value, float $min, float $max): float
    {
        return max($min, min($max, $value));
    }
}
