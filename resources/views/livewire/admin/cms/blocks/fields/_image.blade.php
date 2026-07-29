@if (!$field->isTranslatable())

    <div class="grid grid-cols-1 gap-2">

        @php
            $currentImage = data_get($formValues, "{$pivotId}.{$row}.{$field->id}");

            $isTemp = $currentImage instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

            $isPreviewableImage = $isTemp && str_starts_with($currentImage->getMimeType() ?? '', 'image/');
        @endphp

        <div class="space-y-1" wire:key="locale-{{ $pivotId }}-{{ $row }}-{{ $field->id }}"
            x-data="{
                error: null,
                showLightbox: false,
                checkImage(event) {
                    const file = event.target.files[0];
                    this.error = null;
                    if (!file) return;

                    const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
                    const maxSize = 2 * 1024 * 1024;

                    if (!allowedTypes.includes(file.type)) {
                        this.error = 'Only JPG, PNG, WEBP and GIF formats are allowed.';
                        event.target.value = '';
                        return;
                    }

                    if (file.size > maxSize) {
                        this.error = 'File size must not exceed 2MB.';
                        event.target.value = '';
                    }
                }
            }">

            {{-- PREVIEW (shown above the input) --}}
            <div wire:loading.remove
                wire:target="formValues.{{ $pivotId }}.{{ $row }}.{{ $field->id }}">

                <div class="mb-2"
                    wire:key="preview-{{ $pivotId }}-{{ $row }}-{{ $field->id }}-{{ $isTemp ? $currentImage->hashName() : 'static' }}">

                    {{-- VALID IMAGE PREVIEW --}}
                    @if ($isPreviewableImage)
                        <div class="relative inline-block cursor-zoom-in group" x-on:click="showLightbox = true">
                            <img src="{{ $currentImage->temporaryUrl() }}"
                                class="w-16 h-16 object-cover rounded-lg border" />
                            <div
                                class="absolute inset-0 flex items-center justify-center bg-black/0 group-hover:bg-black/30 rounded-lg transition">
                                <flux:icon name="magnifying-glass-plus"
                                    class="size-5 text-white opacity-0 group-hover:opacity-100 transition" />
                            </div>
                        </div>

                        {{-- INVALID TEMP FILE --}}
                    @elseif ($isTemp)
                        <span class="text-xs text-red-500">Invalid file type selected.</span>

                        {{-- STORED IMAGE --}}
                    @elseif ($currentImage)
                        <div class="relative inline-block cursor-zoom-in group" x-on:click="showLightbox = true">
                            <img src="{{ Storage::url($currentImage) }}"
                                class="w-16 h-16 object-cover rounded-lg border" />
                            <div
                                class="absolute inset-0 flex items-center justify-center bg-black/0 group-hover:bg-black/30 rounded-lg transition">
                                <flux:icon name="magnifying-glass-plus"
                                    class="size-5 text-white opacity-0 group-hover:opacity-100 transition" />
                            </div>
                        </div>

                        <div class="flex items-center gap-2 text-xs">
                            <button type="button"
                                wire:click="removeFile({{ $pivotId }}, {{ $row }}, {{ $field->id }})"
                                wire:confirm="Remove this file?" class="text-red-500 hover:underline">
                                Remove
                            </button>

                        </div>
                    @endif

                </div>
            </div>

            {{-- LIGHTBOX --}}
            <div x-show="showLightbox" x-cloak x-on:keydown.escape.window="showLightbox = false"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4"
                x-on:click.self="showLightbox = false" style="display: none;">
                <button type="button" x-on:click="showLightbox = false"
                    class="absolute top-4 right-4 text-white text-2xl leading-none">&times;</button>

                @if ($isPreviewableImage)
                    <img src="{{ $currentImage->temporaryUrl() }}" class="max-w-full max-h-full rounded-lg" />
                @elseif ($currentImage)
                    <img src="{{ Storage::url($currentImage) }}" class="max-w-full max-h-full rounded-lg" />
                @endif
            </div>

            {{-- LOADING --}}
            <div wire:loading wire:target="formValues.{{ $pivotId }}.{{ $row }}.{{ $field->id }}"
                class="text-xs text-gray-500">
                uploading ...
            </div>

            {{-- INPUT --}}
            <flux:input type="file" id="image-input-{{ $pivotId }}_{{ $row }}_{{ $field->id }}"
                wire:model="formValues.{{ $pivotId }}.{{ $row }}.{{ $field->id }}" accept="image/*"
                x-on:change="checkImage($event)" />

            <small class="text-xs text-gray-500 block leading-tight">
                @if ($currentImage && !$isTemp)
                    Choose a new image to replace the one above • JPG, PNG, WEBP, GIF • Max 2MB
                @else
                    Only: JPG, PNG, WEBP, GIF • Max size: 2MB
                @endif
            </small>

            {{-- ERROR --}}
            <div x-show="error" x-text="error" class="text-xs text-red-500 mt-1"></div>

            {{-- VALIDATION ERROR --}}
            <flux:error name="formValues.{{ $pivotId }}.{{ $row }}.{{ $field->id }}" />

        </div>

    </div>
