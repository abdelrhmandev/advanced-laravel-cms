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

            {{-- Name --}}
            <flux:field class="mb-6">
                <flux:label required>{{ __('Name') }} <span class="text-red-500 ms-1">*</span></flux:label>
                <flux:input wire:model="name" id="name" type="text" placeholder="Full Name" />
                <flux:error name="name" />
            </flux:field>

            {{-- Email --}}
            <flux:field class="mb-6">
                <flux:label required>{{ __('Email') }} <span class="text-red-500 ms-1">*</span></flux:label>
                <flux:input wire:model.live.debounce.500ms="email" id="email" type="email"
                    placeholder="Email Address" />
                <flux:error name="email" />
            </flux:field>

            {{-- Password --}}
            @if (!$Id)
                <flux:field class="mb-6">
                    <flux:label required>{{ __('Password') }} <span class="text-red-500 ms-1">*</span></flux:label>
                    <flux:input wire:model="password" type="password" id="password"
                        placeholder="{{ __('Enter strong password') }}" autocomplete="new-password" viewable />
                    <flux:error name="password" />
                </flux:field>
                <flux:field class="mb-6">
                    <flux:button type="button" wire:click="generatePassword" variant="ghost" size="sm"
                        icon="key">
                        {{ __('Generate Password') }}
                    </flux:button>
                </flux:field>
            @endif

            {{-- Mobile --}}
            <flux:field class="mb-6">
                <flux:label>{{ __('Mobile') }}</flux:label>
                <flux:input wire:model.live.debounce.500ms="mobile" id="mobile" type="text"
                    placeholder="01xxxxxxxxx" />
                <flux:error name="mobile" />
            </flux:field>

            {{-- Avatar --}}
            <flux:field class="mb-6">
                <flux:label>{{ __('Avatar') }}</flux:label>
                <x-admin.form.image-field model="avatar" :value="$avatar" :existing-value="$existingAvatar"
                    hint="PNG, JPG, WEBP (Max 2MB)" shape="rounded-full" :fallback-initials="Auth::guard('admin')->user()->initials()" />
            </flux:field>

            {{-- Status --}}
            <flux:field class="mb-6">
                <flux:label>{{ __('Status') }}</flux:label>
                <div class="flex items-center gap-3">
                    <flux:switch wire:model="is_active" />
                    <span class="text-sm text-gray-700">{{ __('Active') }}</span>
                </div>
                <flux:error name="is_active" />
            </flux:field>

            {{-- Roles --}}
            <flux:field class="mb-6">
                <flux:label required>{{ __('Assign Roles') }} <span class="text-red-500 ms-1">*</span></flux:label>

                <div class="flex flex-col gap-3 pt-2">
                    @foreach ($roles as $role)
                        <div class="flex items-center gap-3" wire:key="role-{{ $role->id }}">
                            <flux:checkbox wire:model.live="selectedRoles" value="{{ $role->id }}" />
                            <span class="text-sm text-zinc-700 dark:text-zinc-300 capitalize">
                                {{ str_replace('-', ' ', $role->name) }}
                            </span>
                        </div>
                    @endforeach
                </div>

                <flux:error name="selectedRoles" />
            </flux:field>

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
                    @php
                        $auth = auth('admin')->user();

                        $canDelete =
                            $auth->can($permissionPrefix . '.delete') &&
                            !$user->hasRole('super-admin') &&
                            $auth->id !== $Id;
                    @endphp
                    @if ($canDelete)
                        <x-admin.delete-button :id="$Id" :permission="$permissionPrefix" />
                    @endif
                @endif
            </div>

        </form>
    </flux:card>

    @if ($Id)
        <x-admin.confirm-delete-js :module="$module" delete-action="delete" />

        <div class="mt-5">
            <livewire:admin.cms.users.update-password-form :user-id="$Id" />
        </div>
    @endif
</div>
