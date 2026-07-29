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

    {{-- Filters --}}
    <div class="flex gap-4">
        <flux:field class="flex-1">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by name or email...') }}"
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

    {{-- Table --}}
    <flux:card class="p-0 overflow-hidden" wire:loading.class="opacity-50 pointer-events-none">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800">
                    <th class="px-4 py-3 text-start">
                        <button wire:click="sort('name')"
                            class="flex items-center gap-1 font-medium text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200">
                            {{ __('Name') }}
                            @if ($sortBy === 'name')
                                <flux:icon name="{{ $sortDir === 'asc' ? 'chevron-up' : 'chevron-down' }}"
                                    class="size-4 text-primary-500" />
                            @else
                                <flux:icon name="chevrons-up-down" class="size-4 opacity-40" />
                            @endif
                        </button>
                    </th>
                    <th class="px-4 py-3 text-start">
                        <button wire:click="sort('email')"
                            class="flex items-center gap-1 font-medium text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200">
                            {{ __('Email') }}
                            @if ($sortBy === 'email')
                                <flux:icon name="{{ $sortDir === 'asc' ? 'chevron-up' : 'chevron-down' }}"
                                    class="size-4 text-primary-500" />
                            @else
                                <flux:icon name="chevrons-up-down" class="size-4 opacity-40" />
                            @endif
                        </button>
                    </th>






                    <th class="px-4 py-3 text-start">
                        <button wire:click="sort('is_active')"
                            class="flex items-center gap-1 font-medium text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200">
                            {{ __('Status') }}
                        </button>
                    </th>

                    <th class="px-4 py-3 text-start font-medium text-zinc-500 dark:text-zinc-400">{{ __('Roles') }}
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
                        </button>
                    </th>
                    <th class="px-4 py-3 text-end font-medium text-zinc-500 dark:text-zinc-400">{{ __('Actions') }}
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                @forelse ($row as $user)
                    <tr wire:key="user-{{ $user->id }}" class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">

                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @php
                                    $colors = [
                                        'A' => 'bg-blue-500',
                                        'B' => 'bg-green-500',
                                        'C' => 'bg-purple-500',
                                        'D' => 'bg-orange-500',
                                        'E' => 'bg-pink-500',
                                        'F' => 'bg-teal-500',
                                        'G' => 'bg-red-500',
                                        'H' => 'bg-indigo-500',
                                        'I' => 'bg-yellow-500',
                                        'J' => 'bg-cyan-500',
                                        'K' => 'bg-lime-500',
                                        'L' => 'bg-emerald-500',
                                        'M' => 'bg-violet-500',
                                        'N' => 'bg-rose-500',
                                        'O' => 'bg-sky-500',
                                        'P' => 'bg-amber-500',
                                        'Q' => 'bg-fuchsia-500',
                                        'R' => 'bg-blue-600',
                                        'S' => 'bg-green-600',
                                        'T' => 'bg-purple-600',
                                        'U' => 'bg-orange-600',
                                        'V' => 'bg-pink-600',
                                        'W' => 'bg-teal-600',
                                        'X' => 'bg-red-600',
                                        'Y' => 'bg-indigo-600',
                                        'Z' => 'bg-yellow-600',
                                    ];
                                    $letter = strtoupper(substr($user->name, 0, 1));
                                    $avatarColor = $colors[$letter] ?? 'bg-zinc-500';
                                @endphp

                                @if ($user->avatar)
                                    <img src="{{ Storage::disk('public')->url($user->avatar) }}"
                                        alt="{{ $user->name }}"
                                        class="size-9 rounded-full object-cover shadow-sm shrink-0" />
                                @else
                                    <div
                                        class="size-9 rounded-full {{ $avatarColor }} flex items-center justify-center text-xs font-bold text-white shadow-sm shrink-0">
                                        {{ $letter }}
                                    </div>
                                @endif

                                <div class="flex flex-col">
                                    <span
                                        class="font-medium text-zinc-800 dark:text-zinc-100">{{ $user->name }}</span>

                                    @if ($user->mobile)
                                        <div class="text-xs text-gray-400">
                                            {{ $user->mobile }}
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </td>

                        <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">
                            {{ $user->email }}
                        </td>





                        <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">
                            @if ($user->is_active)
                                <flux:badge color="green" size="sm" icon="check-circle">{{ __('Active') }}
                                </flux:badge>
                            @else
                                <flux:badge color="red" size="sm" icon="x-circle">{{ __('Inactive') }}
                                </flux:badge>
                            @endif
                        </td>

                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1">
                                @forelse ($user->roles as $role)
                                    <flux:badge color="blue" size="sm">{{ $role->name }}</flux:badge>
                                @empty
                                    <flux:badge color="zinc" size="sm">{{ __('No Roles') }}</flux:badge>
                                @endforelse
                            </div>
                        </td>

                        <td class="px-4 py-3 text-zinc-400 text-xs">
                            <div class="flex flex-col">
                                <span class="font-medium text-zinc-800 dark:text-zinc-100">
                                    {{ $user->created_at->diffForHumans() }}
                                </span>
                                <span class="text-zinc-400 text-[11px]">
                                    {{ $user->created_at->format('d M, Y - h:i A') }}
                                </span>
                            </div>
                        </td>

                        <td class="px-4 py-3 text-zinc-400 text-xs">
                            <div class="flex flex-col">
                                <span class="font-medium text-zinc-800 dark:text-zinc-100">
                                    {{ $user->updated_at->diffForHumans() }}
                                </span>
                                <span class="text-zinc-400 text-[11px]">
                                    {{ $user->updated_at->format('d M, Y - h:i A') }}
                                </span>
                            </div>
                        </td>

                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                @php
                                    $auth = auth('admin')->user();
                                    $canEdit =
                                        $auth->can($permissionPrefix . '.edit') &&
                                        ($auth->hasRole('super-admin') || !$user->hasRole('super-admin'));
                                    $canDelete =
                                        $auth->can($permissionPrefix . '.delete') &&
                                        !$user->hasRole('super-admin') &&
                                        $auth->id !== $user->id;
                                @endphp

                                @if ($canEdit)
                                 <flux:tooltip content="Edit">
                                    <flux:button href="{{ route('admin.' . $route . '.edit', $user) }}" wire:navigate
                                        size="sm" variant="ghost" icon="pencil" />
                                 </flux:tooltip>
                                @endif

                                @if ($canDelete)
                                <flux:tooltip content="Delete">
                                    <flux:button wire:click="confirmDelete({{ $user->id }})" size="sm"
                                        variant="ghost" icon="trash" class="text-red-500" />
                                </flux:tooltip>
                                @endif

                                @if ($auth->id === $user->id)
                                    <span class="badge badge-light-info fw-bold px-3 py-2">{{ __('You') }}</span>
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
</div>
