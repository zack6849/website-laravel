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

    @error('files')
        <div class="mb-4 rounded border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">{{ $message }}</div>
    @enderror

    <div class="mb-4 border border-gray-200 bg-white p-3">
        <div class="grid gap-3 md:grid-cols-[minmax(16rem,1fr)_10rem_10rem_auto] md:items-end">
            <label class="block">
                <span class="block text-sm font-semibold text-gray-700">Search</span>
                <input
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    class="form-control mt-1 w-full"
                    placeholder="Filename or type"
                >
            </label>

            <label class="block">
                <span class="block text-sm font-semibold text-gray-700">Created from</span>
                <input type="date" wire:model.live="createdFrom" class="form-control mt-1 w-full">
            </label>

            <label class="block">
                <span class="block text-sm font-semibold text-gray-700">Created to</span>
                <input type="date" wire:model.live="createdTo" class="form-control mt-1 w-full">
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
                        <th scope="col" class="px-3 py-2 font-semibold">Name</th>
                        <th scope="col" class="px-3 py-2 font-semibold">Uploaded At</th>
                        <th scope="col" class="px-3 py-2 font-semibold">Type</th>
                        <th scope="col" class="px-3 py-2 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($this->files as $file)
                        <tr wire:key="file-{{ $file->id }}">
                            <td class="px-3 py-3">
                                <a href="{{ route('file.show', $file->filename) }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="text-brand-700 underline hover:text-brand-900"
                                >
                                    {{ $file->original_filename }}
                                </a>
                            </td>
                            <td class="whitespace-nowrap px-3 py-3 text-gray-700">
                                <div>
                                    <time datetime="{{ $file->created_at->toIso8601String() }}">
                                        {{ $file->created_at->format('M j, Y, g:i A') }}
                                    </time>
                                </div>
                                <div class="text-xs text-gray-500">{{ $file->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="whitespace-nowrap px-3 py-3 text-gray-700">{{ $file->mime }}</td>
                            <td class="whitespace-nowrap px-3 py-3 text-right">
                                <button type="button"
                                        wire:click="delete({{ $file->id }})"
                                        wire:confirm="Delete {{ $file->original_filename }}? This cannot be undone."
                                        class="text-red-700 underline hover:text-red-900">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-3 py-6 text-center text-gray-500">
                                No files found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-200 px-3 py-2">
            {{ $this->files->links() }}
        </div>
    </div>
</div>
