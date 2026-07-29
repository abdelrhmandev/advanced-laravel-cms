@props(['label', 'model', 'placeholder' => '', 'type' => 'text'])

<flux:field>
    <flux:label>{{ $label }}</flux:label>
    <flux:input type="{{ $type }}" wire:model="{{ $model }}" placeholder="{{ $placeholder }}" />
    <flux:error name="{{ $model }}" />
</flux:field>
