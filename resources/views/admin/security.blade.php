<x-layouts::admin.app.sidebar :title="__('Security Settings')">
    <flux:main>
        <div class="relative mb-6 w-full">
            <flux:heading size="xl" level="1">{{ __('Settings') }}</flux:heading>
            <flux:subheading size="lg" class="mb-6">{{ __('Manage your profile and account settings') }}</flux:subheading>
            <flux:separator variant="subtle" />
        </div>
        <div class="flex items-start max-md:flex-col">
            <div class="me-10 w-full pb-4 md:w-[220px]">
                <flux:navlist aria-label="{{ __('Settings') }}">
                    <flux:navlist.item :href="route('admin.profile')" wire:navigate>
                        {{ __('Profile') }}
                    </flux:navlist.item>
                    <flux:navlist.item :href="route('admin.security')" wire:navigate>
                        {{ __('Security') }}
                    </flux:navlist.item>
                    <flux:navlist.item :href="route('admin.appearance')" wire:navigate>
                        {{ __('Appearance') }}
                    </flux:navlist.item>
                </flux:navlist>
            </div>
            <flux:separator class="md:hidden" />
            <div class="flex-1 self-stretch max-md:pt-6">
                <div class="w-full max-w-lg">
                 <livewire:admin.profile.update-password-form />
                </div>
            </div>
        </div>
    </flux:main>
</x-layouts::admin.app.sidebar>
