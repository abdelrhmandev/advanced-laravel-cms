<?php

namespace App\Livewire\Admin\CMS\Dashboard;

use App\Models\{User, Block};
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Role;

#[Title('Dashboard')]
#[Layout('layouts.admin.app')]
class DashboardManagement extends Component
{
    const CACHE_KEY = 'admin_dashboard_data';
    const CACHE_TTL = 15; // minutes


     public array $permittedActions = ['getDashboardData'];

    private function defaultData(bool $isSkeleton = true): array
    {
        return [
            'is_skeleton' => $isSkeleton,
            'stats' => [
                'total_users' => 0,
                'blocks'      => 0,
            ],
            'userStatus' => [
                'active'   => 0,
                'inactive' => 0,
            ],
            'rolesNames' => [],
            'rolesCount' => [],
        ];
    }

    public function placeholder(): \Illuminate\View\View
    {
        return view('livewire.admin.cms.dashboard.dashboard', $this->defaultData(true));
    }

    public function mount(): void
    {
        $data = $this->getDashboardData();

        $this->dispatch('initChart', [
            'counts'     => $data['rolesCount'],
            'names'      => $data['rolesNames'],
            'userStatus' => $data['userStatus'],
        ]);
    }

    public function render(): \Illuminate\View\View
    {
        return view(
            'livewire.admin.cms.dashboard.dashboard',
            array_merge($this->getDashboardData(), ['is_skeleton' => false])
        );
    }

    public function refresh(): void
    {
        static::clearCache();

        $data = $this->getDashboardData();

        $this->dispatch('initChart', [
            'counts'     => $data['rolesCount'],
            'names'      => $data['rolesNames'],
            'userStatus' => $data['userStatus'],
        ]);
    }

    private function getDashboardData(): array
    {
        return Cache::remember(self::CACHE_KEY, now()->addMinutes(self::CACHE_TTL), function () {
            $roles = Role::withCount('users')->get();

            return [
                'rolesNames' => $roles->pluck('name')->toArray(),
                'rolesCount' => $roles->pluck('users_count')->toArray(),
                'userStatus' => [
                    'active'   => User::where('is_active', 1)->count(),
                    'inactive' => User::where('is_active', 0)->count(),
                ],
                'stats' => [
                    'total_users' => User::count(),
                    'blocks'      => Block::count(),
                ],
            ];
        });
    }

    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
