<div class="space-y-6">
    <div class="space-y-6">
        @forelse ($page->blocks as $block)
            @php $pivot = $block->pivot; @endphp

            <flux:card class="space-y-4" wire:key="block-{{ $pivot->id }}">

                {{-- Block Header --}}
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">


                        <div class="space-y-2">
                            <flux:heading size="lg">{{ __('Title') }}</flux:heading>

                            @foreach (\Mcamara\LaravelLocalization\Facades\LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                @php
                                    $flagUrl = config("project.locale_flags.{$localeCode}");
                                @endphp

                                <div class="flex items-center gap-2">
                                    @if ($flagUrl)
                                        <img src="{{ $flagUrl }}" class="w-4 h-3 object-cover rounded-sm">
                                    @endif

                                    <span class="text-sm text-zinc-500">{{ $properties['native'] }}:</span>
                                    <span>{{ $block->title[$localeCode] ?? '-' }}</span>
                                </div>
                            @endforeach
                        </div>
                        @if ($block->is_repeatable)
                            <flux:badge color="purple">{{ __('Repeatable') }}</flux:badge>
                        @endif
                    </div>
                    @if (empty($block->fields))
                        <flux:button wire:click="saveBlock({{ $pivot->id }})" variant="primary" size="sm"
                            wire:loading.attr="disabled" wire:target="saveBlock({{ $pivot->id }})">
                            <span wire:loading.remove
                                wire:target="saveBlock({{ $pivot->id }})">{{ __('Save') }}</span>
                            <span wire:loading
                                wire:target="saveBlock({{ $pivot->id }})">{{ __('Saving...') }}</span>
                        </flux:button>
                    @else
                        <div class="flex flex-col items-center justify-center gap-3 py-8 text-center">
                            <flux:subheading>
                                {{ __('No fields found in this block .. Add Field') }}
                            </flux:subheading>

                            <flux:button size="sm" variant="primary" icon="arrow-path"
                                href="{{ route('admin.blocks.fields', $block->id) }}">
                                {{ __('Sync Now') }}
                            </flux:button>
                        </div>
                    @endif


                </div>

                @php
                    $rows = $formValues[$pivot->id] ?? [];
                @endphp

                @if ($block->is_repeatable)
                    <div class="space-y-4">
                        @foreach ($rows as $rowIndex => $rowFields)
                            <div wire:key="block-{{ $pivot->id }}-row-{{ $rowIndex }}"
                                class="border border-zinc-200 dark:border-zinc-700 rounded-xl p-4 space-y-4">
                                {{-- Row Header --}}
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-zinc-500">
                                        {{ __('Row') }} # {{ $loop->iteration }}
                                    </span>
                                    @if (count($rows) > 1)
                                        <flux:button
                                            wire:click="confirmDelete({{ $pivot->id }}, {{ $rowIndex }})"
                                            size="sm" variant="ghost" icon="trash" class="text-red-400" />
                                    @endif
                                </div>

                                {{-- Fields --}}
                                <div class="grid grid-cols-1 gap-4">
                                    @foreach ($block->fields as $field)
                                        @include('livewire.admin.cms.blocks._field', [
                                            'field' => $field,
                                            'pivotId' => $pivot->id,
                                            'row' => $rowIndex,
                                        ])
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        {{-- Add Row --}}
                        <flux:button wire:click="addRow({{ $pivot->id }})" variant="ghost" icon="plus"
                            class="w-full border border-dashed border-zinc-300 dark:border-zinc-600">
                            {{ __('Add Row') }}
                        </flux:button>
                    </div>

                    {{-- ============================================================ --}}
                    {{-- NORMAL BLOCK — row = 0                                       --}}
                    {{-- ============================================================ --}}
                @else
                    <div class="grid grid-cols-1 gap-4">
                        @foreach ($block->fields as $field)
                            @include('livewire.admin.cms.blocks._field', [
                                'field' => $field,
                                'pivotId' => $pivot->id,
                                'row' => 0,
                            ])
                        @endforeach
                    </div>
                @endif

            </flux:card>
        @empty

            <flux:card class="flex flex-col items-center justify-center text-center border-dashed border-2">
                <p class="text-center text-zinc-400 py-8">{{ __('No blocks attached to this page yet.') }}</p>

                <flux:button variant="primary" icon="arrow-path" href="{{ route('admin.pages.edit', $page) }}">
                    Manage Blocks
                </flux:button>
            </flux:card>
        @endforelse
    </div>


    <flux:modal wire:model="showDeleteModal">
        <div class="space-y-4">

            <flux:heading size="lg">
                {{ __('Confirm Delete') }}
            </flux:heading>

            <p class="text-sm text-zinc-500">
                {{ __('Are you sure you want to delete this row?') }}
            </p>

            <div class="flex justify-end gap-2">
                <flux:button variant="ghost" wire:click="resetDeleteState">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button variant="danger" wire:click="deleteRow">
                    {{ __('Delete') }}
                </flux:button>
            </div>

        </div>
    </flux:modal>

</div>
