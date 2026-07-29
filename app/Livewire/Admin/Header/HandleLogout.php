<?php

namespace App\Livewire\Admin\Header;

use App\Livewire\Admin\Actions\Logout;
use Livewire\Component;

class HandleLogout extends Component
{
    // The mount method runs automatically when the user visits the route
    public function mount(Logout $logout)
    {
        $logout();

        return redirect()->route('admin.login');
    }

    public function render()
    {
        // This component doesn't actually need to render a view
        // since it immediately redirects the user.
        return <<<'HTML'
            <div>Logging out...</div>
        HTML;
    }
}
