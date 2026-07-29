<div class="space-y-6">



    <div class="flex items-center justify-between">
        @if ($Id)
            <flux:heading size="xl">{{ __('Edit ' . ucfirst(\Str::singular($module))) }}</flux:heading>
        @else
            <flux:heading size="xl">{{ __('Add New ' . ucfirst(\Str::singular($module))) }}</flux:heading>
        @endif
    </div>


    <flux:card>
        <form wire:submit.prevent="save">


            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Main content: translatable fields --}}
                <div class="lg:col-span-2 space-y-6">
                    <div x-data="{ activeLang: '{{ LaravelLocalization::getCurrentLocale() }}' }" class="space-y-4">
                        {{-- Tab buttons --}}



                        <div class="inline-flex gap-1 p-1 bg-zinc-100 dark:bg-zinc-800 rounded-lg">
                            @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                @php
                                    $hasError = collect($errors->getMessages())
                                        ->keys()
                                        ->contains(fn($key) => str_starts_with($key, "translations.$localeCode."));
                                    $flagUrl = config("project.locale_flags.{$localeCode}");
                                @endphp

                                <button type="button" @click="activeLang = '{{ $localeCode }}'"
                                    :class="activeLang === '{{ $localeCode }}'
                                        ?
                                        'bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white shadow-sm' :
                                        'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300'"
                                    class="relative flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-md transition">
                                    {{-- Label --}}
                                    <span class="flex items-center gap-1">
                                        {{ $properties['name'] }}

                                        @if ($flagUrl)
                                            <img src="{{ $flagUrl }}" class="w-4 h-3 object-cover rounded-sm">
                                        @endif
                                    </span>

                                    {{-- Error dot --}}
                                    @if ($hasError)
                                        <div class="flex items-center gap-1 text-red-500 text-xs mt-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01M10.29 3.86l-7.4 12.8A1 1 0 003.76 18h16.48a1 1 0 00.87-1.54l-7.4-12.8a1 1 0 00-1.74 0z" />
                                            </svg>

                                        </div>
                                    @endif
                                </button>
                            @endforeach
                        </div>

                        {{-- Tab panels --}}
                        @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                            <div x-show="activeLang === '{{ $localeCode }}'" x-cloak class="space-y-4">
                                <flux:card>
                                    <div class="space-y-4">
                                        <x-admin.form.text-input label="{{ __('Title') }} ({{ $localeCode }})"
                                            model="translations.{{ $localeCode }}.title" :required="true" />

                                        <x-admin.form.text-input label="{{ __('Slug') }} ({{ $localeCode }})"
                                            model="translations.{{ $localeCode }}.slug" :required="true" />

                                        <x-admin.form.richtext-input
                                            label="{{ __('Description') }} ({{ $localeCode }})"
                                            model="translations.{{ $localeCode }}.description" :properties="$properties" />
                                    </div>
                                </flux:card>
                                <x-admin.form.seo-meta :properties="$properties" :locale-code="$localeCode" />
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="lg:col-span-1 space-y-6">
                    <flux:card>
                        <div class="space-y-4">
                            <x-admin.form.checkbox-input label="{{ __('Status') }}" model="is_active"
                                placeholder="{{ __('Active') }}" />
                        </div>
                    </flux:card>

                    <flux:card>
                        <flux:heading size="sm" class="mb-4">{{ __('Featured Image') }}</flux:heading>
                        <div class="space-y-4">
                            <x-admin.form.image-field model="image" :value="$image" :existingValue="$existingImage"
                                hint="PNG, JPG, WEBP (Max 2MB)" shape="rounded-full" />
                        </div>
                    </flux:card>


                    <flux:card>
                        <div class="space-y-4">
                            <x-admin.form.select-input label="{{ __('Template') }}" model="template" :options="config('project.app.page_templates')"
                                placeholder="{{ __('Select template') }}" />
                        </div>
                    </flux:card>



                    <flux:card>
                        <div class="space-y-4">
                            <x-admin.form.checkbox-input-multi label="{{ __('Blocks') }}" multi="true" model="block"
                                :options="$blocks" placeholder="{{ __('Select block') }}" />
                        </div>
                    </flux:card>





                    <flux:card>
                        <x-admin.form.seo-media :ogImage="$ogImage" :existingOgImage="$existingOgImage" :twitterImage="$twitterImage"
                            :existingTwitterImage="$existingTwitterImage" />
                    </flux:card>


                </div>





            </div>

            {{-- Footer --}}
            <div class="flex justify-end gap-3 mt-6">
                <flux:button href="{{ route('admin.' . $route . '.index') }}" variant="ghost" wire:navigate>
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button variant="primary" type="submit" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save">{{ $Id ? __('Update') : __('Save') }}</span>
                    <span wire:loading wire:target="save">{{ __('Saving...') }}</span>
                </flux:button>

                @if ($Id)
                    <x-admin.delete-button :id="$Id" :permission="$permissionPrefix" />
                @endif
            </div>


        </form>
    </flux:card>


</div>
