@props([
    'id',
    'action' => 'confirmDelete',
    'permission' => null,
])

@if(!$permission || auth('admin')->user()?->can($permission . '.delete'))

        <flux:button
            wire:click="{{ $action }}({{ $id }})"
            variant="danger"
            icon="trash"
            {{ $attributes }}
        >
            {{ $label ?? __('Delete') }}
        </flux:button>

@endif
