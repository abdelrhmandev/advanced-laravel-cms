<?php

namespace App\Livewire\Admin;

use Livewire\Component;

abstract class BaseComponent extends Component
{
    public ?string $pagetitle = null;

     private static ?string $_appName = null;

    private static function appName(): string
    {
        return static::$_appName ??= config('app.name', 'My Dashboard');
    }

    public function rendering($view, $data): void
    {
         $title = $this->pagetitle ?? static::appName();

        $view->layoutData(['pagetitle' => $title]);

         if ($this->pagetitle) {
            $this->dispatch('update-browser-title', title: $title);
        }
    }
}
