<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ ucfirst($module) }}</flux:heading>
            <flux:subheading>
                {{ __('Manage system :module', ['module' => Str::lower($module)]) }} ({{ $row->count() }})
            </flux:subheading>
        </div>
        @if (auth('admin')->user()
                ?->can($permissionPrefix . '.create'))
            <flux:button href="{{ route('admin.' . $route . '.create') }}" wire:navigate variant="primary" icon="plus">
                {{ __('Add New ' . ucfirst(\Str::singular($module))) }}
            </flux:button>
        @endif
    </div>

    {{-- Search --}}





    <div class="flex gap-4">
        <flux:field class="flex-1">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by title...') }}"
                icon="magnifying-glass" clearable />
        </flux:field>

        <flux:field>
            <flux:select wire:model.live="activeFilter" title="{{ __('Filter by status') }}"
                placeholder="{{ __('Filter by status') }}" clearable>
                <flux:select.option value="">{{ __('All Status') }}</flux:select.option>
                <flux:select.option value="1">{{ __('Active') }}</flux:select.option>
                <flux:select.option value="0">{{ __('Inactive') }}</flux:select.option>
            </flux:select>
        </flux:field>
    </div>





    @if ($this->counts['trash'] > 0)
        <div class="flex items-center gap-4 mb-4 border-b border-zinc-200 dark:border-zinc-700">
            <button wire:click="setFilter('all')"
                class="pb-2 text-sm font-medium border-b-2 transition
            {{ $statusFilter === 'all'
                ? 'border-zinc-900 text-zinc-900 dark:text-white dark:border-white'
                : 'border-transparent text-zinc-500 hover:text-zinc-700' }}">
                {{ __('All') }} ({{ $this->counts['all'] }})
            </button>

            <button wire:click="setFilter('trash')"
                class="pb-2 text-sm font-medium border-b-2 transition
            {{ $statusFilter === 'trash'
                ? 'border-red-600 text-red-600'
                : 'border-transparent text-zinc-500 hover:text-red-500' }}">
                {{ __('Trash') }} ({{ $this->counts['trash'] }})
            </button>
        </div>
    @endif



    {{-- Table --}}
    <flux:card class="p-0 overflow-hidden" wire:loading.class="opacity-50 pointer-events-none">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800">
                    <th class="px-4 py-3 text-start">
                        <button wire:click="sort('title')"
                            class="flex items-center gap-1 font-medium text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200">
                            {{ __('Title') }}

                        </button>
                    </th>
                    <th class="px-4 py-3 text-start">
                        <button wire:click="sort('slug')"
                            class="flex items-center gap-1 font-medium text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200">
                            {{ __('Slug') }}

                        </button>
                    </th>

                    <th class="px-4 py-3 text-start">
                        <button wire:click="sort('is_active')"
                            class="flex items-center gap-1 font-medium text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200">
                            {{ __('Status') }}
                        </button>
                    </th>


                    <th class="px-4 py-3 text-start font-medium text-zinc-500 dark:text-zinc-400">{{ __('Template') }}
                    </th>
                    <th class="px-4 py-3 text-start">
                        <button wire:click="sort('created_at')"
                            class="flex items-center gap-1 font-medium text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200">
                            {{ __('Created at') }}


                            @if ($sortBy === 'created_at')
                                <flux:icon name="{{ $sortDir === 'asc' ? 'chevron-up' : 'chevron-down' }}"
                                    class="size-4 text-primary-500" />
                            @else
                                <flux:icon name="chevrons-up-down" class="size-4 opacity-40" />
                            @endif

                        </button>
                    </th>


                    <th class="px-4 py-3 text-start">
                        <button wire:click="sort('updated_at')"
                            class="flex items-center gap-1 font-medium text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200">
                            {{ __('Last Update') }}


                            @if ($sortBy === 'updated_at')
                                <flux:icon name="{{ $sortDir === 'asc' ? 'chevron-up' : 'chevron-down' }}"
                                    class="size-4 text-primary-500" />
                            @else
                                <flux:icon name="chevrons-up-down" class="size-4 opacity-40" />
                            @endif

                        </button>
                    </th>


                    <th class="px-4 py-3 text-end font-medium text-zinc-500 dark:text-zinc-400">{{ __('Actions') }}
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                @forelse ($row as $page)
                    <tr wire:key="page-{{ $page->id }}" class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">

                        <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">




                            <div class="flex items-center gap-3">



                                @if ($page->image)
                                    <img src="{{ asset('storage/' . $page->image) }}" alt="{{ $page->name }}"
                                        class="size-9 rounded-full object-cover shadow-sm shrink-0" />
                                @endif




                                {{ $page->translate->title }}

                            </div>
                        </td>




                        <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">
                            {{ $page->translate->slug }}
                        </td>


                        <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">
                            @if ($page->is_active)
                                <flux:badge color="green" size="sm" icon="check-circle">{{ __('Active') }}
                                </flux:badge>
                            @else
                                <flux:badge color="red" size="sm" icon="x-circle">{{ __('Inactive') }}
                                </flux:badge>
                            @endif
                        </td>

                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1">
                                {{ $page->template }}
                            </div>
                        </td>

                        <td class="px-4 py-3 text-zinc-400 text-xs">

                            <div class="flex flex-col">
                                <span class="font-medium text-zinc-800 dark:text-zinc-100">
                                    {{ $page->created_at->diffForHumans() }}
                                </span>
                                <span class="text-zinc-400 text-[11px]">
                                    {{ $page->created_at->format('d M, Y - h:i A') }}
                                </span>
                            </div>

                        </td>







                        <td class="px-4 py-3 text-zinc-400 text-xs">

                            <div class="flex flex-col">
                                <span class="font-medium text-zinc-800 dark:text-zinc-100">
                                    @if ($page->updated_at)
                                        {{ $page->updated_at->diffForHumans() }}
                                    @endif
                                </span>
                                <span class="text-zinc-400 text-[11px]">
                                    @if ($page->updated_at)
                                        {{ $page->updated_at->format('d M, Y - h:i A') }}
                                    @endif
                                </span>
                            </div>

                        </td>


                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                @php
                                    $auth = auth('admin')->user();
                                    $canEdit = $auth->can($permissionPrefix . '.edit');
                                    $canDelete = $auth->can($permissionPrefix . '.delete');
                                @endphp

                                @if ($statusFilter === 'trash')
                                    @if ($canDelete)
                                        <flux:tooltip content="Restore">
                                            <flux:button wire:click="restore({{ $page->id }})" size="sm"
                                                variant="ghost" icon="arrow-uturn-left" class="text-green-600" />
                                        </flux:tooltip>

                                        <flux:tooltip content="Delete Permanently">
                                            <flux:button wire:click="forceDelete({{ $page->id }})"
                                                wire:confirm="{{ __('Delete permanently? This cannot be undone.') }}"
                                                size="sm" variant="ghost" icon="trash" class="text-red-500" />
                                        </flux:tooltip>
                                    @endif
                                @else
                                    @if ($canEdit)
                                        <flux:tooltip content="Edit">
                                            <flux:button href="{{ route('admin.' . $route . '.edit', $page) }}"
                                                wire:navigate size="sm" variant="ghost" icon="pencil" />
                                        </flux:tooltip>
                                    @endif

                                    @if ($canDelete)
                                        <flux:tooltip content="Delete">
                                            <flux:button wire:click="confirmDelete({{ $page->id }})" size="sm"
                                                variant="ghost" icon="trash" class="text-red-500" />
                                        </flux:tooltip>
                                    @endif

                                    <flux:tooltip content="Blocks">
                                        <flux:button href="{{ route('admin.pages.manage_blocks', $page) }}"
                                            wire:navigate size="sm" variant="ghost" icon="cube" />
                                    </flux:tooltip>
                                @endif
                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-zinc-400">
                            {{ $search ? __('No ' . $module . ' found.') : __('No ' . $module . ' yet.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </flux:card>

    {{-- Pagination --}}
    <div>{{ $row->links() }}</div>
    <x-admin.confirm-delete-js :module="$module" delete-action="delete" />
    {{-- Delete Confirm Modal --}}
</div>
