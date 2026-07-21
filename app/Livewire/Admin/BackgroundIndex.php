<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Actions\Backgrounds\DeleteBackground;
use App\Actions\Backgrounds\PinBackground;
use App\Actions\Backgrounds\ToggleBackgroundEnabled;
use App\Exceptions\Backgrounds\CannotDisableLastBackgroundException;
use App\Models\Background;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class BackgroundIndex extends Component
{
    public function render(): View
    {
        return view('livewire.admin.background-index');
    }

    #[Computed]
    public function backgrounds()
    {
        return Background::query()
            ->orderByDesc('pinned')
            ->orderByDesc('enabled')
            ->orderBy('title')
            ->get();
    }

    public function toggleEnabled(int $id): void
    {
        try {
            resolve(ToggleBackgroundEnabled::class)->toggle(Background::findOrFail($id));
        } catch (CannotDisableLastBackgroundException $exception) {
            $this->addError('backgrounds', $exception->getMessage());

            return;
        }

        $this->dispatch('background-updated');
    }

    public function togglePinned(int $id): void
    {
        $background = Background::findOrFail($id);
        resolve(PinBackground::class)->toggle($background);

        $this->dispatch('background-updated');
    }

    public function delete(int $id): void
    {
        try {
            resolve(DeleteBackground::class)->delete(Background::findOrFail($id));
        } catch (CannotDisableLastBackgroundException $exception) {
            $this->addError('backgrounds', $exception->getMessage());

            return;
        }

        $this->dispatch('background-updated');
    }
}
