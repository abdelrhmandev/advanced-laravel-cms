<?php

use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;
new class extends Component {
    public int $userId;
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(int $userId): void
    {
        $this->userId = $userId;
    }

    public function generatePassword(): void
    {
        $randomPassword = Str::random(12);
        $this->password = $randomPassword;
        Flux::toast(variant: 'success', heading: 'Done!', text: 'Password generated: ' . $randomPassword);
    }

    public function updatePassword(): void
    {
        $validated = $this->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ]);

        User::findOrFail($this->userId)->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('password', 'password_confirmation');

        Flux::toast(variant: 'success', heading: 'Success!', text: 'Password updated successfully.');
    }
};
?>



<flux:card class="mb-6">

    <flux:heading size="lg" class="font-bold mb-1">{{ __('Update Password') }}</flux:heading>
    <flux:description class="mb-6">{{ __('Ensure your account is using a long, random password to stay secure.') }}
    </flux:description>

    <form wire:submit="updatePassword" class="max-w-lg">

        <flux:field class="mb-4">
            <flux:label required>{{ __('New Password') }}</flux:label>
            <flux:input wire:model="password" type="password" placeholder="Enter strong password"
                autocomplete="new-password" viewable />
            <flux:error name="password" />
        </flux:field>

        <flux:field class="mb-4">
            <flux:label required>{{ __('Confirm Password') }}</flux:label>
            <flux:input wire:model="password_confirmation" type="password" placeholder="Re-enter password"
                autocomplete="new-password" viewable />
            <flux:error name="password_confirmation" />
        </flux:field>

        <flux:description class="mb-6">
            Password must be at least 8 characters.
        </flux:description>

        <div class="flex items-center gap-3">
            <flux:button type="button" wire:click="generatePassword" variant="ghost" size="sm" icon="key">
                {{ __('Generate Password') }}
            </flux:button>

            <flux:button type="submit" variant="primary" size="sm" wire:target="updatePassword" icon="check">
                {{ __('Update Password') }}



            </flux:button>
        </div>




        <flux:callout variant="info" icon="shield-check" class="mt-5">
            <flux:callout.heading>{{ __('Secure Your Account') }}</flux:callout.heading>
            <flux:callout.text>{{ __('Ensure your account is using a long, random password to stay secure.') }}
            </flux:callout.text>
        </flux:callout>




    </form>

</flux:card>
