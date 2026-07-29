@props(['ogImage', 'existingOgImage', 'twitterImage', 'existingTwitterImage'])

<div class="space-y-3">
    <flux:heading size="sm" class="mb-2">
        {{ __('Social & Advanced SEO') }}
    </flux:heading>



    <div>
        <flux:heading size="sm" class="mb-4">{{ __('OG Image') }}</flux:heading>
        <x-admin.form.image-field model="ogImage" :value="$ogImage" :existingValue="$existingOgImage" hint="PNG, JPG, WEBP (Max 2MB)"
            shape="rounded-full" />
    </div>




    <div>
        <flux:heading size="sm" class="mb-4">{{ __('Twitter Image') }}</flux:heading>
        <x-admin.form.image-field model="twitterImage" :value="$twitterImage" :existingValue="$existingTwitterImage"
            hint="PNG, JPG, WEBP (Max 2MB)" shape="rounded-full" />
    </div>




    <x-admin.form.text-input label="{{ __('Canonical URL') }}" model="canonicalUrl"
        placeholder="https://example.com/page" class="!py-2" />

    <div class="flex items-center gap-4">

        <x-admin.form.checkbox-input label="{{ __('Index') }}" model="noIndex" placeholder="{{ __('No Index') }}" />
        <x-admin.form.checkbox-input label="{{ __('Follow') }}" model="noFollow" placeholder="{{ __('No Follow') }}" />


    </div>
</div>
