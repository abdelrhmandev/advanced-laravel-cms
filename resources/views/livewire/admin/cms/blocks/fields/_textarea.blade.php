@if (!$field->isTranslatable())
    <flux:textarea
        wire:model="formValues.{{ $pivotId }}.{{ $row }}.{{ $field->id }}"
        placeholder="{{ $field->settings['placeholder'] ?? '' }}" />
    <flux:error name="formValues.{{ $pivotId }}.{{ $row }}.{{ $field->id }}" />
@else
    <div class="grid grid-cols-1 md:grid-cols-{{ count(\Mcamara\LaravelLocalization\Facades\LaravelLocalization::getSupportedLocales()) }} gap-2">
        @foreach (\Mcamara\LaravelLocalization\Facades\LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
            <div class="space-y-1">
                <flux:label>
                    {{ $field->label }} ({{ $properties['native'] }})
                    @if ($field->required)
                        <span class="text-red-400">*</span>
                    @endif
                </flux:label>
                <flux:textarea
                    wire:model="formValues.{{ $pivotId }}.{{ $row }}.{{ $field->id }}.{{ $localeCode }}"
                    id="text-input-{{ $pivotId }}_{{ $row }}_{{ $field->id }}.{{ $localeCode }}"
                    placeholder="{{ $field->settings['placeholder'] ?? '' }} ({{ $properties['native'] }})"
                    dir="{{ in_array($properties['script'] ?? '', ['Arab']) ? 'rtl' : 'ltr' }}" />
                <flux:error name="formValues.{{ $pivotId }}.{{ $row }}.{{ $field->id }}.{{ $localeCode }}" />
            </div>
        @endforeach
    </div>
@endif
