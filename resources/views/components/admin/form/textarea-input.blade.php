@props(['label', 'model', 'placeholder' => '', 'type' => 'text', 'properties'])
<flux:label>
    <flux:label>{{ $label }}</flux:label>
    @php
        $dir = in_array($properties['script'] ?? '', ['Arab']) ? 'rtl' : 'ltr';
        $editorId = 'quill-' . str($model)->slug();
    @endphp
</flux:label>

<flux:textarea wire:model="{{ $model }}"
    id="text-input-{{ $model }}"
    placeholder="{{ $placeholder }}"
    dir="{{ in_array($properties['script'] ?? '', ['Arab']) ? 'rtl' : 'ltr' }}" />
    <flux:error name="{{ $model }}" />
