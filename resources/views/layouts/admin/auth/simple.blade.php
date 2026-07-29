<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.admin.head')
</head>

@php
    $name = app('settings')['site_title_' . app()->getLocale()] ?? '';
    $logoPath = data_get(app('settings'), 'site_admin_logo');
    if ($logoPath && Storage::disk('public')->exists($logoPath)) {
        $logoUrl = Storage::url($logoPath);
    } else {
        $logoUrl = asset(config('project.app.logo'));
    }
@endphp

<body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
    <div class="bg-background flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
        <div class="flex w-full max-w-sm flex-col gap-2">
            <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center gap-2 font-medium" wire:navigate>
                <span class="flex h-16 w-auto mb-1 items-center justify-center rounded-md">
                    @if ($logoUrl)
                        <img src="{{ $logoUrl }}" alt="{{ $name }}" class="h-20 object-contain" />
                    @else
                        <x-admin.app-logo-icon class="size-10 text-zinc-800 dark:text-white" />
                    @endif
                </span>
                <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
            </a>
            <div class="flex flex-col gap-6">
                {{ $slot }}
            </div>
        </div>
    </div>

    @persist('toast')
        <flux:toast.group>
            <flux:toast />
        </flux:toast.group>
    @endpersist

    @fluxScripts
</body>

</html>
