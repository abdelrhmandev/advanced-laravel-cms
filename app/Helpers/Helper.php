<?php
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
if (!function_exists('get_setting_asset')) {
    /**
     * @param string $key The setting key (e.g., 'site_logo')
     * @param string|null $fallback Path to a default asset or a config key
     */
    function get_setting_asset($key, $fallback = null)
    {
        $path = app('settings')[$key] ?? '';

        if (!empty($path)) {
            // 1. Check if it's a static theme asset or an external URL
            if (str_starts_with($path, 'assets') || str_starts_with($path, 'http')) {
                return asset($path);
            }

            // 2. Otherwise, treat as a file stored in the 'public' disk
            // This generates the correct /storage/... URL automatically
            return Storage::url($path);
        }

        // 3. Handle the fallback logic
        if ($fallback) {
            // If the fallback is a config key, resolve it; otherwise use as path
            $resolvedFallback = config($fallback) ?? $fallback;
            return asset($resolvedFallback);
        }

        // Ultimate safety fallback (Metronic default)
        return asset('assets/backend/media/logos/default.png');
    }
}
if (!function_exists('settings')) {
    function settings(string $key, $default = null)
    {
        $settings = App::make('settings');

        if (!is_array($settings)) {
            return $default;
        }

        return $settings[$key] ?? $default;
    }
}
