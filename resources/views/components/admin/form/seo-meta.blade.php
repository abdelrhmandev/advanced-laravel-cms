@props(['properties', 'localeCode'])

<flux:card>
    <flux:heading size="sm" class="mb-4">
        {{ __('Search Engine Metadata') }}
        @error("seoTranslations.{$localeCode}.meta_title")
            <flux:icon.exclamation-circle class="inline size-3.5 text-red-500 ms-1" />
        @enderror
    </flux:heading>

    <div class="space-y-4">
        <x-admin.form.text-input label="{{ __('Meta Title') }} ({{ $localeCode }})"
            model="seoTranslations.{{ $localeCode }}.meta_title" />

        <x-admin.form.textarea-input label="{{ __('Meta Description') }} ({{ $localeCode }})"
            model="seoTranslations.{{ $localeCode }}.meta_description" :rows="3" />

        <x-admin.form.text-input label="{{ __('Meta Keywords') }} ({{ $localeCode }})"
            model="seoTranslations.{{ $localeCode }}.meta_keywords" />

        <flux:separator text="{{ __('Open Graph') }} ({{ $localeCode }})" />

        <x-admin.form.text-input label="{{ __('OG Title') }} ({{ $localeCode }})" model="seoTranslations.{{ $localeCode }}.og_title" />

        <x-admin.form.textarea-input label="{{ __('OG Description') }} ({{ $localeCode }}) "
            model="seoTranslations.{{ $localeCode }}.og_description" :rows="2" />

        <flux:separator text="{{ __('Twitter Card') }} ({{ $localeCode }})"  />

        <x-admin.form.text-input label="{{ __('Twitter Title') }} ({{ $localeCode }}) "
            model="seoTranslations.{{ $localeCode }}.twitter_title" />

        <x-admin.form.textarea-input label="{{ __('Twitter Description') }} ({{ $localeCode }})"
            model="seoTranslations.{{ $localeCode }}.twitter_description" :rows="2" />
    </div>
</flux:card>
