<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Actions\Logbook\QueueLogbookImport;
use App\Actions\Logbook\SetLogbookEntryVisibility;
use App\Models\LogbookEntry;
use App\Services\Logbook\LogbookCache;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class LogbookIndex extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: 'all')]
    public string $visibility = 'all';

    public ?string $status = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedVisibility(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset('search', 'visibility');
        $this->resetPage();
    }

    public function hide(int $id): void
    {
        resolve(SetLogbookEntryVisibility::class)->hide(LogbookEntry::findOrFail($id));

        $this->status = 'QSO hidden from public logbook.';
    }

    public function show(int $id): void
    {
        resolve(SetLogbookEntryVisibility::class)->show(LogbookEntry::findOrFail($id));

        $this->status = 'QSO restored to public logbook.';
    }

    public function import(): void
    {
        resolve(QueueLogbookImport::class)->queue();

        $this->status = 'QRZ logbook re-import queued. A queue worker must be running to process it.';
    }

    #[Computed]
    public function entries()
    {
        return LogbookEntry::query()
            ->with(['station', 'callee'])
            ->visibility($this->visibility)
            ->search($this->search)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(25);
    }

    #[Computed]
    public function lastImportedAt(): ?Carbon
    {
        return resolve(LogbookCache::class)->lastImportedAt();
    }

    public function render(): View
    {
        return view('livewire.admin.logbook-index');
    }
}
