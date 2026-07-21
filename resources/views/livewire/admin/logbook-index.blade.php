<div>
    @if ($status !== null)
        <div class="notification-stack">
            <div
                class="notification-message notification-message-success"
                role="alert"
                data-notification-message
                data-notification-dismiss-after="10000"
            >
                <button
                    type="button"
                    class="notification-dismiss"
                    aria-label="Dismiss notification"
                    data-notification-dismiss
                >
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
                <i class="notification-icon fa fa-check-circle" aria-hidden="true"></i>
                <div>{{ $status }}</div>
            </div>
        </div>
    @endif

    <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <p class="text-sm text-gray-600">
            @if ($this->lastImportedAt)
                Last QRZ import:
                <time
                    datetime="{{ $this->lastImportedAt->utc()->toIso8601String() }}"
                    data-relative-utc-time="{{ $this->lastImportedAt->utc()->toIso8601String() }}"
                    data-relative-utc-time-prefix="Imported"
                >
                    Imported at {{ $this->lastImportedAt->utc()->format('M j, Y, g:i A') }} UTC
                </time>
            @else
                No QRZ import recorded yet
            @endif
        </p>
        <button type="button" wire:click="import" class="btn-primary whitespace-nowrap">
            Force QRZ Re-import
        </button>
    </div>

    <div class="mb-4 border border-gray-200 bg-white p-3">
        <div class="grid gap-3 md:grid-cols-[minmax(16rem,1fr)_12rem_auto] md:items-end">
            <label class="block">
                <span class="block text-sm font-semibold text-gray-700">Search</span>
                <input
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    class="form-control mt-1 w-full"
                    placeholder="Callsign, grid, comments, QRZ log id"
                >
            </label>

            <label class="block">
                <span class="block text-sm font-semibold text-gray-700">Visibility</span>
                <select wire:model.live="visibility" class="form-control mt-1 w-full">
                    <option value="all">All</option>
                    <option value="public">Public</option>
                    <option value="hidden">Hidden</option>
                </select>
            </label>

            <div class="flex gap-2">
                <button type="button" wire:click="resetFilters" class="auth-link self-center">Reset</button>
            </div>
        </div>
    </div>

    <div class="overflow-hidden border border-gray-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th scope="col" class="px-3 py-2 font-semibold">Date</th>
                        <th scope="col" class="px-3 py-2 font-semibold">Station</th>
                        <th scope="col" class="px-3 py-2 font-semibold">Details</th>
                        <th scope="col" class="px-3 py-2 font-semibold">Location</th>
                        <th scope="col" class="px-3 py-2 font-semibold">QRZ</th>
                        <th scope="col" class="px-3 py-2 font-semibold">Status</th>
                        <th scope="col" class="px-3 py-2 text-right font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($this->entries as $entry)
                        @php($qsoTimestamp = $entry->created_at)
                        <tr @class(['bg-amber-50' => $entry->hidden_from_public]) wire:key="entry-{{ $entry->id }}">
                            <td class="whitespace-nowrap px-3 py-3 text-gray-700">
                                <div>
                                    @if ($qsoTimestamp)
                                        <time
                                            datetime="{{ $qsoTimestamp->toIso8601String() }}"
                                        >{{ $qsoTimestamp->format('M j, Y, g:i A') }}</time>
                                    @else
                                        -
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500">{{ $qsoTimestamp?->diffForHumans() }}</div>
                            </td>
                            <td class="px-3 py-3">
                                <div class="font-semibold text-gray-900">{{ $entry->callee?->name }}</div>
                                <div class="text-xs text-gray-500">{{ $entry->callee?->country }}</div>
                            </td>
                            <td class="whitespace-nowrap px-3 py-3 text-gray-700">
                                <div>{{ $entry->band }} {{ $entry->mode }}</div>
                                <div class="text-xs text-gray-500">{{ $entry->frequency }} MHz</div>
                            </td>
                            <td class="px-3 py-3 text-gray-700">
                                <div>{{ $entry->to_grid ?: 'No grid' }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ $entry->distance !== null ? $entry->distance . ' mi' : 'No distance' }}
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-3 py-3 text-gray-700">
                                {{ $entry->qrz_logid ?: 'Unavailable' }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-3">
                                @if ($entry->hidden_from_public)
                                    <span class="bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-800">Hidden</span>
                                @else
                                    <span class="bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-800">Public</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-3 py-3 text-right">
                                @if ($entry->hidden_from_public)
                                    <button type="button" wire:click="show({{ $entry->id }})"
                                            class="text-brand-700 underline hover:text-brand-900">
                                        Show
                                    </button>
                                @else
                                    <button type="button" wire:click="hide({{ $entry->id }})"
                                            class="text-red-700 underline hover:text-red-900">
                                        Hide
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-6 text-center text-gray-500">
                                No QSOs found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-200 px-3 py-2">
            {{ $this->entries->links() }}
        </div>
    </div>
</div>
