<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Blocks Manager') }} </flux:heading>
            <flux:subheading>{{ __('Manage reusable content blocks') }} ({{ $blocks->count() }})</flux:subheading>
        </div>
        <flux:button wire:click="create" variant="primary" icon="plus">
            {{ __('New Block') }}
        </flux:button>
    </div>

    {{-- Create / Edit Form --}}
    @if ($showForm)
        <flux:card class="space-y-4">
            <flux:heading size="lg">
                {{ $editingId ? __('Edit Block') : __('New Block') }}
            </flux:heading>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">


                @foreach (\Mcamara\LaravelLocalization\Facades\LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                    @php
                        $flagUrl = config("project.locale_flags.{$localeCode}");
                    @endphp

                    <flux:field>
                        <flux:label>
                            {{ __('Title') }} ({{ $properties['native'] }}) &nbsp;

                            @if ($flagUrl)
                                <img src="{{ $flagUrl }}" class="w-4 h-3 object-cover rounded-sm">
                            @endif

                            <span class="text-red-500 ms-1">*</span>
                        </flux:label>
                        <flux:input wire:model.live.debounce.300ms="title.{{ $localeCode }}"
                            placeholder="{{ __('Title') }} ({{ $properties['native'] }})" />
                        <flux:error name="title.{{ $localeCode }}" />
                    </flux:field>
                @endforeach




            </div>

            <div class="flex items-center gap-6">
                <flux:field variant="inline">
                    <flux:switch wire:model="show_title" />
                    <flux:label>{{ __('Show title on page') }}</flux:label>
                </flux:field>

                <flux:field variant="inline">
                    <flux:switch wire:model="is_active" />
                    <flux:label>{{ __('Active') }}</flux:label>
                </flux:field>


                <flux:field variant="inline">
                    <flux:switch wire:model="is_repeatable" />
                    <flux:label>{{ __('Repeatable Block') }}</flux:label>
                </flux:field>


            </div>

            <div class="flex items-center gap-3 pt-2">
                <flux:button wire:click="save" variant="primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">{{ __('Save') }}</span>
                    <span wire:loading wire:target="save">{{ __('Saving...') }}</span>
                </flux:button>
                <flux:button wire:click="cancelForm" variant="ghost">
                    {{ __('Cancel') }}
                </flux:button>
            </div>
        </flux:card>
    @endif

    {{-- Search --}}
    <flux:field>
        <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search blocks...') }}"
            icon="magnifying-glass" clearable />
    </flux:field>

    {{-- Blocks Table --}}
    <flux:card class="p-0 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800">
                    <th class="px-4 py-3 text-start font-medium text-zinc-500 dark:text-zinc-400">{{ __('Title') }}
                    </th>

                    <th class="px-4 py-3 text-start font-medium text-zinc-500 dark:text-zinc-400">{{ __('Fields') }}
                    </th>
                    <th class="px-4 py-3 text-start font-medium text-zinc-500 dark:text-zinc-400">{{ __('Status') }}
                    </th>


                    <th class="px-4 py-3 text-start font-medium text-zinc-500 dark:text-zinc-400">
                        {{ __('Created at') }}
                    </th>


                    <th class="px-4 py-3 text-start font-medium text-zinc-500 dark:text-zinc-400">
                        {{ __('Last Update') }}
                    </th>



                    <th class="px-4 py-3 text-start font-medium text-zinc-500 dark:text-zinc-400">{{ __('Actions') }}
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                @forelse ($blocks as $block)
                    <tr wire:key="block-{{ $block->id }}" class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">

                        <td class="px-4 py-3">
                            <div class="font-medium text-zinc-800 dark:text-zinc-100">
                                {{ $block->getTranslatedTitle('en') }}
                            </div>
                            @if (!$block->show_title)
                                <flux:badge color="zinc" size="sm">{{ __('Title hidden') }}</flux:badge>
                            @endif


                            @if ($block->is_repeatable)
                                <flux:badge color="purple" size="sm">{{ __('Repeatable') }}</flux:badge>
                            @endif

                        </td>



                        <td class="px-4 py-3">
                            <flux:badge color="blue" size="sm">
                                {{ $block->fields_count }} {{ __('fields') }}
                            </flux:badge>
                        </td>

                        <td class="px-4 py-3">
                            <flux:switch wire:click="toggleActive({{ $block->id }})" :checked="$block->is_active" />
                        </td>


                        <td class="px-4 py-3">

                            <div class="flex flex-col">
                                <span class="font-medium text-zinc-800 dark:text-zinc-100">
                                    {{ $block->created_at->diffForHumans() }}
                                </span>
                                <span class="text-zinc-400 text-[11px]">
                                    {{ $block->created_at->format('d M, Y - h:i A') }}
                                </span>
                            </div>
                        </td>

                        <td class="px-4 py-3">

                            <div class="flex flex-col">
                                <span class="font-medium text-zinc-800 dark:text-zinc-100">
                                    @if ($block->updated_at)
                                        {{ $block->updated_at->diffForHumans() }}
                                    @endif
                                </span>
                                <span class="text-zinc-400 text-[11px]">
                                    @if ($block->updated_at)
                                        {{ $block->updated_at->format('d M, Y - h:i A') }}
                                    @endif
                                </span>
                            </div>

                        </td>


                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1">

                                <flux:tooltip content="Edit">
                                    <flux:button wire:click="edit({{ $block->id }})" size="sm" variant="ghost"
                                        icon="pencil" />
                                </flux:tooltip>

                                <flux:tooltip content="Fields">
                                    <flux:button href="{{ route('admin.blocks.fields', $block) }}" wire:navigate
                                        size="sm" variant="ghost" icon="squares-2x2" />
                                </flux:tooltip>


                                <flux:tooltip content="Delete">
                                    <flux:button wire:click="confirmDelete({{ $block->id }})" size="sm"
                                        variant="ghost" icon="trash" class="text-red-500" />
                                </flux:tooltip>



                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-zinc-400">
                            {{ $search ? __('No blocks found.') : __('No blocks yet. Create your first block.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </flux:card>

    {{-- Pagination --}}
    <div>{{ $blocks->links() }}</div>


    {{-- Delete Confirm Modal --}}
    <x-admin.confirm-delete-js :module="$module" delete-action="delete" />
</div>
