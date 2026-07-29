@props(['label', 'model', 'placeholder' => '', 'type' => 'text', 'properties'])
<flux:label>
    <flux:label>{{ $label }}</flux:label>
    @php
        $dir = in_array($properties['script'] ?? '', ['Arab']) ? 'rtl' : 'ltr';
       $editorId = 'quill-' . str($model)->slug();
    @endphp
</flux:label>
<div wire:ignore x-data="quillField({
    editorId: '{{ $editorId }}',
    wireModel: '{{ $model }}',
    dir: '{{ $dir }}'
})"
    class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700">
    <div id="{{ $editorId }}" style="min-height: 120px;" dir="{{ $dir }}"></div>
</div>
