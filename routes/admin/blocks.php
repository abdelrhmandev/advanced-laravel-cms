<?php
use App\Livewire\Admin\CMS\Blocks\BlockBuilder;
use App\Livewire\Admin\CMS\Blocks\BlockEditor;
use App\Livewire\Admin\CMS\Blocks\BlockManager;
use Illuminate\Support\Facades\Route;
Route::prefix('blocks')
    ->name('blocks.')
    ->group(function () {
        Route::get('/', BlockManager::class)->name('index');
        Route::get('/{block}/fields', BlockEditor::class)->name('fields');
        Route::get('/pages/{page}/builder', BlockBuilder::class)->name('pages.builde');
    });

?>
