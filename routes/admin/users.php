<?php
use App\Livewire\Admin\CMS\Users\UserCreate;
use App\Livewire\Admin\CMS\Users\UserEdit;
use App\Livewire\Admin\CMS\Users\UserList;
use App\Livewire\Admin\CMS\Users\UserForm;
use Illuminate\Support\Facades\Route;

Route::prefix('users')
    ->name('users.')
    ->group(function () {
        Route::get('/', UserList::class)->name('index');
        Route::get('/create', UserForm::class)->name('create');
        Route::get('/edit/{user}', UserForm::class)->name('edit');
    }); ?>
