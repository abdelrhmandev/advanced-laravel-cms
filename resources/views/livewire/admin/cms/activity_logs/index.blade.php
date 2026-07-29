{{-- resources/views/livewire/admin/cms/logs/index.blade.php --}}
<div class="max-w-[1600px] mx-auto">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <flux:heading size="xl">Activity Log Last {{ (int) (app('settings')['activity_log_retention_days'] ?? 365) }} Days</flux:heading>
            <flux:subheading>Monitor and analyze all system activities</flux:subheading>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-5 items-start">
        {{-- Sidebar Filters --}}
        <div class="lg:col-span-1">
            <div
                class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5 lg:sticky lg:top-6">
                <div class="flex items-center justify-between mb-5">
                    <flux:heading size="lg">Filters</flux:heading>
                    <button type="button" wire:click="$set('search', ''); $set('events', []); $set('user', '')"
                        class="text-xs font-medium text-zinc-400 hover:text-red-500 transition-colors">
                        Clear
                    </button>
                </div>

                {{-- Search --}}
                <div class="mb-5">
                    <flux:input wire:model.live.debounce.400ms="search" label="Search"
                        placeholder="Search activities..." icon="magnifying-glass" />
                </div>

                {{-- Event Types --}}
                <div class="mb-5">
                    <flux:label class="!text-xs !font-semibold !uppercase !tracking-wide !text-zinc-400">
                        Event Types
                    </flux:label>
                    <div class="mt-3 space-y-2.5">
                        @foreach ([
        'updated' => ['label' => 'Updated', 'dot' => 'bg-blue-500'],
        'created' => ['label' => 'Created', 'dot' => 'bg-emerald-500'],
        'deleted' => ['label' => 'Deleted', 'dot' => 'bg-red-500'],
    ] as $value => $meta)
                            <label class="flex items-center justify-between cursor-pointer group">
                                <span class="flex items-center gap-2.5">
                                    <flux:checkbox wire:model.live="events" value="{{ $value }}" />
                                    <span
                                        class="text-sm text-zinc-600 dark:text-zinc-300 group-hover:text-zinc-900 dark:group-hover:text-white transition-colors">
                                        {{ $meta['label'] }}
                                    </span>
                                </span>
                                <span class="w-2 h-2 rounded-full {{ $meta['dot'] }}"></span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- User / Causer --}}
                <div>
                    <flux:label class="!text-xs !font-semibold !uppercase !tracking-wide !text-zinc-400">
                        User / Causer
                    </flux:label>
                    <div class="relative mt-2">
                        <select wire:model.live="user"
                            class="w-full appearance-none text-sm rounded-lg border border-zinc-200 dark:border-zinc-600
                   bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white
                   px-3 py-2 pr-9
                   hover:border-zinc-300 dark:hover:border-zinc-500
                   focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500
                   transition-colors cursor-pointer">
                            <option value="">All users</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                        <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 pointer-events-none"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>


            </div>
        </div>

        {{-- Table --}}
        <div class="lg:col-span-3">
            <div
                class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden relative">

                <div wire:loading.flex wire:target="search, events, user"
                    class="absolute inset-0 bg-white/50 dark:bg-zinc-800/50 z-10 items-center justify-center">
                    <flux:icon.loading class="size-6 text-indigo-500" />
                </div>

                <div class="px-5 py-4 border-b border-zinc-200 dark:border-zinc-700">
                    <flux:heading size="lg">Activity Log </flux:heading>
                    <flux:subheading>{{ number_format($logs->total()) }} activities found</flux:subheading>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-[11px] font-semibold text-zinc-400 uppercase tracking-wide">
                                <th class="px-5 py-3">Event</th>
                                <th class="px-5 py-3">Description</th>
                                <th class="px-5 py-3">Subject</th>
                                <th class="px-5 py-3">User</th>
                                <th class="px-5 py-3">Date</th>
                                <th class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                            @forelse ($logs as $log)
                                @php
                                    $badgeColor = match ($log->event) {
                                        'created' => 'green',
                                        'updated' => 'blue',
                                        'deleted' => 'red',
                                        default => 'zinc',
                                    };
                                    $name = $log->causer->name ?? 'System';
                                @endphp
                                <tr wire:key="log-{{ $log->id }}"
                                    class="hover:bg-zinc-50 dark:hover:bg-zinc-700/30 transition-colors">
                                    <td class="px-5 py-3.5">

                                        <flux:badge color="{{ $badgeColor }}" size="sm">
                                            {{ $log->event }}
                                        </flux:badge>

                                    </td>
                                    <td class="px-5 py-3.5 text-zinc-700 dark:text-zinc-200">{{ $log->description }}
                                    </td>
                                    <td class="px-5 py-3.5 font-mono text-xs text-zinc-500">
                                        {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="w-[26px] h-[26px] rounded-full bg-indigo-600 text-white text-[11px] font-medium flex items-center justify-center shrink-0">
                                                {{ strtoupper(substr($name, 0, 1)) }}
                                            </div>
                                            <span
                                                class="text-zinc-700 dark:text-zinc-300 truncate">{{ $name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5 text-zinc-500 whitespace-nowrap">
                                        <div>{{ $log->created_at->format('d/m/Y') }}</div>
                                        <div class="text-[11px] text-zinc-400">{{ $log->created_at->format('H:i:s') }}
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5 text-right">

                                        <flux:button wire:click="show({{ $log->id }})" variant="ghost"
                                            size="sm" icon="eye">
                                            View
                                        </flux:button>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-16 text-center">
                                        <flux:icon.inbox class="size-8 mx-auto text-zinc-300 mb-2" />
                                        <p class="text-zinc-500 text-sm">No activity found for the selected filters.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-5 py-4 border-t border-zinc-200 dark:border-zinc-700">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>


    <flux:modal wire:model="showModal" class="!max-w-2xl !w-full">
        @if ($selectedLog)
            <div class="space-y-5">
                <div>
                    <flux:heading size="lg">Activity Details</flux:heading>
                    <flux:subheading>Log #{{ $selectedLog->id }}</flux:subheading>
                </div>

                <flux:separator />

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-zinc-400">Event</span>

                        @php
                            $badgeColor = match ($selectedLog->event) {
                                'created' => 'green',
                                'updated' => 'blue',
                                'deleted' => 'red',
                                default => 'zinc',
                            };
                        @endphp


                        <flux:badge color="{{ $badgeColor }}" size="sm">
                            {{ $selectedLog->event }}
                        </flux:badge>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-zinc-400 shrink-0">Description</span>
                        <span class="text-zinc-900 dark:text-white text-right">{{ $selectedLog->description }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-400">Subject</span>
                        <span class="font-mono text-xs">
                            {{ class_basename($selectedLog->subject_type) }} #{{ $selectedLog->subject_id }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-400">Causer</span>
                        <span>{{ $selectedLog->causer->name ?? 'System' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-400">Date</span>
                        <span>{{ $selectedLog->created_at->format('d/m/Y H:i:s') }}</span>
                    </div>
                </div>

                @if ($selectedLog->properties && $selectedLog->properties->isNotEmpty())
                    <div>
                        <flux:label class="!text-xs !font-semibold !uppercase !tracking-wide !text-zinc-400">
                            Changes
                        </flux:label>
                        <pre
                            class="mt-2 text-xs bg-zinc-50 dark:bg-zinc-900 rounded-xl p-4 overflow-x-auto text-zinc-700 dark:text-zinc-300 max-h-64">{{ json_encode($selectedLog->properties, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                @endif

                <div class="flex justify-end pt-2">
                    <flux:button wire:click="closeModal" variant="ghost">Close</flux:button>
                </div>
            </div>
        @endif
    </flux:modal>
</div>
