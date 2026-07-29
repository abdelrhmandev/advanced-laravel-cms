@php
    $min = $field->settings['min'] ?? null;
    $max = $field->settings['max'] ?? null;
    $step = $field->settings['step'] ?? 1;

    $decimals = 0;
    if (str_contains((string) $step, '.')) {
        $decimals = strlen(explode('.', $step)[1]);
    }

    $regex = $decimals > 0 ? "^\\d+(\\.\\d{1,{$decimals}})?$" : "^\\d+$";
@endphp
<flux:input type="number" wire:model.lazy="formValues.{{ $pivotId }}.{{ $row }}.{{ $field->id }}"
    placeholder="{{ $field->settings['placeholder'] ?? '' }}" min="{{ $min }}" max="{{ $max }}"
    step="{{ $step }}" pattern="{{ $regex }}" />
<small class="text-xs text-gray-500 block">

    @php
        $rules = [];
    @endphp

    @if ($min !== null)
        @php $rules[] = "Min: $min"; @endphp
    @endif

    @if ($max !== null)
        @php $rules[] = "Max: $max"; @endphp
    @endif

    @if ($step !== null)
        @php $rules[] = "Step: $step"; @endphp
    @endif


    @if ($decimals > 0)
        @php $rules[] = "Max $decimals decimal places"; @endphp
    @endif

    {{ implode(' • ', $rules) }}

</small>
<div class="text-xs text-red-500 space-y-1">
    <flux:error name="formValues.{{ $pivotId }}.{{ $row }}.{{ $field->id }}" />
</div>
