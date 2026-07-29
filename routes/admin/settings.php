<?php
use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\CMS\Settings\SettingManager;
Route::get('/settings', SettingManager::class)->name('settings.index');
