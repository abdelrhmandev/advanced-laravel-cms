<?php
namespace App\Livewire\Admin\CMS\Users;
use App\Http\Requests\Admin\CMS\UserRequest;
use App\Models\User;
use App\Traits\Upload;
use Flux\Flux;
use App\Services\Users\UserService;
use App\Traits\AuthorizesActions;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\Permission\Models\Role;

#[Layout('layouts.admin.app')]
class UserForm extends Component
{
    use WithFileUploads, Upload , AuthorizesActions;

    public array $permittedActions = ['create', 'edit'];
    public User $user;

    public ?string $UploadDir = 'users/avatars';
    public string $module = 'Users';
    public string $route = 'users';
    public string $permissionPrefix = 'users';

    public ?string $pagetitle = null;
    public ?int $Id = null;

    public ?string $existingAvatar = null;
    public array $selectedRoles = [];
    public $roles = [];
    public ?string $name = '';
    public ?string $mobile = null;
    public ?string $email = '';
    public string|\Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null $avatar = null;
    public ?string $password = null;
    public ?bool $is_active = true;
    public ?int $deletingId = null;

    protected function rules()
    {
        return (new UserRequest())->rules($this->Id);
    }

    protected function messages()
    {
        return (new UserRequest())->messages();
    }
    public function updatedAvatar(): void
    {
        $this->validateOnly('avatar');
    }
    public function generatePassword()
    {
        $randomPassword = Str::random(12);

        $this->password = $randomPassword;
        Flux::toast(variant: 'success', heading: 'Success!', text: 'Password Generated : ' . $randomPassword);
    }

    ////////////////////

    public function save(UserService $service)
    {
        $this->validate();

        $avatarPath = $service->save(
            id: $this->Id,
            data: [
                'name' => $this->name,
                'mobile' => $this->mobile ?? null,
                'email' => $this->email,
                'password' => $this->password,
                'is_active' => $this->is_active,
                'selectedRoles' => $this->selectedRoles,
            ],
            avatar: $this->avatar,
            existingAvatar: $this->existingAvatar,
            uploadDir: $this->UploadDir,
        );

        if ($avatarPath) {
            $this->existingAvatar = $avatarPath;
        }

        $this->reset('avatar');

        if (!$this->Id) {
            $this->reset(['name', 'email', 'password', 'mobile', 'avatar', 'is_active', 'selectedRoles']);
        }

        Flux::toast(variant: 'success', heading: 'Success!', text: $this->Id ? ucfirst(Str::singular($this->module)).' updated successfully' : ucfirst(Str::singular($this->module)).' created successfully');



        $this->redirect(
            $this->Id
                ? route('admin.' . $this->route . '.edit', $this->Id)
                : route('admin.' . $this->route . '.index'),
            navigate: true
        );

    }

    //////////
    public function mount(?User $user = null): void
    {
        $this->roles = Role::all();
        $this->pagetitle = $this->module;
        $this->module = $this->module;

        if ($user?->exists) {
            $this->user = $user;
            $this->Id = $user->id;
            $this->name = $user->name;
            $this->mobile = $user->mobile;
            $this->existingAvatar = $user->avatar;
            $this->email = $user->email;
            $this->is_active = $user->is_active;
            $this->selectedRoles = $user->roles->pluck('id')->toArray();

            $this->pagetitle = "{$this->module} | Edit {$user->name}";
            $this->authorizeAction('edit');
        } else {
            $this->user = new User();
            $this->pagetitle .= ' | Add';
            $this->authorizeAction('create');
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

        if ($user && $user->avatar) {
            $this->deleteFile($user->avatar);
        }

        $user->delete();

        \Illuminate\Support\Facades\Cache::forget('admin_dashboard_data');

        Flux::toast(variant: 'success', heading: 'Success!', text: ucfirst(Str::singular($this->module)).' deleted successfully.');
        Flux::modal('confirm-delete')->close();
        $this->redirect(route('admin.' . $this->route . '.index'), navigate: true);

        $this->deletingId = null;
    }

    public function rendering($view, $data): void
    {
        $view->layoutData([
            'title' => $this->pagetitle,
            'module' => $this->module,
            'route' => $this->route,
            'permissionPrefix' => $this->permissionPrefix,
        ]);
    }
    public function render()
    {
        return view('livewire.admin.cms.users.form');
    }
}
