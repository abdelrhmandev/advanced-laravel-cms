<?php
use App\Livewire\Admin\CMS\Pages\PageBlockManager;
use Illuminate\Support\Facades\Route;
            Route::prefix('pages')
                ->name('pages.')
                ->group(function () {
                    Route::get('/{id}/blocks', PageBlockManager::class)->name('manage_blocks')->where('id', '[0-9]+');
                });
?>
