@props([
    'module',
    'deleteAction' => 'delete',
    'name' => 'confirm-delete',
])

<flux:modal name="{{ $name }}" class="min-w-[22rem]">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">
                {{ __('Delete ' . ucfirst(\Str::singular($module))) }}
            </flux:heading>
            <flux:subheading>
                {{ __('Are you sure you want to delete this ' . \Str::singular($module) . ' ? ') }}
            </flux:subheading>
        </div>
        <div class="flex gap-2 justify-end">
            <flux:modal.close>
                <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
            </flux:modal.close>
            <flux:button wire:click="{{ $deleteAction }}" variant="danger">
                {{ __('Delete') }}
            </flux:button>
        </div>
    </div>
</flux:modal>
