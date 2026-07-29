<?php
namespace App\Traits;
use App\Livewire\Admin\CMS\Dashboard\DashboardManagement;

trait ClearsDashboardCache
{
    public static function bootClearsDashboardCache(): void
    {
        static::saved(fn() => DashboardManagement::clearCache());
        static::deleted(fn() => DashboardManagement::clearCache());
    }
}
