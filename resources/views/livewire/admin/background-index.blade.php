<div>
    <div class="mb-4 flex items-center justify-between">
        <p class="text-sm text-gray-600">
            Pinned backgrounds override everything. Otherwise a themed (scheduled) background is used when its
            day matches, and failing that one is chosen at random weighted by its weight.
        </p>
        <a href="{{ route('admin.backgrounds.create') }}" class="btn-primary whitespace-nowrap">
            <i class="fa fa-plus"></i> New background
        </a>
    </div>

    @error('backgrounds')
        <div class="mb-4 rounded border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">{{ $message }}</div>
    @enderror

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($this->backgrounds as $background)
            @php($style = $background->thumbnailStyle())
            <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                <div
                    class="h-32 w-full bg-slate-800 bg-no-repeat"
                    style="
                        background-image: url('{{ $style['url'] }}');
                        background-size: {{ $style['size'] }};
                        background-position: {{ $style['x'] }} {{ $style['y'] }};
                    "
                ></div>
                <div class="p-3">
                    <div class="flex items-center justify-between gap-2">
                        <h3 class="truncate font-semibold text-slate-900" title="{{ $background->title }}">
                            {{ $background->title }}
                        </h3>
                        <div class="flex shrink-0 gap-1">
                            @if ($background->pinned)
                                <span class="rounded bg-amber-100 px-1.5 py-0.5 text-xs font-medium text-amber-800">Pinned</span>
                            @endif
                            @if ($background->hasSchedule())
                                <span class="rounded bg-indigo-100 px-1.5 py-0.5 text-xs font-medium text-indigo-800">Themed</span>
                            @endif
                            @unless ($background->enabled)
                                <span class="rounded bg-slate-200 px-1.5 py-0.5 text-xs font-medium text-slate-600">Off</span>
                            @endunless
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-slate-500">Weight: {{ $background->weight }}</p>

                    <div class="mt-3 flex flex-wrap items-center gap-2 text-sm">
                        <a href="{{ route('admin.backgrounds.edit', $background) }}"
                           class="rounded bg-slate-100 px-2 py-1 text-slate-700 hover:bg-slate-200">
                            Edit
                        </a>
                        <button type="button" wire:click="toggleEnabled({{ $background->id }})"
                                class="rounded bg-slate-100 px-2 py-1 text-slate-700 hover:bg-slate-200">
                            {{ $background->enabled ? 'Disable' : 'Enable' }}
                        </button>
                        <button type="button" wire:click="togglePinned({{ $background->id }})"
                                class="rounded px-2 py-1 {{ $background->pinned ? 'bg-amber-500 text-white hover:bg-amber-600' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                            {{ $background->pinned ? 'Unpin' : 'Pin' }}
                        </button>
                        <button type="button"
                                wire:click="delete({{ $background->id }})"
                                wire:confirm="Delete this background?"
                                class="ml-auto rounded bg-red-50 px-2 py-1 text-red-700 hover:bg-red-100">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-slate-500">No backgrounds yet. Create one to get started.</p>
        @endforelse
    </div>
</div>
