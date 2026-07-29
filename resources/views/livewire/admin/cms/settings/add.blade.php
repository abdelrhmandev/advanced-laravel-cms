@if (auth('admin')->user()?->can('settings.create'))
<flux:modal name="add-setting" class="max-w-lg">
    <form wire:submit.prevent="addNewSetting" class="space-y-6">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold">
                    Add New Configuration
                </h2>

            </div>


        </div>

        <!-- Divider -->
        <div class="border-t"></div>

        <!-- Label -->
        <flux:field>
            <flux:label>Setting Label</flux:label>

            <flux:input wire:model.defer="new_setting.label" placeholder="e.g. WhatsApp Number" />

            <flux:description>
                Human readable name
            </flux:description>

            <flux:error name="new_setting.label" />
        </flux:field>

        <!-- Key -->
        <flux:field>
            <flux:label>Key</flux:label>

            <flux:input wire:model.defer="new_setting.key" placeholder="e.g. social_whatsapp" />

            <flux:description>
                Unique identifier (no spaces)
            </flux:description>

            <flux:error name="new_setting.key" />
        </flux:field>

        <!-- Type -->
        <flux:field>
            <flux:label>Field Type</flux:label>

            <flux:select wire:model="new_setting.type">
                <flux:select.option value="textbox">Text Input</flux:select.option>
                <flux:select.option value="textarea">Textarea</flux:select.option>
                <flux:select.option value="social">Social Media</flux:select.option>
                <flux:select.option value="image">Image / Media</flux:select.option>
            </flux:select>

            <flux:description>
                Determines how the value is rendered
            </flux:description>
        </flux:field>

        <!-- Translate Toggle -->
        <flux:field>
            <div class="flex items-center justify-between">
                <div>
                    <flux:label>Translate</flux:label>
                    <flux:description>
                        Enable multi-language support
                    </flux:description>
                </div>

                <flux:switch wire:model="new_setting.is_translatable" />
            </div>
        </flux:field>

        <!-- Footer -->
        <div class="flex justify-end gap-3 pt-4 border-t">

            <flux:button variant="ghost" x-on:click="$flux.modal('add-setting').close()">
                Cancel
            </flux:button>

            <flux:button type="submit" variant="primary" wire:loading.attr="disabled" class="min-w-[140px]">
                <span wire:loading.remove>
                    Create Setting
                </span>

                <span wire:loading class="flex items-center justify-center gap-2">
                    <flux:icon.arrow-path class="w-4 h-4 animate-spin" />
                    Saving...
                </span>
            </flux:button>

        </div>

    </form>
</flux:modal>
@endif
