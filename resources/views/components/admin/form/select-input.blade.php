@props(['label', 'model', 'options' => [], 'placeholder' => null])





<flux:field>
    <flux:label>{{ $label }}</flux:label>
    <flux:select wire:model.live="{{ $model }}" placeholder="{{ $model }}" clearable>
        <flux:select.option value="0">Select Option</flux:select.option>
        @foreach ($options as $value => $label)
            <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
        @endforeach
    </flux:select>

    <flux:error name="{{ $model }}" />
</flux:field>
