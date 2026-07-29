@props([
    'sidebar' => false,
])

@php
    $name = app('settings')['site_title_' . app()->getLocale()] ?? '';
    $logoPath = data_get(app('settings'), 'site_admin_logo');
    if ($logoPath && Storage::disk('public')->exists($logoPath)) {
        $logoUrl = Storage::url($logoPath);
    } else {
        $logoUrl = asset(config('project.app.logo'));
    }
@endphp




@if ($sidebar)
    <flux:sidebar.brand name="{{ $name }}" {{ $attributes }}>
        <x-slot name="logo" class="flex items-center justify-center">
            @if ($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $name }}" class="h-8 object-contain" />
            @else
                <x-admin.app-logo-icon class="size-10 text-zinc-800 dark:text-white" />
            @endif

        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand name="{{ $name }}" {{ $attributes }}>
        <x-slot name="logo"
            class="flex aspect-square size-8 items-center justify-center rounded-md bg-accent-content text-accent-foreground">
            <x-admin.app-logo-icon class="size-5 fill-current text-white dark:text-black" />
        </x-slot>
    </flux:brand>
@endif
