@php
    $admin = auth('admin')->user();
@endphp

<flux:dropdown position="bottom" align="start">


    <flux:sidebar.profile
        :name="$admin->name"
        :initials="$admin->initials()"
        icon:leading="none"
        icon:trailing="chevrons-up-down"
        data-test="sidebar-menu-button"
    >
        <x-slot:avatar>
            <img
                src="{{ $admin->avatar
                    ? Storage::disk('public')->url($admin->avatar)
                    : asset('images/default-avatar.png') }}"
                class="w-8 h-8 rounded-full object-cover"
            >
        </x-slot:avatar>
    </flux:sidebar.profile>


    <flux:menu>

        {{-- User Info --}}
        <div class="flex items-center gap-2 px-2 py-2 text-sm">
            <flux:avatar
                :name="$admin->name"
                :initials="$admin->initials()"
                :src="$admin->avatar
                    ? Storage::disk('public')->url($admin->avatar)
                    : null"
            />

            <div class="grid leading-tight">
                <flux:heading class="truncate">{{ $admin->name }}</flux:heading>
                <flux:text class="truncate">{{ $admin->email }}</flux:text>
            </div>
        </div>

        <flux:menu.separator />


        <flux:menu.item icon="shield-check">
            {{ $admin->formatted_role_names ?? 'N/A' }}
        </flux:menu.item>

        <flux:menu.separator />


        <flux:menu.radio.group>
            <flux:menu.item :href="route('admin.profile')" icon="cog" wire:navigate>
                {{ __('Settings') }}
            </flux:menu.item>

            <form method="POST" action="{{ route('admin.logout') }}" class="w-full">
                @csrf
                <flux:menu.item as="button" type="submit"
                    icon="arrow-right-start-on-rectangle"
                    class="w-full cursor-pointer"
                    data-test="logout-button">
                    {{ __('Log out') }}
                </flux:menu.item>
            </form>
        </flux:menu.radio.group>

    </flux:menu>

</flux:dropdown>
