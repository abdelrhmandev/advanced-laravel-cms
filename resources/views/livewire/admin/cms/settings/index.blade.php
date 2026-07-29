<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Global Configurations') }}</flux:heading>
            <flux:subheading>{{ __('Manage global settings and multilingual content') }}</flux:subheading>
        </div>


        <div class="flex items-center justify-between mb-6">
            <div class="flex gap-2">
                <flux:button wire:click="refreshCache" wire:loading.attr="disabled" variant="primary">

                    <span wire:loading.remove wire:target="refreshCache" class="flex items-center gap-2">
                        <flux:icon.arrow-path class="w-5 h-5" />
                        Force Cache Refresh
                    </span>

                    <span wire:loading class="flex items-center gap-2">
                        <flux:icon.arrow-path class="w-4 h-4 animate-spin" />
                        Processing...
                    </span>

                </flux:button>


                @if (auth('admin')->user()?->can('settings.create'))
                    <flux:modal.trigger name="add-setting">
                        <flux:button variant="primary">
                            <flux:icon.plus class="w-5 h-5" />
                            {{ __('Add New Setting') }}
                        </flux:button>
                    </flux:modal.trigger>
                @endif


            </div>
        </div>



    </div>
    @include('livewire.admin.cms.settings.add')
    @php
        $admin = auth('admin')->user();
    @endphp

    @if (!$admin || !$admin->can('settings.edit'))
        @include('livewire.admin.partials.permissions.403_message')
    @else
        <form id="SaveSettings" wire:submit.prevent="save" enctype="multipart/form-data" class="space-y-6">

            {{-- Text & Textarea Settings --}}
            <flux:card>
                <div class="flex items-start gap-4 mb-6">
                    <flux:icon name="language" class="text-primary" />

                    <div>
                        <flux:heading size="lg">
                            {{ __('Site Configurations') }}
                        </flux:heading>

                        <flux:text size="sm" class="text-gray-500">
                            {{ __('Manage global settings and multilingual content') }}
                        </flux:text>
                    </div>
                </div>

                <div class="space-y-4">
                    @foreach ($settingsCollection->whereIn('type', ['textbox', 'textarea', 'number'])->filter(fn($s) => !str_starts_with($s['key'], 'social_')) as $index => $setting)
                        <flux:field>
                            <flux:label>{{ $setting['label'] }}</flux:label>

                            @if ($setting['type'] === 'textarea')
                                <flux:textarea wire:model="settings.{{ $index }}.value"
                                    class="{{ str_ends_with($setting['key'], '_ar') ? 'text-right' : '' }}"
                                    rows="3" />
                            @elseif ($setting['type'] === 'number')
                                <flux:input type="number" wire:model="settings.{{ $index }}.value"
                                    min="1"
                                    class="{{ str_ends_with($setting['key'], '_ar') ? 'text-right' : '' }}" />
                            @else
                                <flux:input type="text" wire:model="settings.{{ $index }}.value"
                                    class="{{ str_ends_with($setting['key'], '_ar') ? 'text-right' : '' }}" />
                            @endif
                        </flux:field>
                    @endforeach
                </div>
            </flux:card>

            {{-- Branding & Media --}}
            <flux:card class="mb-8">

                {{-- Header --}}
                <div class="flex items-start gap-4 mb-6">
                    <flux:icon name="photo" class="text-primary" />

                    <div>
                        <flux:heading size="lg">
                            {{ __('Branding & Media') }}
                        </flux:heading>

                        <flux:text size="sm" class="text-gray-500">
                            {{ __('Upload site logos, favicons, and social sharing images') }}
                        </flux:text>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">




                    @foreach ($settingsCollection->where('type', 'image') as $index => $setting)
                        <div class="text-center space-y-3">

                            <div class="relative inline-block group">
                                {{-- Image Container --}}
                                <div
                                    class="w-32 h-32 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden bg-gray-50 dark:bg-gray-800 flex items-center justify-center">
                                    @if (!empty($uploads[$index]))
                                        <img src="{{ $uploads[$index]->temporaryUrl() }}"
                                            class="w-full h-full object-contain" />
                                    @elseif (!empty($setting['value']))
                                        <img src="{{ asset('storage/' . $setting['value']) }}"
                                            class="w-full h-full object-contain" />
                                    @else
                                        <flux:icon name="photo" class="w-10 h-10 text-gray-400" />
                                    @endif
                                </div>

                                {{-- Hover Overlay --}}
                                <label
                                    class="absolute inset-0 rounded-lg bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer flex items-center justify-center">
                                    <div class="flex flex-col items-center gap-1 text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                                        </svg>
                                        <span class="text-xs font-medium">Change</span>
                                    </div>
                                    <input type="file" wire:model="uploads.{{ $index }}"
                                        accept=".png,.jpg,.jpeg,.ico" class="sr-only" />
                                </label>

                                {{-- Loading Spinner --}}
                                <div wire:loading wire:target="uploads.{{ $index }}"
                                    class="absolute inset-0 rounded-lg bg-black/60 flex items-center justify-center">
                                    <svg class="animate-spin w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4" />
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                                    </svg>
                                </div>

                                @if (!empty($setting['value']))
                                    <flux:button wire:click="confirmDelete({{ $index }})" size="sm"
                                        variant="ghost" icon="trash"
                                        class="absolute -top-2 -right-2 w-5 h-5 rounded-full bg-red-500 hover:bg-red-600 text-white flex items-center justify-center transition-colors" />
                                @endif
                            </div>

                            {{-- Label --}}
                            <flux:text size="sm" class="text-gray-500 font-medium">
                                {{ $setting['label'] }}
                            </flux:text>

                            {{-- Error --}}
                            @error("uploads.$index")
                                <flux:text class="text-red-500 text-xs">{{ $message }}</flux:text>
                            @enderror

                        </div>
                    @endforeach





                </div>

            </flux:card>


            {{-- Contact & Location --}}
            <flux:card class="mb-8">
                <div class="flex items-start gap-4 mb-6">
                    <flux:icon name="map-pin" class="text-primary" />
                    <div>
                        <flux:heading size="lg">
                            {{ __('Contact & Location') }}
                        </flux:heading>

                        <flux:text size="sm" class="text-gray-500">
                            {{ __('Manage store addresses, emails, and map coordinates') }}
                        </flux:text>
                    </div>
                </div>
                <div class="space-y-5">
                    @foreach ($settingsCollection->whereIn('type', ['email', 'phone', 'mobile', 'google_map'])->filter(fn($s) => !str_contains($s['key'], 'social_')) as $index => $setting)
                        <flux:field wire:key="setting-{{ $setting['id'] }}">
                            <flux:label>
                                {{ $setting['label'] }}
                            </flux:label>
                            <flux:input.group>
                                <flux:input.group.prefix>


                                    @if ($setting['type'] === 'email')
                                        <flux:icon name="envelope" class="text-gray-500" />
                                    @elseif ($setting['type'] === 'google_map')
                                        <flux:icon name="map" class="text-gray-500" />
                                    @else
                                        <flux:icon name="phone" class="text-gray-500" />
                                    @endif
                                </flux:input.group.prefix>

                                <flux:input type="text" wire:model="settings.{{ $index }}.value" />

                            </flux:input.group>



                        </flux:field>
                    @endforeach

                </div>

            </flux:card>


            {{-- Social Networks --}}

            <flux:card class="mb-8">
                <div class="flex items-start gap-4 mb-6">
                    <flux:icon name="map-pin" class="text-primary" />
                    <div>
                        <flux:heading size="lg">
                            {{ __('Social Networks') }}
                        </flux:heading>

                        <flux:text size="sm" class="text-gray-500">
                            {{ __('Link your official social media profiles') }}
                        </flux:text>
                    </div>
                </div>
                <div class="space-y-5">

                    @foreach ($settingsCollection->filter(fn($s) => str_contains($s['key'], 'social_')) as $index => $setting)
                        @php
                            $key = $setting['key'];
                            $icon = match (true) {
                                str_contains($key, 'facebook')
                                    => '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>',
                                str_contains($key, 'twitter')
                                    => '<path d="M4 4l11.733 16H20L8.267 4zm0 16 6.768-6.768M20 4l-6.768 6.768"/>',
                                str_contains($key, 'instagram')
                                    => '<rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/>',
                                str_contains($key, 'linkedin')
                                    => '<path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect width="4" height="12" x="2" y="9"/><circle cx="4" cy="4" r="2"/>',
                                str_contains($key, 'youtube')
                                    => '<path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 0 0-1.95 1.96A29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58a2.78 2.78 0 0 0 1.95 1.95C5.12 20 12 20 12 20s6.88 0 8.59-.47a2.78 2.78 0 0 0 1.95-1.95A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"/><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02"/>',
                                str_contains($key, 'tiktok') => '<path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"/>',
                                default
                                    => '<path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>',
                            };
                        @endphp
                        <flux:input.group>
                            <flux:input.group.prefix>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="text-gray-500">
                                    {!! $icon !!}
                                </svg>
                            </flux:input.group.prefix>
                            <flux:input type="text" wire:model="settings.{{ $index }}.value" />
                        </flux:input.group>
                    @endforeach


                </div>

            </flux:card>

            <div class="flex items-center gap-3 mt-6">
                <flux:button type="submit" variant="primary" wire:target="save">Save</flux:button>
            </div>
        </form>
        <flux:modal name="confirm-delete-image" class="min-w-[22rem]">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">{{ __('Delete Image') }}</flux:heading>
                    <flux:subheading>
                        {{ __('Are you sure you want to delete this image? This action cannot be undone.') }}
                    </flux:subheading>
                </div>
                <div class="flex gap-2 justify-end">
                    <flux:modal.close>
                        <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                    </flux:modal.close>
                    <flux:button wire:click="removeImage" variant="danger">
                        {{ __('Delete') }}
                    </flux:button>
                </div>
            </div>
        </flux:modal>
    @endif

</div>
