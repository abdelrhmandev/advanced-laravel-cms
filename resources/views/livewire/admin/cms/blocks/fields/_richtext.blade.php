@if (!$field->isTranslatable())
    @php
        $dir = 'ltr';
        $editorId = "quill-{$pivotId}-{$row}-{$field->id}";
    @endphp
    <div class="space-y-1">
        <div wire:ignore x-data="quillField({
            editorId: '{{ $editorId }}',
            wireModel: 'formValues.{{ $pivotId }}.{{ $row }}.{{ $field->id }}',
            dir: '{{ $dir }}'
        })"
            class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700">
            <div id="{{ $editorId }}" style="min-height: 120px;" dir="{{ $dir }}"></div>
        </div>
    </div>
@else
    <div
        class="grid grid-cols-1 md:grid-cols-{{ count(\Mcamara\LaravelLocalization\Facades\LaravelLocalization::getSupportedLocales()) }} gap-4">
        @foreach (\Mcamara\LaravelLocalization\Facades\LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
            @php
                $dir = in_array($properties['script'] ?? '', ['Arab']) ? 'rtl' : 'ltr';
                $editorId = "quill-{$pivotId}-{$row}-{$field->id}-{$localeCode}";
            @endphp
            <div class="space-y-1">
                <flux:label>
                    {{ $field->label }} ({{ $properties['native'] }})
                    @if ($field->required)
                        <span class="text-red-400">*</span>
                    @endif
                </flux:label>
                <div wire:ignore x-data="quillField({
                    editorId: '{{ $editorId }}',
                    wireModel: 'formValues.{{ $pivotId }}.{{ $row }}.{{ $field->id }}.{{ $localeCode }}',
                    dir: '{{ $dir }}'
                })"
                    class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <div id="{{ $editorId }}" style="min-height: 120px;" dir="{{ $dir }}"></div>
                </div>
                <flux:error
                    name="formValues.{{ $pivotId }}.{{ $row }}.{{ $field->id }}.{{ $localeCode }}" />
            </div>
        @endforeach
    </div>
@endif
