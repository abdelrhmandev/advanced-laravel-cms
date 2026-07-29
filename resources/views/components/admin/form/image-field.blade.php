@props([
    'model',
    'value',
    'existingValue' => null,
    'label' => null,
    'disk' => 'public',
    'accept' => 'image/*',
    'hint' => null,
    'fallbackInitials' => null,
    'shape' => 'rounded-lg',
])

<flux:field class="mb-6">

    <div
        x-data="{ uploading: false, progress: 0 }"
        x-on:livewire-upload-start="uploading = true"
        x-on:livewire-upload-finish="uploading = false"
        x-on:livewire-upload-error="uploading = false"
        x-on:livewire-upload-progress="progress = $event.detail.progress"
        class="flex items-center gap-6 relative"
    >


        <div
            wire:key="preview-{{ $model }}-{{ $value instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile ? $value->hashName() : 'static' }}"
            class="size-16 min-w-[64px] {{ $shape }} bg-zinc-100 border border-zinc-200 flex items-center justify-center overflow-hidden relative"
        >


            <div
                x-show="uploading"
                class="absolute inset-0 bg-white/70 flex flex-col items-center justify-center z-10"
            >
                <svg class="animate-spin h-5 w-5 text-blue-600 mb-1" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                </svg>
                <span class="text-[10px]" x-text="progress + '%'"></span>
            </div>

            @if ($value instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)
                @if (in_array(strtolower($value->getClientOriginalExtension()), ['png','jpg','jpeg','webp','gif']))
                    <img src="{{ $value->temporaryUrl() }}" class="size-full object-cover">
                @else
                        <span class="text-[10px] text-danger-400">Invalid File</span>
                @endif
            @elseif ($existingValue)
                <img src="{{ Storage::disk($disk)->url($existingValue) }}" class="size-full object-cover">
            @elseif ($fallbackInitials)
                <span class="text-zinc-500 font-semibold text-lg">{{ $fallbackInitials }}</span>
            @else
                <span class="text-zinc-400 text-xs">No image</span>
            @endif
        </div>

        <!-- ✅ Input + Progress -->
        <div class="flex flex-col gap-2 w-full">

            <flux:input
                type="file"
                wire:model="{{ $model }}"
                :accept="$accept"
                wire:loading.attr="disabled"
                wire:target="{{ $model }}"
                x-bind:disabled="uploading"
            />


            <div x-show="uploading" class="w-full">
                <div class="w-full bg-gray-200 rounded h-2 overflow-hidden">
                    <div
                        class="bg-blue-500 h-2 transition-all duration-200"
                        :style="'width: ' + progress + '%'"
                    ></div>
                </div>
                <div class="text-xs text-gray-500 mt-1">
                    Uploading... <span x-text="progress"></span>%
                </div>
            </div>

            @if ($hint)
                <p class="text-xs text-zinc-500">{{ $hint }}</p>
            @endif

        </div>
    </div>

    <flux:error name="{{ $model }}" />
</flux:field>
