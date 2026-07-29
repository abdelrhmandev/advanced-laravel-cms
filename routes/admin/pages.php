<?php
use App\Livewire\Admin\CMS\Pages\PageList;
use App\Livewire\Admin\CMS\Pages\PageForm;
use Illuminate\Support\Facades\Route;
            Route::prefix('pages')
                ->name('pages.')
                ->group(function () {
                    Route::get('/', PageList::class)->name('index');
                    Route::get('/create', PageForm::class)->name('create');
                    Route::get('/edit/{page}', PageForm::class)->name('edit');
                });

?>
