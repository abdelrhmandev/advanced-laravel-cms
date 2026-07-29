@if ($field->settings['options'])
    <flux:select wire:model="formValues.{{ $pivotId }}.{{ $row }}.{{ $field->id }}" id="formValues.{{ $pivotId }}.{{ $row }}.{{ $field->id }}"
        name="formValues.{{ $pivotId }}.{{ $row }}.{{ $field->id }}">
        <flux:select.option value="">Choose an option...</flux:select.option>
        @foreach ($field->settings['options'] as $option)
            <flux:select.option value="{{ $option['value'] }}">{{ $option['label'] }}</flux:select.option>
        @endforeach


    </flux:select>
    <flux:error name="formValues.{{ $pivotId }}.{{ $row }}.{{ $field->id }}" />
@endif
