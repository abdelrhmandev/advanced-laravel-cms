<div x-data="blockEditor()" x-init="initSortable()" class="flex gap-6 items-start">




    {{-- LEFT --}}
    <div class="flex-1 space-y-4 min-w-0">

        <flux:card class="space-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="lg">


                        <div class="space-y-2">
                            <flux:heading size="lg">{{ __('Title') }}</flux:heading>

                            @foreach (\Mcamara\LaravelLocalization\Facades\LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                @php
                                    $flagUrl = config("project.locale_flags.{$localeCode}");
                                @endphp

                                <div class="flex items-center gap-2">
                                    @if ($flagUrl)
                                        <img src="{{ $flagUrl }}" class="w-4 h-3 object-cover rounded-sm">
                                    @endif

                                    <span class="text-sm text-zinc-500">{{ $properties['native'] }}:</span>
                                    <span>{{ $block->getTranslatedTitle($localeCode) }}</span>
                                </div>
                            @endforeach
                        </div>



                    </flux:heading>
                    <code class="text-xs text-zinc-400">{{ $block->slug }}</code>
                </div>
                <div class="flex items-center gap-4">
                    @if ($block->is_repeatable)
                        <flux:badge color="purple">{{ __('Repeatable') }}</flux:badge>
                    @endif
                    <flux:field variant="inline">
                        <flux:switch :checked="(bool) $block->show_title" :disabled="!$block->show_title" />
                        <flux:label class="text-sm text-zinc-500">{{ __('Show title') }}</flux:label>
                    </flux:field>
                </div>
            </div>
        </flux:card>

        <flux:card class="p-0 overflow-hidden">
            <div class="flex items-center justify-between px-4 py-3 border-b">
                <flux:heading size="sm">{{ __('Fields') }} ({{ $block->fields->count() }})</flux:heading>

                <flux:tooltip content="Click To Add New Field">
                    <flux:button wire:click="openAddPanel" size="sm" variant="ghost" icon="plus">
                        {{ __('Add Field') }}
                    </flux:button>
                </flux:tooltip>
            </div>

            <ul id="fields-sortable" class="divide-y" wire:ignore.self>
                @forelse ($block->fields as $field)
                    <li wire:key="field-{{ $field->id }}" data-id="{{ $field->id }}"
                        class="flex items-center gap-3 px-4 py-3 group hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors"
                        :class="{ 'bg-primary-50 dark:bg-primary-900/20': activeField == {{ $field->id }} }">
                        <span class="drag-handle cursor-grab text-zinc-400 shrink-0">
                            <flux:icon name="bars-3" class="size-4" />
                        </span>

                        <span class="text-zinc-400 shrink-0">
                            @switch($field->type)
                                @case('text')
                                    <flux:icon name="bars-2" />
                                @break

                                @case('textarea')
                                    <flux:icon name="bars-3" />
                                @break

                                @case('richtext')
                                    <flux:icon name="document-text" />
                                @break

                                @case('image')
                                    <flux:icon name="photo" />
                                @break

                                @case('icon')
                                    <flux:icon name="clipboard-document" />
                                @break

                                @case('file')
                                    <flux:icon name="paper-clip" />
                                @break

                                @case('number')
                                    <flux:icon name="hashtag" />
                                @break

                                @case('select')
                                    <flux:icon name="chevron-up-down" />
                                @break

                                @case('color')
                                    <flux:icon name="swatch" />
                                @break

                                @case('repeater')
                                    <flux:icon name="table-cells" />
                                @break

                                @case('relation')
                                    <flux:icon name="link" />
                                @break
                            @endswitch
                        </span>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="font-medium truncate">{{ $field->label }}</span>
                                @if ($field->required)
                                    <span class="text-red-400 shrink-0">*</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 mt-0.5">
                                <code class="text-xs text-zinc-400">{{ $field->key }}</code>

                                @if ($field->required)
                                    <flux:badge size="sm" color="red">required</flux:badge>
                                @endif

                                @if ($field->translatable)
                                    <flux:badge size="sm" color="purple">trans</flux:badge>
                                @endif


                                @if ($field->isRepeater())
                                    <flux:badge size="sm" color="blue">
                                        {{ $field->children->count() }} sub-fields
                                    </flux:badge>
                                @endif
                            </div>
                        </div>

                        <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity shrink-0">
                            <flux:tooltip content="Edit Field">
                                <flux:button wire:click="editField({{ $field->id }})"
                                    x-on:click="activeField = {{ $field->id }}" size="sm" variant="ghost"
                                    icon="pencil" />
                            </flux:tooltip>
                            <flux:tooltip content="Delete Field">
                                <flux:button wire:click="confirmDeleteField({{ $field->id }})" size="sm"
                                    variant="ghost" icon="trash" class="text-red-400" />
                            </flux:tooltip>
                        </div>
                    </li>
                    @empty
                        <li class="text-center py-12 text-zinc-400">
                            <flux:icon name="square-3-stack-3d" class="size-8 mx-auto mb-2 opacity-40" />
                            <p class="text-sm">{{ __('No fields yet') }}</p>
                        </li>
                    @endforelse
                </ul>
            </flux:card>
        </div>

        {{-- RIGHT --}}
        <div class="w-80 shrink-0 space-y-4">

            @if ($showAddPanel)
                <flux:card>
                    <div class="flex items-center justify-between mb-4">
                        <flux:heading size="sm">{{ __('Choose type') }}</flux:heading>
                        <flux:button wire:click="closeFieldForm" size="sm" variant="ghost" icon="x-mark" />
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach ($fieldTypes as $type => $meta)
                            <button wire:click="selectType('{{ $type }}')"
                                class="flex flex-col items-center gap-2 p-3 border rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 dark:border-zinc-700 transition-colors text-sm">
                                <flux:icon name="{{ $meta['icon'] }}" class="size-5 text-zinc-500" />
                                <span>{{ $meta['label'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </flux:card>
            @endif

            @if ($showFieldForm)
                <flux:card class="space-y-4">

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <flux:icon name="{{ $fieldTypes[$f_type]['icon'] ?? 'bars-2' }}"
                                class="size-4 text-zinc-400" />
                            <flux:heading size="sm">
                                {{ $activeFieldId ? __('Edit Field') : __('New Field') }}
                            </flux:heading>
                        </div>
                        <flux:button wire:click="closeFieldForm" size="sm" variant="ghost" icon="x-mark" />
                    </div>



                    <flux:input wire:model="f_label" label="{{ __('Label') }} " placeholder="e.g. Hero Title" />
                    <flux:input wire:model="f_key" label="{{ __('Key') }}" placeholder="e.g. hero_title"
                        dir="ltr" />





                    <div class="space-y-2 pt-1">
                        <flux:field variant="inline">
                            <flux:switch wire:model="f_required" />
                            <flux:label>{{ __('Required') }}</flux:label>
                        </flux:field>

                        @if ($f_type !== 'repeater' && $f_type !== 'number' && $f_type !== 'color')
                            <flux:field variant="inline">
                                <flux:switch wire:model="f_translatable" />
                                <flux:label>{{ __('Translatable') }}</flux:label>
                            </flux:field>
                        @endif
                    </div>



                    {{-- TEXT / TEXTAREA / RICHTEXT --}}
                    @if (in_array($f_type, ['text', 'textarea', 'richtext']))
                        <div class="space-y-3 pt-1 border-t dark:border-zinc-700">
                            <p class="text-xs text-zinc-500 uppercase tracking-wide pt-2">{{ __('Settings') }}</p>
                            <flux:input wire:model="f_settings.placeholder" label="{{ __('Placeholder') }}"
                                placeholder="e.g. Enter text here…" />
                            <flux:input type="number" wire:model="f_settings.max_length" label="{{ __('Max length') }}"
                                placeholder="255" />
                        </div>
                    @endif



                    @if (in_array($f_type, ['icon']))
                        <div class="space-y-3 pt-1 border-t dark:border-zinc-700">
                            <p class="text-xs text-zinc-500 uppercase tracking-wide pt-2">{{ __('Settings') }}</p>
                            <flux:input type="number" wire:model="f_settings.max_size"
                                label="{{ __('Max size (KB)') }}" placeholder="2048" />

                        </div>
                    @endif


                    {{-- IMAGE / FILE --}}
                    @if (in_array($f_type, ['image', 'file']))
                        <div class="space-y-3 pt-1 border-t dark:border-zinc-700">
                            <p class="text-xs text-zinc-500 uppercase tracking-wide pt-2">{{ __('Settings') }}</p>
                            <flux:input type="number" wire:model="f_settings.max_size"
                                label="{{ __('Max size (KB)') }}" placeholder="2048" />
                            <flux:field>
                                <flux:label>{{ __('Disk') }}</flux:label>
                                <select wire:model="f_settings.disk"
                                    class="w-full rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 text-sm">
                                    <option value="public">Public</option>
                                    {{-- <option value="s3">S3</option> --}}
                                </select>
                            </flux:field>
                        </div>
                    @endif

                    {{-- NUMBER --}}
                    @if ($f_type === 'number')
                        <div class="space-y-3 pt-1 border-t dark:border-zinc-700">
                            <p class="text-xs text-zinc-500 uppercase tracking-wide pt-2">{{ __('Settings') }}</p>
                            <div class="grid grid-cols-2 gap-2">
                                <flux:input type="number" wire:model="f_settings.min" label="{{ __('Min') }}" />
                                <flux:input type="number" wire:model="f_settings.max" label="{{ __('Max') }}" />
                            </div>
                            <flux:input type="number" wire:model="f_settings.step" label="{{ __('Step') }}"
                                placeholder="1" />
                        </div>
                    @endif

                    {{-- COLOR --}}
                    @if ($f_type === 'color')
                        <div class="space-y-3 pt-1 border-t dark:border-zinc-700">
                            <p class="text-xs text-zinc-500 uppercase tracking-wide pt-2">{{ __('Settings') }}</p>
                            <flux:field>
                                <flux:label>{{ __('Format') }}</flux:label>
                                <select wire:model="f_settings.format"
                                    class="w-full rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 text-sm">
                                    <option value="hex">HEX</option>
                                    <option value="rgb">RGB</option>
                                    <option value="hsl">HSL</option>
                                </select>
                            </flux:field>
                        </div>
                    @endif

                    {{-- SELECT OPTIONS --}}
                    @if ($f_type === 'select')
                        <div class="space-y-3 pt-1 border-t dark:border-zinc-700">
                            <div class="flex items-center justify-between pt-2">
                                <p class="text-xs text-zinc-500 uppercase tracking-wide">{{ __('Options') }}</p>
                                <flux:button wire:click="addOption" size="sm" variant="ghost" icon="plus">
                                    {{ __('Add') }}
                                </flux:button>
                            </div>

                            @forelse ($selectOptions as $i => $option)
                                <div class="flex gap-2 items-center">
                                    <flux:input wire:model="selectOptions.{{ $i }}.value"
                                        placeholder="{{ __('Value') }}" class="flex-1" />
                                    <flux:input wire:model="selectOptions.{{ $i }}.label"
                                        placeholder="{{ __('Label') }}" class="flex-1" />
                                    <flux:button wire:click="removeOption({{ $i }})" size="sm"
                                        variant="ghost" icon="x-mark" class="text-red-400 shrink-0" />
                                </div>
                            @empty
                                <p class="text-xs text-zinc-400">{{ __('No options yet.') }}</p>
                            @endforelse

                            <flux:field variant="inline">
                                <flux:switch wire:model="f_settings.multiple" />
                                <flux:label>{{ __('Multiple select') }}</flux:label>
                            </flux:field>
                        </div>
                    @endif

                    {{-- REPEATER SUB-FIELDS --}}
                    @if ($f_type === 'repeater')
                        <div class="space-y-3 pt-1 border-t dark:border-zinc-700">
                            <div class="flex items-center justify-between pt-2">
                                <p class="text-xs text-zinc-500 uppercase tracking-wide">{{ __('Sub-fields') }}</p>
                                <div class="flex gap-1">
                                    <flux:button wire:click="addTitleIconRow" size="sm" variant="ghost"
                                        icon="photo" title="{{ __('Add Title + Icon') }}" />
                                    <flux:button wire:click="addRepeaterField" size="sm" variant="ghost"
                                        icon="plus" title="{{ __('Add Field') }}" />
                                </div>
                            </div>

                            @forelse ($repeaterFields as $i => $rf)
                                <div class="p-3 border rounded-lg dark:border-zinc-700 space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-zinc-400 font-mono">
                                            {{ $rf['label'] ?: __('Sub-field') . ' #' . ($i + 1) }}
                                        </span>
                                        <flux:button wire:click="removeRepeaterField({{ $i }})" size="sm"
                                            variant="ghost" icon="x-mark" class="text-red-400" />
                                    </div>

                                    <flux:input wire:model="repeaterFields.{{ $i }}.label"
                                        placeholder="{{ __('Label') }}" />
                                    <flux:input wire:model="repeaterFields.{{ $i }}.key"
                                        placeholder="{{ __('Key') }}" dir="ltr" />

                                    <flux:field>
                                        <flux:label>{{ __('Type') }}</flux:label>
                                        <select wire:model="repeaterFields.{{ $i }}.type"
                                            class="w-full rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 text-sm">
                                            <option value="text">Text</option>
                                            <option value="textarea">Textarea</option>
                                            <option value="richtext">Rich Text</option>
                                            <option value="image">Image</option>
                                            <option value="icon">Icon</option>
                                            <option value="file">File</option>
                                            <option value="number">Number</option>
                                            <option value="color">Color</option>
                                        </select>
                                    </flux:field>

                                    <flux:field variant="inline">
                                        <flux:switch wire:model="repeaterFields.{{ $i }}.translatable" />
                                        <flux:label>{{ __('Translatable') }}</flux:label>
                                    </flux:field>
                                </div>
                            @empty
                                <p class="text-xs text-zinc-400 text-center py-2">{{ __('No sub-fields yet.') }}</p>
                            @endforelse
                        </div>
                    @endif

                    {{-- DEFAULT VALUE --}}
                    @if (!in_array($f_type, ['icon', 'image', 'file', 'repeater', 'color', 'relation', 'number']))
                        <flux:input wire:model="f_default" label="{{ __('Default value') }}"
                            placeholder="{{ __('Optional') }}" />
                    @endif

                    <flux:button wire:click="saveField" variant="primary" class="w-full">
                        {{ __('Save Field') }}
                    </flux:button>

                </flux:card>
            @endif

        </div>


        <flux:modal name="confirm-delete-field" class="max-w-sm">
            <div class="space-y-4">
                <flux:heading size="lg">{{ __('Delete Field?') }}</flux:heading>
                <flux:text class="text-zinc-500">
                    {{ __('This will permanently delete the field and all its sub-fields and stored values.') }}
                </flux:text>
                <div class="flex justify-end gap-3">
                    <flux:button x-on:click="$flux.modal('confirm-delete-field').close()">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button wire:click="deleteField" variant="danger">
                        {{ __('Delete') }}
                    </flux:button>
                </div>
            </div>
        </flux:modal>

    </div>

    @script
        <script>
            Alpine.data('blockEditor', () => ({
                activeField: null,

                initSortable() {
                    this.mountSortable();
                    Livewire.hook('commit', ({
                        succeed
                    }) => {
                        succeed(() => {
                            this.$nextTick(() => this.mountSortable());
                        });
                    });
                },

                mountSortable() {
                    const el = document.getElementById('fields-sortable');
                    if (!el || typeof Sortable === 'undefined') return;
                    if (el._sortable) el._sortable.destroy();
                    el._sortable = Sortable.create(el, {
                        handle: '.drag-handle',
                        animation: 150,
                        onEnd: () => {
                            const order = [...el.querySelectorAll('[data-id]')]
                                .map(el => parseInt(el.dataset.id));
                            $wire.dispatch('fields-reordered', {
                                order
                            });
                        }
                    });
                }
            }));
        </script>
    @endscript
