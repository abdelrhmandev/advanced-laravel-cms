<?php
use App\Livewire\Admin\CMS\Menus\Builder;
use App\Livewire\Admin\CMS\Menus\Index;
use Illuminate\Support\Facades\Route;
// Menu Management
Route::prefix('menus')
    ->name('menus.')
    ->group(function () {
        Route::get('/', Index::class)->name('index');
        Route::get('{menu}/builder', Builder::class)->name('builder');
    });
?>
