<?php
namespace App\Livewire\Admin\CMS\ActivityLogs;
use App\Models\User;
use App\Traits\AuthorizesActions;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;
#[Layout('layouts.admin.app')]
#[Title('Activity Logs')]
class Index extends Component
{
    use WithPagination, AuthorizesActions;
    protected $paginationTheme = 'tailwind';
    public string $search = '';
    public array $events = [];
    public ?int $user = null;

    public ?Activity $selectedLog = null;
    public bool $showModal = false;

    public array $permittedActions = ['index'];

    public function mount()
    {
        $this->authorizeAction('index');
    }


    public function updatingSearch() { $this->resetPage(); }
    public function updatingEvents() { $this->resetPage(); }
    public function updatingUser() { $this->resetPage(); }


    public function show(int $id): void
    {
        $this->selectedLog = Activity::with('causer')->findOrFail($id);
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->reset(['selectedLog', 'showModal']);
    }


    public function getLogsProperty()
    {
        $events = array_filter($this->events);

        return Activity::query()
            ->with('causer:id,name')
            ->when($this->search, fn ($q) =>
                $q->where('description', 'like', "%{$this->search}%")
            )
            ->when($events, fn ($q) =>
                $q->whereIn('event', $events)
            )
            ->when($this->user, fn ($q) =>
                $q->where('causer_id', $this->user)
            )
            ->latest()
            ->paginate(10);
    }

    public function getUsersProperty()
    {
        return User::select('id', 'name')
            ->orderBy('name')
            ->limit(50)
            ->get();
    }


    public function render()
    {
        return view('livewire.admin.cms.activity_logs.index', [
            'logs' => $this->logs,
            'users' => $this->users,
        ]);
    }
}
