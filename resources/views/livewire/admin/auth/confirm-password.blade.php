<div>
    <div class="flex flex-col gap-6">
        <x-admin.auth-header :title="__('Confirm password')" :description="__('This is a secure area of the application. Please confirm your password before continuing.')" />
          <x-auth-session-status class="text-center" :status="session('status')" />
         <x-passkey-verify
            options-route="passkey.confirm-options"
            submit-route="passkey.confirm"
            :label="__('Confirm with passkey')"
            :loading-label="__('Confirming...')"
            :separator="__('Or confirm with password')"
        />
        <form wire:submit="confirmPassword" class="flex flex-col gap-6">
            <flux:input wire:model="password" :label="__('Password')" type="password" required
                autocomplete="current-password" :placeholder="__('Password')" viewable />

            @error('password')
                <flux:error>{{ $message }}</flux:error>
            @enderror

            <flux:button variant="primary" type="submit" class="w-full">
                {{ __('Confirm') }}
            </flux:button>
        </form>
    </div>
</div>

