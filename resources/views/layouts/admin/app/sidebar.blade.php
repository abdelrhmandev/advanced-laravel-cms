<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.admin.head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/icon-picker/icon-picker.min.css') }}">
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky collapsible="mobile"
        class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.header>
            <x-admin.app-logo :sidebar="true" href="{{ route('admin.dashboard') }}" wire:navigate />
            <flux:sidebar.collapse class="lg:hidden" />
        </flux:sidebar.header>

        <flux:sidebar.nav>
            <flux:sidebar.group :heading="__('Administration')" class="grid">


                @foreach (config('backendsidebar_menu') as $item)
                    @include('livewire.admin.partials.sidebar.menu-item', ['item' => $item])
                @endforeach

            </flux:sidebar.group>
        </flux:sidebar.nav>

        <flux:spacer />



        <x-admin.desktop-user-menu class="hidden lg:block" :name="auth('admin')->user()->name" />
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth('admin')->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <flux:avatar :name="auth('admin')->user()->name"
                                :initials="auth('admin')->user()->initials()" />

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <flux:heading class="truncate">{{ auth('admin')->user()->name }}</flux:heading>
                                <flux:text class="truncate">{{ auth('admin')->user()->email }}</flux:text>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('admin.profile')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('admin.logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                        class="w-full cursor-pointer" data-test="logout-button">
                        {{ __('Log out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    <div class="space-y-6 ml-2 px-6 mt-5">
        <livewire:admin.breadcrumbs />
    </div>
    {{ $slot }}



    @persist('toast')
        <flux:toast.group>
            <flux:toast />
        </flux:toast.group>
    @endpersist

    @fluxScripts
    <script src="{{ asset('assets/icon-picker/icon-picker.min.js') }}"></script>
    <script>
        const iconPickerInstances = new Map();

        function LoadPicker(trans, context = document) {
            const iconPickers = context.querySelectorAll('[data-icon-picker]');
            iconPickers.forEach((iconInput) => {
                if (typeof IconPicker === 'undefined') {
                    console.error('IconPicker not loaded');
                    return;
                }

                const id = iconInput.id;

                if (iconPickerInstances.has(id)) {
                    return;
                }

                const picker = new IconPicker(iconInput, {
                    theme: 'bootstrap-5',
                    iconSource: [{
                            key: 'fa6-solid',
                            prefix: 'fa-solid fa-',
                            url: '/assets/icon-picker/icons/fa6-solid.json',
                            iconFormat: 'i',
                        },
                        {
                            key: 'fa6-regular',
                            prefix: 'fa-regular fa-',
                            url: '/assets/icon-picker/icons/fa6-regular.json',
                            iconFormat: 'i',
                        },
                        {
                            key: 'fa6-brands',
                            prefix: 'fa-brands fa-',
                            url: '/assets/icon-picker/icons/fa6-brands.json',
                            iconFormat: 'i',
                        },
                    ],
                    i18n: {
                        'input:placeholder': 'Search icon…',
                        'text:title': 'Select icon',
                        'text:empty': 'No results found…',
                        'btn:save': 'Save'
                    },
                    closeOnSelect: true
                });

                picker.on('select', (icon) => {
                    iconInput.value = icon.name;
                    iconInput.dispatchEvent(new Event('input'));
                    iconInput.dispatchEvent(new Event('change'));
                });

                iconPickerInstances.set(id, picker);
            });
        }

        function CleanupPickers() {
            iconPickerInstances.forEach((picker, id) => {
                if (!document.getElementById(id)) {
                    try {
                        picker.destroy();
                    } catch (e) {}
                    iconPickerInstances.delete(id);
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => LoadPicker());
        document.addEventListener('livewire:navigated', () => setTimeout(() => LoadPicker(), 150));

        document.addEventListener('livewire:init', () => {
            Livewire.hook('morph.updated', ({
                el
            }) => {
                setTimeout(() => {
                    LoadPicker(null, el);
                    CleanupPickers();
                }, 50);
            });
        });
    </script>


</body>

</html>
