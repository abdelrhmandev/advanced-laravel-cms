<div
    x-data="blockBuilder()"
    x-init="initSortable()"
    class="flex gap-6 items-start"
>

    {{-- ====================================================
         Left — page blocks list
    ==================================================== --}}
    Block Builder
    <div class="flex-1 space-y-4 min-w-0">

        {{-- Page header --}}
        <flux:card class="space-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="lg">{{ $page->title }}</flux:heading>
                    <code class="text-xs text-zinc-400">{{ $page->slug }}</code>
                </div>
                <flux:button
                    wire:click="$set('showBlockPicker', true)"
                    variant="primary"
                    icon="plus"
                    size="sm"
                >
                    {{ __('Add Block') }}
                </flux:button>
            </div>
        </flux:card>

        {{-- Block picker --}}
        @if ($showBlockPicker)
            <flux:card class="space-y-3">
                <div class="flex items-center justify-between">
                    <flux:heading size="sm">{{ __('Choose a block') }}</flux:heading>
                    <flux:button
                        wire:click="$set('showBlockPicker', false)"
                        size="sm" variant="ghost" icon="x-mark"
                    />
                </div>

                @forelse ($availableBlocks as $block)
                    <button
                        wire:click="attachBlock({{ $block->id }})"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-lg border border-zinc-200 dark:border-zinc-700 hover:border-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors text-start"
                    >
                        <div>
                            <div class="text-sm font-medium text-zinc-800 dark:text-zinc-100">{{ $block->title }}</div>
                            <code class="text-xs text-zinc-400">{{ $block->slug }}</code>
                        </div>
                        <flux:badge color="blue" size="sm">
                            {{ $block->fields->count() }} {{ __('fields') }}
                        </flux:badge>
                    </button>
                @empty
                    <p class="text-sm text-zinc-400 text-center py-4">{{ __('All blocks are already added.') }}</p>
                @endforelse
            </flux:card>
        @endif

        {{-- Blocks list --}}
        <flux:card class="p-0 overflow-hidden">
            <div class="px-4 py-3 border-b border-zinc-100 dark:border-zinc-700">
                <flux:heading size="sm">{{ __('Page Blocks') }}</flux:heading>
            </div>

            <ul id="page-blocks-sortable" class="divide-y divide-zinc-100 dark:divide-zinc-700">
                @forelse ($pageBlocks as $block)
                    @php $pb = $block->pivot; @endphp
                    <li
                        wire:key="pb-{{ $pb->id }}"
                        data-id="{{ $pb->id }}"
                        class="flex items-center gap-3 px-4 py-3 group hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors"
                        :class="{ 'bg-primary-50 dark:bg-primary-900/20 border-s-2 border-primary-400': activeBlock === {{ $pb->id }} }"
                    >
                        {{-- Drag handle --}}
                        <span class="drag-handle cursor-grab text-zinc-300 hover:text-zinc-500">
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                            </svg>
                        </span>

                        {{-- Block info --}}
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-zinc-800 dark:text-zinc-100 truncate">
                                {{ $block->title }}
                            </div>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <code class="text-xs text-zinc-400">{{ $block->slug }}</code>
                                @if (! $pb->is_visible)
                                    <flux:badge color="zinc" size="sm">{{ __('hidden') }}</flux:badge>
                                @endif
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-1">
                            {{-- Fill values --}}
                            <flux:button
                                wire:click="openBlock({{ $pb->id }})"
                                size="sm"
                                variant="{{ $activePageBlockId === $pb->id ? 'primary' : 'ghost' }}"
                                icon="pencil-square"
                                x-on:click="activeBlock = {{ $pb->id }}"
                            />

                            {{-- Toggle visibility --}}
                            <flux:button
                                wire:click="toggleVisibility({{ $pb->id }})"
                                size="sm"
                                variant="ghost"
                                icon="{{ $pb->is_visible ? 'eye' : 'eye-slash' }}"
                            />

                            {{-- Detach --}}
                            <flux:button
                                wire:click="detachBlock({{ $pb->id }})"
                                wire:confirm="{{ __('Remove this block from the page?') }}"
                                size="sm"
                                variant="ghost"
                                icon="trash"
                                class="text-red-400"
                            />
                        </div>
                    </li>
                @empty
                    <li class="py-12 text-center text-zinc-400 text-sm">
                        {{ __('No blocks added yet.') }}
                    </li>
                @endforelse
            </ul>
        </flux:card>

    </div>

    {{-- ====================================================
         Right — field values form
    ==================================================== --}}
    @if ($activePageBlock)
        <div class="w-96 shrink-0 space-y-4">

            <flux:card class="space-y-4">
                <div class="flex items-center justify-between">
                    <flux:heading size="sm">{{ $activePageBlock->block->title }}</flux:heading>
                    <flux:button
                        wire:click="$set('activePageBlockId', null)"
                        size="sm" variant="ghost" icon="x-mark"
                    />
                </div>

                {{-- Fields --}}
                @foreach ($activePageBlock->block->fields as $field)
                    <div wire:key="field-{{ $field->id }}" class="space-y-2">

                        {{-- Field label --}}
                        <div class="flex items-center gap-2">
                            <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                {{ $field->label }}
                                @if ($field->required)
                                    <span class="text-red-400">*</span>
                                @endif
                            </label>
                            @if ($field->translatable)
                                <flux:badge color="purple" size="sm">{{ __('trans') }}</flux:badge>
                            @endif
                            @if ($field->repeatable)
                                <flux:badge color="blue" size="sm">{{ __('repeat') }}</flux:badge>
                            @endif
                            @if ($field->hint)
                                <span class="text-xs text-zinc-400">— {{ $field->hint }}</span>
                            @endif
                        </div>

                        {{-- ── REPEATER ── --}}
                        @if ($field->type === 'repeater')
                            <div class="space-y-3">
                                @foreach ($values[$field->id] ?? [] as $rowIndex => $row)
                                    <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-3 space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs text-zinc-400">{{ __('Row') }} {{ $rowIndex + 1 }}</span>
                                            <div class="flex gap-1">
                                                <flux:button
                                                    wire:click="moveRepeaterRow({{ $field->id }}, {{ $rowIndex }}, 'up')"
                                                    size="sm" variant="ghost" icon="arrow-up"
                                                    :disabled="$rowIndex === 0"
                                                />
                                                <flux:button
                                                    wire:click="moveRepeaterRow({{ $field->id }}, {{ $rowIndex }}, 'down')"
                                                    size="sm" variant="ghost" icon="arrow-down"
                                                />
                                                <flux:button
                                                    wire:click="removeRepeaterRow({{ $field->id }}, {{ $rowIndex }})"
                                                    size="sm" variant="ghost" icon="trash"
                                                    class="text-red-400"
                                                />
                                            </div>
                                        </div>

                                        @foreach ($field->getSubFields() as $sub)
                                            <div class="space-y-1">
                                                <label class="text-xs font-medium text-zinc-600 dark:text-zinc-400">
                                                    {{ $sub['label'] ?? $sub['key'] }}
                                                </label>

                                                @if ($sub['translatable'] ?? false)
                                                    @foreach (config('app.locales', ['ar', 'en']) as $locale)
                                                        <flux:input
                                                            wire:model="values.{{ $field->id }}.{{ $rowIndex }}.{{ $sub['key'] }}.{{ $locale }}"
                                                            placeholder="{{ strtoupper($locale) }}"
                                                            size="sm"
                                                        />
                                                    @endforeach
                                                @else
                                                    <flux:input
                                                        wire:model="values.{{ $field->id }}.{{ $rowIndex }}.{{ $sub['key'] }}"
                                                        size="sm"
                                                    />
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach

                                <flux:button
                                    wire:click="addRepeaterRow({{ $field->id }})"
                                    size="sm" variant="ghost" icon="plus"
                                >
                                    {{ __('Add row') }}
                                </flux:button>
                            </div>

                        {{-- ── TRANSLATABLE (non-repeater) ── --}}
                        @elseif ($field->translatable)
                            <div class="space-y-2">
                                @foreach (config('app.locales', ['ar', 'en']) as $locale)
                                    <div class="flex items-center gap-2">
                                        <span class="w-8 text-center text-xs font-medium text-zinc-400 uppercase shrink-0">
                                            {{ $locale }}
                                        </span>
                                        @if ($field->type === 'textarea')
                                            <flux:textarea
                                                wire:model="values.{{ $field->id }}.{{ $locale }}"
                                                rows="2"
                                                dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}"
                                            />
                                        @else
                                            <flux:input
                                                wire:model="values.{{ $field->id }}.{{ $locale }}"
                                                dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}"
                                            />
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                        {{-- ── REPEATABLE scalar ── --}}
                        @elseif ($field->repeatable)
                            <div class="space-y-2">
                                @foreach ($values[$field->id] ?? [] as $i => $val)
                                    <div class="flex items-center gap-2">
                                        <flux:input
                                            wire:model="values.{{ $field->id }}.{{ $i }}"
                                            class="flex-1"
                                        />
                                        <flux:button
                                            wire:click="$set('values.{{ $field->id }}', array_values(array_filter(array_map(fn($k,$v) => $k === {{ $i }} ? null : $v, array_keys($values[{{ $field->id }}] ?? []), $values[{{ $field->id }}] ?? []))))"
                                            size="sm" variant="ghost" icon="x-mark"
                                            class="text-red-400 shrink-0"
                                        />
                                    </div>
                                @endforeach
                                <flux:button
                                    wire:click="$push('values.{{ $field->id }}', '')"
                                    size="sm" variant="ghost" icon="plus"
                                >
                                    {{ __('Add item') }}
                                </flux:button>
                            </div>

                        {{-- ── SINGLE FIELDS ── --}}
                        @elseif ($field->type === 'text')
                            <flux:input wire:model="values.{{ $field->id }}" />

                        @elseif ($field->type === 'textarea')
                            <flux:textarea wire:model="values.{{ $field->id }}" rows="3" />

                        @elseif ($field->type === 'image')
                            <flux:input
                                wire:model="values.{{ $field->id }}"
                                placeholder="/storage/..."
                                dir="ltr"
                            />

                        @elseif ($field->type === 'select')
                            <select
                                wire:model="values.{{ $field->id }}"
                                class="w-full rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 text-sm text-zinc-800 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-primary-500"
                                {{ ($field->settings['multiple'] ?? false) ? 'multiple' : '' }}
                            >
                                <option value="">{{ __('— Choose —') }}</option>
                                @foreach ($field->getOptions() as $opt)
                                    <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                                @endforeach
                            </select>

                        @elseif ($field->type === 'color')
                            <div class="flex items-center gap-2">
                                <input
                                    type="color"
                                    wire:model="values.{{ $field->id }}"
                                    class="h-9 w-12 cursor-pointer rounded border border-zinc-200 dark:border-zinc-700 bg-transparent p-0.5"
                                />
                                <flux:input
                                    wire:model="values.{{ $field->id }}"
                                    placeholder="#000000"
                                    dir="ltr"
                                    class="flex-1"
                                />
                            </div>
                        @endif

                    </div>

                    <flux:separator />
                @endforeach

                {{-- Save --}}
                <flux:button
                    wire:click="saveValues"
                    variant="primary"
                    class="w-full"
                    wire:loading.attr="disabled"
                    wire:target="saveValues"
                >
                    <span wire:loading.remove wire:target="saveValues">{{ __('Save') }}</span>
                    <span wire:loading wire:target="saveValues">{{ __('Saving...') }}</span>
                </flux:button>

            </flux:card>
        </div>
    @endif

</div>

@script
<script>
    function blockBuilder() {
        return {
            activeBlock: null,

            initSortable() {
                const el = document.getElementById('page-blocks-sortable');
                if (!el || typeof Sortable === 'undefined') return;

                Sortable.create(el, {
                    handle: '.drag-handle',
                    animation: 150,
                    ghostClass: 'opacity-40',
                    onEnd: () => {
                        const order = [...el.querySelectorAll('[data-id]')]
                            .map(li => parseInt(li.dataset.id));

                        $wire.dispatch('blocks-reordered', { order });
                    },
                });
            },
        };
    }
</script>
@endscript
