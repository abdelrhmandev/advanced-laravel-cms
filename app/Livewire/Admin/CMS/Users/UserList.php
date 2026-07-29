<?php

namespace App\Livewire\Admin\CMS\Users;

use App\Models\User;
use App\Traits\AuthorizesActions;
use Flux\Flux;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin.app')]
#[Title('Users')]
class UserList extends Component
{
    use AuthorizesActions, WithPagination;

    public string $pagetitle = 'Users';
    public string $module = 'users';
    public string $route = 'users';
    public string $permissionPrefix = 'users';

    public array $permittedActions = ['delete', 'index'];

    public string $search = '';
    public string $sortBy = 'created_at';
    public string $sortDir = 'desc';

    public string $activeFilter = '';

    public ?int $deletingId = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = 'asc';
        }
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        Flux::modal('confirm-delete')->show();
    }

    public function delete(): void
    {
        if (!$this->deletingId) {
            return;
        }

        $this->authorizeAction('delete');

        $user = User::find($this->deletingId);

        if (!$user) {
            $this->deletingId = null;
            return;
        }

        if ($user->id === auth('admin')->id()) {
            Flux::toast(variant: 'danger', heading: 'Error!', text: 'You cannot delete your own account.');
            $this->deletingId = null;
            return;
        }

        $user->delete();

        \Illuminate\Support\Facades\Cache::forget('admin_dashboard_data');

        Flux::toast(variant: 'success', heading: 'Success!', text: ucfirst(Str::singular($this->module)).' deleted successfully.');
        Flux::modal('confirm-delete')->close();

        $this->deletingId = null;
    }



    public function render()
    {
        $row = User::query()
            ->with('roles')
            ->when(
                $this->search,
                fn($q) => $q->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%");
                }),
            )
            ->when($this->activeFilter !== '', fn($q) => $q->where('is_active', (int) $this->activeFilter))
            ->orderBy($this->sortBy, $this->sortDir)
            ->cursorPaginate(10);

        return view('livewire.admin.cms.users.index', compact('row'));
    }

    public function rendering($view, $data)
    {
        $view->layoutData([
            'title' => $this->pagetitle,
            'module' => $this->module,
            'route' => $this->route,
            'permissionPrefix' => $this->permissionPrefix,
        ]);
    }
}
