<input type="color" wire:model="formValues.{{ $pivotId }}.{{ $row }}.{{ $field->id }}" />
<div class="text-xs text-red-500 space-y-1">
    <flux:error name="formValues.{{ $pivotId }}.{{ $row }}.{{ $field->id }}" />
</div>
