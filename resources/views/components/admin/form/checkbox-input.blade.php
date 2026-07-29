@props(['label', 'model', 'placeholder'])





<flux:field variant="inline">
    <flux:label>{{ $label }}</flux:label>
    <div class="flex items-center gap-3">
        <flux:switch wire:model="{{ $model }}" />
        <span class="text-sm text-gray-700">{{ $placeholder }}</span>
    </div>
    <flux:error name="{{ $model }}" />
</flux:field>
