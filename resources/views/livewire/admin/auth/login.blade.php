<div>
    <div class="flex flex-col gap-6">
        <x-admin.auth-header
            :title="__('Log in to your account')"
            :description="__('Enter your email and password below to log in')"
        />
        <x-admin.auth-session-status class="text-center" :status="session('status')" />
        <x-admin.passkey-verify />
        <form wire:submit="login" class="flex flex-col gap-6">
            <flux:input
                wire:model="email"
                :label="__('Email address')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="email@example.com"
            />
            <div class="relative">
                <flux:input
                    wire:model="password"
                    :label="__('Password')"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('Password')"
                    viewable
                />
                @if (Route::has('admin.password.request'))
                    <flux:link class="absolute top-0 text-sm end-0" :href="route('admin.password.request')" wire:navigate>
                        {{ __('Forgot your password?') }}
                    </flux:link>
                @endif
            </div>
            <flux:checkbox wire:model="remember" :label="__('Remember me')" />

            <flux:button variant="primary" type="submit" class="w-full">
                {{ __('Log in') }}
            </flux:button>
        </form>
    </div>
</div>
