<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

@php
    $name = app('settings')['site_title_' . app()->getLocale()] ?? '';
    $logoPath = data_get(app('settings'), 'site_admin_logo');

    if ($logoPath && Storage::disk('public')->exists($logoPath)) {
        $logoUrl = Storage::url($logoPath);
    } else {
        $logoUrl = asset(config('project.app.logo'));
    }

    $logoExtension = $logoUrl ? pathinfo($logoUrl, PATHINFO_EXTENSION) : null;
    $logoMimeType = match ($logoExtension) {
        'svg' => 'image/svg+xml',
        'png' => 'image/png',
        'jpg', 'jpeg' => 'image/jpeg',
        'webp' => 'image/webp',
        'ico' => 'image/x-icon',
        default => 'image/png',
    };

@endphp

<title>
    {{ filled($title ?? null) ? $title . ' - ' . $name : config('app.name', 'Laravel') }}
</title>


@if ($logoUrl)
    <link rel="icon" href="{{ $logoUrl }}" type="{{ $logoMimeType }}" sizes="any">
    <link rel="apple-touch-icon" href="{{ $logoUrl }}">
@endif

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
