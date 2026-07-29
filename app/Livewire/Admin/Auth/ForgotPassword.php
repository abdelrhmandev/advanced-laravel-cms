<?php
namespace App\Livewire\Admin\Auth;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
#[Layout('layouts.admin.auth.simple')]
#[Title('ForgotPassword')]
class ForgotPassword extends Component
{
    public string $email = '';

    public function forgotPassword(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $status = Password::sendResetLink($this->only('email'));

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));
            return;
        }

        $this->reset('email');
        session()->flash('status', __($status));
    }

    public function render()
    {
        return view('livewire.admin.auth.forgot-password');
    }
}