@else
    <div
        class="grid grid-cols-1 md:grid-cols-{{ count(\Mcamara\LaravelLocalization\Facades\LaravelLocalization::getSupportedLocales()) }} gap-2">

        @foreach (\Mcamara\LaravelLocalization\Facades\LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
            @php
                $currentImage = data_get($formValues, "{$pivotId}.{$row}.{$field->id}.{$localeCode}");

                $isTemp = $currentImage instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

                $isPreviewableImage = $isTemp && str_starts_with($currentImage->getMimeType() ?? '', 'image/');
            @endphp

            <div class="space-y-1"
                wire:key="locale-{{ $pivotId }}-{{ $row }}-{{ $field->id }}-{{ $localeCode }}"
                x-data="{
                    error: null,
                    showLightbox: false,
                    checkImage(event) {
                        const file = event.target.files[0];
                        this.error = null;
                        if (!file) return;

                        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
                        const maxSize = 2 * 1024 * 1024;

                        if (!allowedTypes.includes(file.type)) {
                            this.error = 'Only JPG, PNG, WEBP and GIF formats are allowed.';
                            event.target.value = '';
                            return;
                        }

                        if (file.size > maxSize) {
                            this.error = 'File size must not exceed 2MB.';
                            event.target.value = '';
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

                {{-- PREVIEW (shown above the input) --}}
                <div wire:loading.remove
                    wire:target="formValues.{{ $pivotId }}.{{ $row }}.{{ $field->id }}.{{ $localeCode }}">

                    <div class="mb-2"
                        wire:key="preview-{{ $pivotId }}-{{ $row }}-{{ $field->id }}-{{ $localeCode }}-{{ $isTemp ? $currentImage->hashName() : 'static' }}">

                        {{-- VALID IMAGE PREVIEW --}}
                        @if ($isPreviewableImage)
                            <div class="relative inline-block cursor-zoom-in group" x-on:click="showLightbox = true">
                                <img src="{{ $currentImage->temporaryUrl() }}"
                                    class="w-16 h-16 object-cover rounded-lg border" />
                                <div
                                    class="absolute inset-0 flex items-center justify-center bg-black/0 group-hover:bg-black/30 rounded-lg transition">
                                    <flux:icon name="magnifying-glass-plus"
                                        class="size-5 text-white opacity-0 group-hover:opacity-100 transition" />
                                </div>
                            </div>

                            {{-- INVALID TEMP FILE --}}
                        @elseif ($isTemp)
                            <span class="text-xs text-red-500">Invalid file type selected.</span>

                            {{-- STORED IMAGE --}}
                        @elseif ($currentImage)
                            <div class="relative inline-block cursor-zoom-in group" x-on:click="showLightbox = true">
                                <img src="{{ Storage::url($currentImage) }}"
                                    class="w-16 h-16 object-cover rounded-lg border" />
                                <div
                                    class="absolute inset-0 flex items-center justify-center bg-black/0 group-hover:bg-black/30 rounded-lg transition">
                                    <flux:icon name="magnifying-glass-plus"
                                        class="size-5 text-white opacity-0 group-hover:opacity-100 transition" />
                                </div>
                            </div>


                            <div class="flex items-center gap-2 text-xs">
                                <button type="button"
                                    wire:click="removeFile({{ $pivotId }}, {{ $row }}, {{ $field->id }}, '{{ $localeCode }}')"
                                    wire:confirm="Remove this file?" class="text-red-500 hover:underline">
                                    Remove
                                </button>

                            </div>
                        @endif

                    </div>
                </div>

                {{-- LIGHTBOX --}}
                <div x-show="showLightbox" x-cloak x-on:keydown.escape.window="showLightbox = false"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4"
                    x-on:click.self="showLightbox = false" style="display: none;">
                    <button type="button" x-on:click="showLightbox = false"
                        class="absolute top-4 right-4 text-white text-2xl leading-none">&times;</button>

                    @if ($isPreviewableImage)
                        <img src="{{ $currentImage->temporaryUrl() }}" class="max-w-full max-h-full rounded-lg" />
                    @elseif ($currentImage)
                        <img src="{{ Storage::url($currentImage) }}" class="max-w-full max-h-full rounded-lg" />
                    @endif
                </div>

                {{-- LOADING --}}
                <div wire:loading
                    wire:target="formValues.{{ $pivotId }}.{{ $row }}.{{ $field->id }}.{{ $localeCode }}"
                    class="text-xs text-gray-500">
                    uploading ...
                </div>

                {{-- INPUT --}}
                <flux:input type="file"
                    id="image-input-{{ $pivotId }}_{{ $row }}_{{ $field->id }}_{{ $localeCode }}"
                    wire:model="formValues.{{ $pivotId }}.{{ $row }}.{{ $field->id }}.{{ $localeCode }}"
                    accept="image/*" x-on:change="checkImage($event)" />

                <small class="text-xs text-gray-500 block leading-tight">
                    @if ($currentImage && !$isTemp)
                        Choose a new image to replace the one above • JPG, PNG, WEBP, GIF • Max 2MB
                    @else
                        Only: JPG, PNG, WEBP, GIF • Max size: 2MB
                    @endif
                </small>

                {{-- ERROR --}}
                <div x-show="error" x-text="error" class="text-xs text-red-500 mt-1"></div>

                {{-- VALIDATION ERROR --}}
                <flux:error
                    name="formValues.{{ $pivotId }}.{{ $row }}.{{ $field->id }}.{{ $localeCode }}" />

            </div>
        @endforeach
    </div>
@endif
