<?php
use App\Concerns\Admin\ProfileValidationRules;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

new #[Title('Profile settings')] class extends Component {
    use ProfileValidationRules;

    use WithFileUploads;
    public string $name = '';
    public string $email = '';

    public ?string $mobile = null;
    public $avatar = null;

    public function mount(): void
    {
        $this->name = Auth::guard('admin')->user()->name;
        $this->email = Auth::guard('admin')->user()->email;
        $this->mobile = Auth::guard('admin')->user()->mobile;
        $this->avatar = Auth::guard('admin')->user()->avatar;
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::guard('admin')->user();
        $validated = $this->validate($this->profileRules($user->id));

        if ($this->avatar instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
            $path = $this->avatar->store('uploads/users/avatars', 'public');
            if ($user->avatar) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = $path;
        } else {
            unset($validated['avatar']);
        }

        $user->fill($validated);
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();
        Flux::toast(variant: 'success', text: __('Profile updated.'));
    }

    public function resendVerificationNotification(): void
    {
        $user = Auth::guard('admin')->user();
        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('admin.dashboard', absolute: false));
            return;
        }
        $user->sendEmailVerificationNotification();
        Session::flash('status', 'verification-link-sent');
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        $user = Auth::guard('admin')->user();
        return $user instanceof MustVerifyEmail && !$user->hasVerifiedEmail();
    }
}; ?>

<section class="w-full space-y-6">
    <div>
        <flux:heading size="lg">{{ __('Profile Information') }}</flux:heading>
        <flux:subheading>{{ __('Update your name , email address , mobile , and avatar') }}</flux:subheading>
    </div>

    <form wire:submit="updateProfileInformation" enctype="multipart/form-data" class="space-y-6">
        <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" />

        <div>
            <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="username" />

            @if ($this->hasUnverifiedEmail)
                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                    {{ __('Your email address is unverified.') }}
                    <flux:button wire:click.prevent="resendVerificationNotification" variant="subtle" size="sm"
                        class="ml-1">
                        {{ __('Resend verification email') }}
                    </flux:button>
                </p>

                @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 text-sm font-medium text-green-600 dark:text-green-400">
                        {{ __('A new verification link has been sent to your email address.') }}
                    </p>
                @endif
            @endif
        </div>



        <flux:input wire:model="mobile" :label="__('Mobile')" type="text" autofocus autocomplete="mobile" />



        <div class="space-y-4">
            <flux:label>{{ __('Avatar') }}</flux:label>

            <div class="flex items-center gap-6">
                <div wire:key="avatar-preview-{{ $avatar instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile ? $avatar->hashName() : 'static' }}"
                    class="size-16 min-w-[64px] rounded-lg bg-zinc-100 border border-zinc-200 flex items-center justify-center overflow-hidden relative">

                    @if ($avatar instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)
                        @if (in_array(strtolower($avatar->getClientOriginalExtension()), ['png', 'jpg', 'jpeg', 'webp', 'gif']))
                            <img src="{{ $avatar->temporaryUrl() }}" class="size-full object-cover">
                        @else
                            <span class="text-[10px] text-zinc-400">Invalid</span>
                        @endif
                    @elseif ($avatar)
                        <img src="{{ asset('storage/' . $avatar) }}" class="size-full object-cover">
                    @else
                        <span class="text-zinc-500 font-semibold text-lg">
                            {{ Auth::guard('admin')->user()->initials() }}
                        </span>
                    @endif
                </div>

                <div class="flex flex-col gap-2">
                    <flux:input type="file" wire:model="avatar" accept="image/*" />
                    <flux:error name="avatar" />
                    <p class="text-xs text-zinc-500">PNG, JPG, WEBP (Max 2MB)</p>
                </div>

                <div wire:loading.delay wire:target="avatar" class="text-xs text-gray-500">
                    uploading ...
                </div>


            </div>
        </div>





        <flux:button variant="primary" type="submit">
            {{ __('Save') }}
        </flux:button>
    </form>
</section>
