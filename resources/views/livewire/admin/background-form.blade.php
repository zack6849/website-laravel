<div class="space-y-6">
    @php
        $previewBackground = $this->previewBackground;
        $cropControls = [
            'base' => [
                'legend' => 'Mobile crop',
                'focusX' => 'focus.base.x',
                'focusY' => 'focus.base.y',
                'focusSize' => 'focus.base.size',
                'fieldX' => 'form.position.x',
                'fieldY' => 'form.position.y',
                'fieldSize' => 'form.size',
            ],
            'sm' => [
                'legend' => 'Tablet crop',
                'focusX' => 'focus.sm.x',
                'focusY' => 'focus.sm.y',
                'focusSize' => 'focus.sm.size',
                'fieldX' => 'form.sm.x',
                'fieldY' => 'form.sm.y',
                'fieldSize' => 'form.sm.size',
            ],
            'lg' => [
                'legend' => 'Desktop crop',
                'focusX' => 'focus.lg.x',
                'focusY' => 'focus.lg.y',
                'focusSize' => 'focus.lg.size',
                'fieldX' => 'form.lg.x',
                'fieldY' => 'form.lg.y',
                'fieldSize' => 'form.lg.size',
            ],
        ];
        $activeCrop = $cropControls[$previewBreakpoint] ?? $cropControls['base'];
    @endphp

    <form wire:submit="save" class="space-y-6">
        {{-- Live preview --}}
        <div>
            <div class="mb-2 flex items-center justify-between">
                <span class="text-sm font-semibold text-slate-700">Live preview</span>
                <div class="inline-flex overflow-hidden rounded border border-slate-300 text-xs">
                    @foreach (['base' => 'Mobile', 'sm' => 'Tablet', 'lg' => 'Desktop'] as $bp => $label)
                        <button type="button" wire:click="$set('previewBreakpoint', '{{ $bp }}')"
                                class="px-3 py-1 {{ $previewBreakpoint === $bp ? 'bg-brand-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>
            <div class="top-banner-preview-shell top-banner-preview-shell-{{ $previewBreakpoint }}">
                <x-backgrounds.home-banner
                    :background="$previewBackground"
                    :full-bleed="false"
                    :preview-breakpoint="$previewBreakpoint"
                    empty-message="Upload or set an image to preview"
                />
            </div>
        </div>

        {{-- Crop --}}
        <fieldset wire:key="crop-controls-{{ $previewBreakpoint }}" class="rounded-lg border border-slate-200 p-4">
            <legend class="px-1 text-sm font-semibold text-slate-700">{{ $activeCrop['legend'] }}</legend>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div>
                    <label class="auth-label">Focus X</label>
                    <input type="range" min="-50" max="250" step="0.1" wire:model.live="{{ $activeCrop['focusX'] }}" class="w-full accent-brand-600">
                    <input type="text" wire:model.live.debounce.150ms="{{ $activeCrop['fieldX'] }}" placeholder="50%" class="auth-input mt-2">
                    @error($activeCrop['fieldX']) <p class="auth-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="auth-label">Focus Y</label>
                    <input type="range" min="-50" max="250" step="0.1" wire:model.live="{{ $activeCrop['focusY'] }}" class="w-full accent-brand-600">
                    <input type="text" wire:model.live.debounce.150ms="{{ $activeCrop['fieldY'] }}" placeholder="50%" class="auth-input mt-2">
                    @error($activeCrop['fieldY']) <p class="auth-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="auth-label">Size</label>
                    <input type="range" min="25" max="300" step="0.5" wire:model.live="{{ $activeCrop['focusSize'] }}" class="w-full accent-brand-600">
                    <input type="text" wire:model.live.debounce.150ms="{{ $activeCrop['fieldSize'] }}" placeholder="cover" class="auth-input mt-2">
                    @error($activeCrop['fieldSize']) <p class="auth-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </fieldset>

        {{-- Basics --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="auth-label">Title</label>
                <input type="text" wire:model.live="form.title" class="auth-input">
                @error('form.title') <p class="auth-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="auth-label">Weight (random likelihood)</label>
                <input type="number" min="1" max="1000" wire:model="form.weight" class="auth-input">
                @error('form.weight') <p class="auth-error">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="auth-label">Description (shown in the info tooltip)</label>
            <textarea wire:model.live.debounce.150ms="form.description" rows="2" class="auth-input"></textarea>
            @error('form.description') <p class="auth-error">{{ $message }}</p> @enderror
        </div>

        {{-- Image --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="auth-label">Upload image</label>
                <input type="file" wire:model="form.upload" accept="image/*" class="block w-full text-sm">
                <div wire:loading wire:target="form.upload" class="mt-1 text-xs text-slate-500">Uploading…</div>
                @error('form.upload') <p class="auth-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="auth-label">…or image path / URL</label>
                <input type="text" wire:model.live="form.image" placeholder="img/bg/example.jpg" class="auth-input">
                @error('form.image') <p class="auth-error">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="auth-label">Overlay darkness ({{ number_format($form->overlay, 2) }})</label>
            <input type="range" min="0" max="1" step="0.01" wire:model.live="form.overlay" class="w-full">
        </div>

        {{-- Schedule / themed days --}}
        <fieldset class="rounded-lg border border-slate-200 p-4">
            <legend class="px-1 text-sm font-semibold text-slate-700">Themed days (optional)</legend>
            <p class="mb-3 text-xs text-slate-500">
                When any rule matches the current day, this background is preferred over the random pool.
            </p>
            @error('form.schedule') <p class="auth-error">{{ $message }}</p> @enderror

            <div class="space-y-3">
                @foreach ($form->schedule as $i => $rule)
                    <div class="flex flex-wrap items-end gap-3 rounded border border-slate-200 bg-slate-50 p-3">
                        <div>
                            <label class="auth-label">Type</label>
                            <select wire:model.live="form.schedule.{{ $i }}.type" class="auth-input">
                                <option value="date">Specific date (yearly)</option>
                                <option value="weekday">Weekday(s)</option>
                                <option value="range">Date range</option>
                            </select>
                        </div>

                        @if (($rule['type'] ?? 'date') === 'date')
                            <div>
                                <label class="auth-label">Month</label>
                                <input type="number" min="1" max="12" wire:model="form.schedule.{{ $i }}.month" class="auth-input w-24">
                            </div>
                            <div>
                                <label class="auth-label">Day</label>
                                <input type="number" min="1" max="31" wire:model="form.schedule.{{ $i }}.day" class="auth-input w-24">
                            </div>
                        @elseif (($rule['type'] ?? '') === 'weekday')
                            <div>
                                <label class="auth-label">Days</label>
                                <div class="flex flex-wrap gap-2">
                                    @foreach (['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $dow => $dowLabel)
                                        <label class="inline-flex items-center gap-1 text-sm">
                                            <input type="checkbox" value="{{ $dow }}" wire:model="form.schedule.{{ $i }}.days">
                                            {{ $dowLabel }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @elseif (($rule['type'] ?? '') === 'range')
                            <div>
                                <label class="auth-label">Start</label>
                                <input type="date" wire:model="form.schedule.{{ $i }}.start" class="auth-input">
                            </div>
                            <div>
                                <label class="auth-label">End</label>
                                <input type="date" wire:model="form.schedule.{{ $i }}.end" class="auth-input">
                            </div>
                        @endif

                        <button type="button" wire:click="removeRule({{ $i }})"
                                class="ml-auto rounded bg-red-50 px-2 py-1 text-sm text-red-700 hover:bg-red-100">
                            Remove
                        </button>
                    </div>
                @endforeach
            </div>

            <div class="mt-3 flex flex-wrap gap-2">
                <button type="button" wire:click="addRule('date')" class="rounded bg-slate-100 px-3 py-1 text-sm hover:bg-slate-200">+ Date</button>
                <button type="button" wire:click="addRule('weekday')" class="rounded bg-slate-100 px-3 py-1 text-sm hover:bg-slate-200">+ Weekday</button>
                <button type="button" wire:click="addRule('range')" class="rounded bg-slate-100 px-3 py-1 text-sm hover:bg-slate-200">+ Range</button>
            </div>
        </fieldset>

        {{-- Flags --}}
        <div class="flex flex-wrap gap-6">
            <label class="inline-flex items-center gap-2">
                <input type="checkbox" wire:model="form.enabled" class="auth-checkbox"> Enabled
            </label>
            <label class="inline-flex items-center gap-2">
                <input type="checkbox" wire:model="form.pinned" class="auth-checkbox"> Pin as active override
            </label>
        </div>
        @error('form.enabled') <p class="auth-error">{{ $message }}</p> @enderror

        <div class="flex items-center gap-3">
            <button type="submit" class="auth-button">Save background</button>
            <a href="{{ route('admin.backgrounds.index') }}" class="auth-link">Cancel</a>
        </div>
    </form>
</div>
