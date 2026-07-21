<?php

declare(strict_types=1);

namespace App\Livewire\Files;

use App\Exceptions\FileCannotBeDeletedException;
use App\Models\File;
use App\Services\FileUploadService;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class FileIndex extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $createdFrom = '';

    #[Url(except: '')]
    public string $createdTo = '';

    public ?string $status = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCreatedFrom(): void
    {
        $this->resetPage();
    }

    public function updatedCreatedTo(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset('search', 'createdFrom', 'createdTo');
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        $file = File::forUser(auth()->user())->findOrFail($id);

        $this->authorize('delete', $file);

        try {
            resolve(FileUploadService::class)->delete($file);
        } catch (FileCannotBeDeletedException) {
            $this->addError('files', 'File deletion failed');

            return;
        }

        $this->status = 'Your file was deleted!';
    }

    #[Computed]
    public function files()
    {
        return File::forUser(auth()->user())
            ->when($this->search !== '', fn ($query) => $query->search($this->search))
            ->when($this->createdFrom !== '', fn ($query) => $query->whereDate('created_at', '>=', $this->createdFrom))
            ->when($this->createdTo !== '', fn ($query) => $query->whereDate('created_at', '<=', $this->createdTo))
            ->latest()
            ->paginate(25);
    }

    public function render(): View
    {
        return view('livewire.files.file-index');
    }
}
