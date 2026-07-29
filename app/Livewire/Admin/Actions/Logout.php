<?php

namespace App\Livewire\Admin\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Logout
{
    /**
     * Log the current user out of the application.
     */
    public function __invoke(): void
    {
        // Set a flash message before invalidating the session
        // session()->flash('status', 'You have been successfully logged out.');


        Auth::guard('admin')->logout();
        session()->invalidate();
        session()->regenerateToken();
    }
}
