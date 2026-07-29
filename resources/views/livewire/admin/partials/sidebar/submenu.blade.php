{{-- submenu.blade.php --}}
@php
    $children = collect($item['children'] ?? [])
        ->filter(fn($child) => !isset($child['can']) || auth('admin')->user()?->can($child['can']))
        ->values()
        ->all();

    $hasChildren   = count($children) > 0;
    $hasPermission = !isset($item['can']) || auth('admin')->user()?->can($item['can']);
    $isActive      = isset($item['url']) && request()->routeIs($item['url']);

    $isChildActive = false;
    if ($hasChildren) {
        $checkActive = function ($items) use (&$checkActive) {
            foreach ($items as $child) {
                if (isset($child['url']) && request()->routeIs($child['url'])) return true;
                if (!empty($child['children']) && $checkActive($child['children'])) return true;
            }
            return false;
        };
        $isChildActive = $checkActive($children);
    }
@endphp

@if ($hasChildren)
    <flux:navlist.group
        expandable
        :expanded="$isChildActive">

        <x-slot name="heading">
            <span @class([
                'flex items-center gap-2 w-full text-sm',
                'text-zinc-900 dark:text-white font-medium'  => $isChildActive,
                'text-zinc-500 dark:text-zinc-400'           => !$isChildActive,
            ])>
                <flux:icon name="{{ $item['icon'] }}" variant="outline" class="size-4 shrink-0" />
                {{ $item['title'] }}
            </span>
        </x-slot>

        @foreach ($children as $child)
            @include('livewire.admin.partials.sidebar.submenu', ['item' => $child])
        @endforeach
    </flux:navlist.group>

@elseif ($hasPermission && isset($item['url']))
    <flux:sidebar.item
        icon="{{ $item['icon'] ?? 'circle' }}"
        :href="route($item['url'])"
        :current="$isActive"
        wire:navigate>
        {{ $item['title'] }}
    </flux:sidebar.item>
@endif
