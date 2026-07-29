@php
    $originalChildren = $item['children'] ?? [];
    $itemHasChildren  = count($originalChildren) > 0;
    $children = collect($originalChildren)
        ->filter(fn($child) => !isset($child['can']) || auth('admin')->user()?->can($child['can']))
        ->values()
        ->all();
    $hasChildren   = count($children) > 0;
    $hasPermission = !isset($item['can']) || auth('admin')->user()?->can($item['can']);
    $shouldRender  = $itemHasChildren ? $hasChildren : $hasPermission;
    $url      = isset($item['url']) ? route($item['url']) : '#';
    $isActive = isset($item['url']) && (request()->routeIs($item['url']) || request()->routeIs($item['url'] . '.*'));
    $isParentActive = false;
    if ($hasChildren) {
        foreach ($children as $child) {
            if (isset($child['url']) && (request()->routeIs($child['url']) || request()->routeIs($child['url'] . '.*'))) {
                $isParentActive = true;
                break;
            }
        }
    }
@endphp

@if ($shouldRender)
    @if (!$hasChildren)
        <flux:sidebar.item
            icon="{{ $item['icon'] }}"
            :href="$url"
            :current="$isActive"
            wire:navigate>
            {{ $item['title'] }}
        </flux:sidebar.item>
    @else
        <flux:navlist>
            <flux:navlist.group
                :expandable="true"
                :expanded="$isParentActive">

                <x-slot:heading>
                    <span @class([
                        'flex items-center gap-2 w-full text-sm',
                        'text-zinc-900 dark:text-white font-medium' => $isParentActive,
                        'text-zinc-500 dark:text-zinc-400'          => !$isParentActive,
                    ])>
                        <flux:icon name="{{ $item['icon'] }}" variant="outline" class="size-4 shrink-0" />
                        {{ $item['title'] }}
                    </span>
                </x-slot:heading>

                @foreach ($children as $child)
                    @include('livewire.admin.partials.sidebar.submenu', ['item' => $child])
                @endforeach
            </flux:navlist.group>
        </flux:navlist>
    @endif
@endif
