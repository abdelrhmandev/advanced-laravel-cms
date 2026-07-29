<flux:field wire:key="field-{{ $pivotId }}-{{ $row }}-{{ $field->id }}">

    @if (!$field->isTranslatable())
        <flux:label>
            {{ $field->label }}
            @if ($field->required)
                <span class="text-red-400">*</span>
            @endif
        </flux:label>
    @endif

    @includeIf('livewire.admin.cms.blocks.fields._' . $field->type, [
        'field' => $field,
        'pivotId' => $pivotId,
        'row' => $row,
    ])

</flux:field>
