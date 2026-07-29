<?php
namespace App\Livewire\Admin\CMS\Permissions;
use App\Traits\AuthorizesActions;
use Flux\Flux;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use RegexIterator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
#[Title('Permissions')]
#[Layout('layouts.admin.app')]
class PermissionManager extends Component
{
    public $permissions = [];

    public $generatedPermissions = [];
    public $componentsList = [];
    use AuthorizesActions;


    public $moduleName = 'permissions';

    protected $basePath = 'Livewire/Admin/CMS';
    public array $permittedActions = ['index'];



    /**
     * List folder names you want to skip entirely.
     * Example: 'Shared', 'Components', 'Partials'
     */
    protected $ignoredFolders = [];

    public function index()
    {
        $this->authorizeAction('index');
        $this->refreshData();
    }


    public function mount()
    {
        return $this->index();
    }



    public function refreshData()
    {
        $this->loadExistingPermissions();
        $this->scanComponents();
        $this->generatePermissions();
    }

    public function loadExistingPermissions()
    {
        $all = Permission::pluck('name')->toArray();
        $this->permissions = $all;

        $grouped = [];
        foreach ($all as $name) {
            $parts = explode('.', $name);
            $moduleName = count($parts) > 1 ? Str::title($parts[0]) : 'Other';
            $grouped[$moduleName][] = $name;
        }
    }

    protected function scanComponents()
    {
        $base = app_path($this->basePath);
        if (!is_dir($base)) return;

        $result = [];
        $files = new RegexIterator(
            new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base)),
            '/^.+\.php$/i',
            RegexIterator::GET_MATCH
        );

        foreach ($files as $file) {
            $absolutePath = $file[0];

            // --- IGNORE LOGIC ---
            // Check if the path contains any of our ignored folder names
            $shouldIgnore = false;
            foreach ($this->ignoredFolders as $folder) {
                if (Str::contains($absolutePath, DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR)) {
                    $shouldIgnore = true;
                    break;
                }
            }
            if ($shouldIgnore) continue;
            // --------------------

            $class = (string) Str::of($absolutePath)
                ->after(app_path())
                ->prepend('App')
                ->replace(['/', '\\'], '\\')
                ->replace('.php', '');

            if (!class_exists($class)) continue;

            $reflect = new ReflectionClass($class);
            if ($reflect->isAbstract()) continue;

            $defaultProps = $reflect->getDefaultProperties();
            $actions = $defaultProps['permittedActions'] ?? [];

            if (empty($actions)) continue;

            $module = 'Global';
            if (preg_match('/\\\\CMS\\\\([^\\\\]+)/', $class, $matches)) {
                $module = $matches[1];
            }

            $result[$module] = array_unique(array_merge($result[$module] ?? [], $actions));
        }

        $this->componentsList = $result;
    }

    protected function generatePermissions()
    {
        $generated = [];
        foreach ($this->componentsList as $module => $actions) {
            $moduleSlug = Str::snake($module);
            foreach ($actions as $action) {
                $permName = "{$moduleSlug}." . strtolower(trim($action));
                if (!in_array($permName, $this->permissions)) {
                    $generated[$module][$permName] = $action;
                }
            }
        }
        $this->generatedPermissions = $generated;
    }

    public function syncAll()
    {
        $count = 0;
        $newPermissions = [];

        foreach ($this->generatedPermissions as $module => $perms) {
            foreach ($perms as $permName => $uiLabel) {
                $newPermissions[] = [
                    'name' => $permName,
                    'guard_name' => 'admin',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $count++;
            }
        }

        if ($count > 0) {
            Permission::insertOrIgnore($newPermissions);
            app(PermissionRegistrar::class)->forgetCachedPermissions();
        }

         Flux::toast(variant: 'success', heading: 'System Synced!', text: 'created ('.$count.') new module permissions.');


        $this->refreshData();
    }

    public function render()
    {
        return view('livewire.admin.cms.permissions.permission-manager');
    }
}
