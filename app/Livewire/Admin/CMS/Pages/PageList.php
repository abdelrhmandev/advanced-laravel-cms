<?php
namespace App\Livewire\Admin\CMS\Pages;
use App\Models\Page;
use App\Traits\AuthorizesActions;
use App\Traits\Upload;
use Flux\Flux;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin.app')]
#[Title('Pages')]
class PageList extends Component
{
    use AuthorizesActions, WithPagination;
    public string $pagetitle = 'Pages';
    public string $module = 'pages';
    public string $route = 'pages';
    public string $permissionPrefix = 'pages';
    public string $statusFilter = 'all';

    use Upload;

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
        $page = Page::find($this->deletingId);

        if (!$page) {
            $this->deletingId = null;
            return;
        }
        $page->delete();
        \Illuminate\Support\Facades\Cache::forget('admin_dashboard_data');
        Flux::toast(variant: 'success', heading: 'Success!', text: ucfirst(Str::singular($this->module)) . ' deleted successfully.');
        Flux::modal('confirm-delete')->close();

        $this->deletingId = null;
    }

    public function setFilter(string $filter): void
    {
        $this->statusFilter = $filter;
        $this->resetPage();
    }
    #[Computed]
    public function counts(): array
    {
        return [
            'all' => Page::withoutTrashed()->count(),
            'trash' => Page::onlyTrashed()->count(),
        ];
    }

    public function restore(int $id): void
    {
        Page::onlyTrashed()->findOrFail($id)->restore();
        $filter = 'all';
        if(Page::onlyTrashed()->count()){
            $filter  = 'trash';
        }
        $this->setFilter($filter);

        Flux::toast(variant: 'success', heading: 'Success!', text: ucfirst(Str::singular($this->module)) . ' restored successfully');
    }

    public function forceDelete(int $id): void
    {
        $page = Page::onlyTrashed()->findOrFail($id);
        $this->deleteFile($page->image);
        $page->forceDelete();
        Flux::toast(variant: 'success', heading: 'Success!', text: ucfirst(Str::singular($this->module)) . ' permanently deleted');
    }

    public function render()
    {
        $this->authorizeAction('index');
        $row = Page::query()
            ->with('translate')

            ->when($this->statusFilter === 'trash', fn($q) => $q->onlyTrashed())
            ->when($this->statusFilter === 'all', fn($q) => $q->withoutTrashed())

            ->when($this->activeFilter !== '', fn($q) => $q->where('is_active', (int) $this->activeFilter))

            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate(10);
        return view('livewire.admin.cms.pages.index', compact('row'));
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
