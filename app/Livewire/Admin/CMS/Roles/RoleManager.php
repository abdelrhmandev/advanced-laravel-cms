<?php
namespace App\Livewire\Admin\CMS\Roles;
use App\Traits\AuthorizesActions;
use Flux\Flux;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
#[Layout('layouts.admin.app')]
#[Title('Roles')]
class RoleManager extends Component
{
    public $permissions;
    public array $permittedActions = ['create', 'edit', 'delete', 'index'];
    public $name;
    public $selectedRoleId = null;
    public $selectedPermissions = [];
    public $roleIdBeingDeleted = null;
    public $roleNameBeingDeleted = null;
    use WithPagination;

    use AuthorizesActions;
    public $moduleName = 'roles';

    private function getFinalPermissions(): array
    {
        $permissions = collect($this->selectedPermissions);

        $autoIndexes = $permissions;

        return Permission::query()
            ->where('guard_name', 'admin')
            ->whereIn('name', $permissions->merge($autoIndexes)->unique()->values())
            ->pluck('name')
            ->toArray();
    }

    protected function rules()
    {
        return [
            // If editing, ignore the current ID in the unique check
            'name' => ['required', 'min:3', 'unique:roles,name,' . $this->selectedRoleId],
            'selectedPermissions' => 'required|array|min:1',
        ];
    }

    /**
     * Custom Error Messages
     */
    protected function messages()
    {
        return [
            'name.required' => 'The Role title required.',
            'name.unique' => 'This Role title already exists.',
            'selectedPermissions.required' => 'You must select at least one permission.',
            'selectedPermissions.min' => 'Please check at least one module action.',
        ];
    }

    public function index()
    {

        $this->authorizeAction('index');
        $this->loadData();
    }

    public function mount()
    {
        $this->index();
    }

    public function loadData()
    {
        $this->permissions = Permission::orderBy('name')->get();
    }

    public function createRole()
    {
        $this->validate();
        $role = Role::create([
            'name' => $this->name,
            'guard_name' => 'admin',
        ]);

        if (!empty($this->selectedPermissions)) {
            $role->syncPermissions($this->getFinalPermissions());
        }
        activity()->performedOn($role)
            ->causedBy(auth('admin')->user())
            ->event('created')
            ->withProperties(['permissions' => $this->getFinalPermissions()])
            ->log('Created role: ' . $this->name);

        $this->resetForm();
        $this->loadData();

        Flux::toast(variant: 'success', heading: 'Success!', text: 'Role created and permissions synced successfully.');
    }

    public function editRole($id)
    {
        $this->authorizeAction('edit');

        $role = Role::findOrFail($id);
        $this->selectedRoleId = $role->id;
        $this->name = $role->name;

        $allSaved = $role->permissions->pluck('name');
        $nonIndexPermissions = $allSaved->filter(fn($p) => !str_ends_with($p, '.index'));
        $modulesWithRealPerms = $nonIndexPermissions->map(fn($p) => explode('.', $p)[0])->unique();
        $indexes = $allSaved->filter(fn($p) => str_ends_with($p, '.index'))->filter(fn($p) => !$modulesWithRealPerms->contains(explode('.', $p)[0]));
        $this->selectedPermissions = $nonIndexPermissions->merge($indexes)->values()->toArray();
    }

    public function selectModulePermissions($module)
    {
        $modulePermissionNames = \Spatie\Permission\Models\Permission::where('name', 'like', $module . '%')
            ->pluck('name')
            ->toArray();

        $alreadySelected = array_intersect($modulePermissionNames, $this->selectedPermissions);

        if (count($alreadySelected) === count($modulePermissionNames)) {
            $this->selectedPermissions = array_diff($this->selectedPermissions, $modulePermissionNames);
        } else {
            $this->selectedPermissions = array_unique(array_merge($this->selectedPermissions, $modulePermissionNames));
        }
    }

    public function confirmDelete($id)
    {
        $role = Role::findOrFail($id);
        $this->roleIdBeingDeleted = $id;
        $this->roleNameBeingDeleted = Str::title(str_replace('-', ' ', $role->name));

        Flux::modal('confirm-delete-role')->show();
    }
    public function deleteRoleConfirmed($id)
    {
        $role = Role::find($id);

        if (!$role) {
            return;
        }

        if ($role->users()->count() > 0) {
            Flux::modal('confirm-delete-role')->close();
            Flux::toast(variant: 'danger', heading: 'Action Denied', text: 'Cannot delete ' . $role->name . '. Users are still assigned.');
            return;
        }

        if ($role->name === 'super-admin') {
            Flux::modal('confirm-delete-role')->close();
            return;
        }

        $role->permissions()->detach();
        $role->delete();

        activity()->performedOn($role)
            ->causedBy(auth('admin')->user()) // Explicitly record the admin user
            ->event('deleted')
            ->withProperties($role->toArray())
            ->log('Deleted role: ' . $role->toArray()['name']);


        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        if ($this->selectedRoleId == $id) {
            $this->reset(['selectedRoleId', 'name', 'selectedPermissions']);
        }

        Flux::modal('confirm-delete-role')->close();
        Flux::toast(variant: 'success', heading: 'Deleted!', text: 'Role deleted successfully.');

        $this->loadData();
    }

    public function updateRole()
    {
        $this->validate([
            'name' => 'required|min:3|unique:roles,name,' . $this->selectedRoleId,
            'selectedPermissions' => 'required|array|min:1',
        ]);

        $role = Role::findOrFail($this->selectedRoleId);

        if ($role->name === 'super-admin' && $this->name !== 'super-admin') {
            Flux::toast(variant: 'danger', heading: 'Error!', text: 'You cannot rename the Super Admin role.');

            return;
        }

        $role->update(['name' => $this->name]);

        // Sync using the helper to include hidden indexes
        $role->syncPermissions($this->getFinalPermissions());

        Flux::toast(variant: 'success', heading: 'Success!', text: 'Role and Permssions Updated successfully.');

        activity()->performedOn($role)
            ->causedBy(auth('admin')->user())
            ->event('updated')
            ->withProperties([
                // 'old_permissions' => $oldPermissions,
                'new_permissions' => $this->getFinalPermissions(),
            ])
            ->log('Updated role: ' . $role->name);

        // $this->resetForm();
        $this->loadData();
    }

    public function resetForm()
    {
        $this->reset(['name', 'selectedRoleId', 'selectedPermissions']);
        $this->resetErrorBag(); // Optional: clears validation messages
    }
    public function render()
    {
        return view('livewire.admin.cms.roles.role-manager', [
            'roles' => \Spatie\Permission\Models\Role::where('guard_name', 'admin')->orderBy('name', 'asc')->paginate(5),
        ]);
    }
}
