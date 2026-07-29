@props(['label', 'model', 'placeholder' => '', 'type' => 'text', 'required' => false])

<flux:field>
    <flux:label>{{ $label }}

     @if ($required)
            <span class="text-red-500 ms-1">*</span>
        @endif

    </flux:label>
    <flux:input type="{{ $type }}" wire:model="{{ $model }}" placeholder="{{ $placeholder }}" />
    <flux:error name="{{ $model }}" />
</flux:field>
