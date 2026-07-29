<?php
use App\Livewire\Admin\Auth\ConfirmPassword;
use App\Livewire\Admin\Auth\ForgotPassword;
use App\Livewire\Admin\Auth\Login;
use App\Livewire\Admin\Auth\ResetPassword;
use App\Livewire\Admin\CMS\Contacts\ContactManager;
use App\Livewire\Admin\CMS\Dashboard\DashboardManagement;
use App\Livewire\Admin\CMS\Permissions\PermissionManager;
use App\Livewire\Admin\CMS\Roles\RoleManager;
use App\Livewire\Admin\CMS\ActivityLogs\Index;
use App\Livewire\Admin\Header\HandleLogout;
use Illuminate\Support\Facades\Route;

Route::prefix('control-panel')
    ->name('admin.')
    ->group(function () {
        Route::middleware('guest:admin')->group(function () {
            Route::get('login', Login::class)->name('login');
            Route::get('/forgot-password', ForgotPassword::class)->name('password.request');
            Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset');
        });

        Route::middleware('admin')->group(function () {
            require __DIR__ . '/admin/pages.php';
            require __DIR__ . '/admin/pages.block.php';
            require __DIR__ . '/admin/menus.php';
            require __DIR__ . '/admin/blocks.php';
            require __DIR__ . '/admin/users.php';
            require __DIR__ . '/admin/settings.php';

            Route::get('/contacts', ContactManager::class)->name('contacts.index');

            Route::get('/activitylogs', Index::class)->name('activitylogs.index');

            Route::get('/', fn() => redirect()->route('admin.dashboard'));
            Route::get('/dashboard', DashboardManagement::class)->name('dashboard');

            Route::get('/permissions', PermissionManager::class)->name('permissions');
            Route::get('/roles', RoleManager::class)->name('roles');

            Route::post('logout', HandleLogout::class)->name('logout');

            Route::view('/profile', 'admin.profile')->name('profile');

            Route::get('/security', fn() => view('admin.security'))->name('security')->middleware('admin.password.confirm');

            Route::view('/appearance', 'admin.appearance')->name('appearance');

            Route::get('/confirm-password', ConfirmPassword::class)->name('password.confirm');
        });
    });
?>
