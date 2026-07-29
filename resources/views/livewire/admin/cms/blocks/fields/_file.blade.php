@if (!$field->isTranslatable())
    <div class="grid grid-cols-1 gap-2">

        <div class="space-y-2" wire:key="file-{{ $pivotId }}-{{ $row }}-{{ $field->id }}"
            x-data="{
                error: null,
                checkFile(event) {
                    const file = event.target.files[0];
                    this.error = null;

                    if (!file) return;

                    const allowedTypes = [
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                    ];

                    const maxSize = 2 * 1024 * 1024;

                    if (!allowedTypes.includes(file.type)) {
                        this.error = 'Only PDF, DOC and DOCX formats are allowed.';
                    }

                    if (file.size > maxSize) {
                        this.error = 'File size must not exceed 2MB.';
                    }
                }
            }">

            @php
                $currentFile = data_get($formValues, "{$pivotId}.{$row}.{$field->id}");
                $isExistingFile = is_string($currentFile) && $currentFile !== '';
            @endphp

            @if ($isExistingFile)
                <div class="flex items-center gap-2 text-xs">
                    <flux:icon name="document" class="size-4 text-gray-400" />
                    <a href="{{ Storage::url($currentFile) }}" target="_blank" class="text-blue-600 hover:underline">
                        {{ basename($currentFile) }}
                    </a>
                    <span class="text-gray-400">(uploaded)</span>



                    <button type="button"
                        wire:click="removeFile({{ $pivotId }}, {{ $row }}, {{ $field->id }})"
                        wire:confirm="Remove this file?" class="text-red-500 hover:underline">
                        Remove
                    </button>


                </div>
            @endif

            <flux:input type="file" id="file-input-{{ $pivotId }}_{{ $row }}_{{ $field->id }}"
                wire:model="formValues.{{ $pivotId }}.{{ $row }}.{{ $field->id }}"
                accept=".pdf,.doc,.docx" x-on:change="checkFile($event)" />

            <small class="text-xs text-gray-500 block">
                @if ($isExistingFile)
                    Choose a new file to replace the one above • PDF, DOC, DOCX • Max 2MB
                @else
                    Only: PDF, DOC, DOCX • Max size: 2MB
                @endif
            </small>

            <div class="text-xs text-red-500 space-y-1">
                <div x-show="error" x-text="error"></div>
                <flux:error name="formValues.{{ $pivotId }}.{{ $row }}.{{ $field->id }}" />
            </div>

        </div>

    </div>
@else
    <div
        class="grid grid-cols-1 md:grid-cols-{{ count(\Mcamara\LaravelLocalization\Facades\LaravelLocalization::getSupportedLocales()) }} gap-2">

        @foreach (\Mcamara\LaravelLocalization\Facades\LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
            @php
                $currentFile = data_get($formValues, "{$pivotId}.{$row}.{$field->id}.{$localeCode}");
                $isTemp = $currentFile instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
                $isExistingFile = is_string($currentFile) && $currentFile !== '';
                $fileName = $isTemp
                    ? $currentFile->getClientOriginalName()
                    : ($isExistingFile
                        ? basename($currentFile)
                        : null);
            @endphp

            <div class="space-y-2"
                wire:key="file-{{ $pivotId }}-{{ $row }}-{{ $field->id }}-{{ $localeCode }}"
                x-data="{
                    error: null,
                    checkFile(event) {
                        const file = event.target.files[0];
                        this.error = null;

                        if (!file) return;

                        const allowedTypes = [
                            'application/pdf',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                        ];

                        const maxSize = 2 * 1024 * 1024;

                        if (!allowedTypes.includes(file.type)) {
                            this.error = 'Only PDF, DOC and DOCX formats are allowed.';
                        }

                        if (file.size > maxSize) {
                            this.error = 'File size must not exceed 2MB.';
                        }
                    }
                }">

                {{-- LABEL --}}
                <flux:label>
                    {{ $field->label }} ({{ $properties['native'] }})
                    @if ($field->required)
                        <span class="text-red-400">*</span>
                    @endif
                </flux:label>

                @if ($isExistingFile)
                    <div class="flex items-center gap-2 text-xs">
                        <flux:icon name="document" class="size-4 text-gray-400" />
                        <a href="{{ Storage::url($currentFile) }}" target="_blank"
                            class="text-blue-600 hover:underline">
                            {{ $fileName }}
                        </a>
                        <span class="text-gray-400">(uploaded)</span>
                        <button type="button"
                            wire:click="removeFile({{ $pivotId }}, {{ $row }}, {{ $field->id }}, '{{ $localeCode }}')"
                            wire:confirm="Remove this file?" class="text-red-500 hover:underline">
                            Remove
                        </button>
                    </div>
                @elseif ($isTemp)
                    <div class="text-xs text-gray-500">
                        Selected: {{ $fileName }}
                    </div>
                @endif

                <flux:input type="file"
                    id="file-input-{{ $pivotId }}_{{ $row }}_{{ $field->id }}_{{ $localeCode }}"
                    wire:model="formValues.{{ $pivotId }}.{{ $row }}.{{ $field->id }}.{{ $localeCode }}"
                    accept=".pdf,.doc,.docx" x-on:change="checkFile($event)" />

                <small class="text-xs text-gray-500 block">
                    @if ($isExistingFile)
                        Choose a new file to replace the one above • PDF, DOC, DOCX • Max 2MB
                    @else
                        Only: PDF, DOC, DOCX • Max size: 2MB
                    @endif
                </small>

                <div class="text-xs text-red-500 space-y-1">
                    <div x-show="error" x-text="error"></div>
                    <flux:error
                        name="formValues.{{ $pivotId }}.{{ $row }}.{{ $field->id }}.{{ $localeCode }}" />
                </div>

            </div>
        @endforeach
    </div>

@endif
