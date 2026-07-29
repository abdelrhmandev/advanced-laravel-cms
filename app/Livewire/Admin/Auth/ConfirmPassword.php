<?php

namespace App\Livewire\Admin\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
#[Layout('layouts.admin.auth.simple')]
#[Title('ConfirmPassword')]
class ConfirmPassword extends Component
{
    public string $password = '';

    public function confirmPassword(): void
    {
        if (! Auth::guard('admin')->validate([
            'email'    => Auth::guard('admin')->user()->email,
            'password' => $this->password,
        ])) {
            $this->addError('password', __('auth.password'));
            return;
        }

        session()->put('auth.password_confirmed_at', time());

        $this->redirectIntended(default: route('admin.security'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.auth.confirm-password');

    }
}
