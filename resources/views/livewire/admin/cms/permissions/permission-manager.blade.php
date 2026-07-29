<div class="space-y-6">
    {{-- Header Section --}}
    <flux:card class="flex items-center justify-between p-6">
        <div class="flex items-center gap-4">
            <div class="flex items-center justify-center w-14 h-14 rounded-full bg-zinc-100 dark:bg-zinc-800">
                <flux:icon name="shield-check" class="w-7 h-7 text-zinc-600 dark:text-zinc-300" />
            </div>
            <div>
                <flux:heading size="xl" class="font-bold">Permission Manager</flux:heading>
                <flux:subheading>Code-First Module Synchronization & Access Control</flux:subheading>
            </div>
        </div>

        <flux:button
            wire:click="syncAll"
            wire:loading.attr="disabled"
            variant="primary"
            icon="arrow-path"
        >
            <span wire:loading.remove>Sync All Permissions</span>
            <span wire:loading>Syncing...</span>
        </flux:button>
    </flux:card>

    {{-- Logic Preparation --}}
    @php
        $modules = collect(array_keys($componentsList))
            ->merge(array_keys($generatedPermissions))
            ->unique()
            ->sort();
    @endphp

    {{-- Modules Header --}}
    <div class="flex items-center gap-3">
        <flux:heading size="lg" class="font-bold">Discovered Modules ({{ $modules->count() }})</flux:heading>
        <flux:badge color="blue" variant="subtle">Code-First Scanner Active</flux:badge>
    </div>

    {{-- Modules Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse ($modules as $module)
            <flux:card class="flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <flux:icon name="folder-open" class="w-5 h-5 text-zinc-400" />
                        <flux:heading size="md" class="font-bold">{{ $module }}</flux:heading>
                    </div>
                    {{-- Status partial --}}
                    @include('livewire.admin.partials.permissions.sync-status', ['module' => $module])
                </div>

                {{-- Badges partial --}}
                <div class="flex-grow">
                    @include('livewire.admin.partials.permissions.badges', ['module' => $module])
                </div>
            </flux:card>
        @empty
            <div class="col-span-full">
                <flux:callout variant="warning" icon="exclamation-triangle">
                    <flux:callout.heading>No permittedActions found!</flux:callout.heading>
                    <flux:callout.text>
                        Ensure your Livewire components in <code>App\Livewire\Admin\CMS</code> have a <code>public array $permittedActions</code> defined.
                    </flux:callout.text>
                </flux:callout>
            </div>
        @endforelse
    </div>
</div>
