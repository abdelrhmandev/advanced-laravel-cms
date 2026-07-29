<?php
namespace Database\Seeders;
use App\Models\User;
use Database\Seeders\SettingDatabaseSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([SettingDatabaseSeeder::class]);
        $this->call([PageDatabaseSeeder::class]);
        // $this->call([BlockDatabaseSeeder::class]);

        $user = User::create([
            'password' => Hash::make('12345678'),
            'email' => 'abdelrahman@domain.com',
            'name' => 'Abdelrahman El Monshed',
        ]);

        $role = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'admin']);
        $permissions = Permission::pluck('name')->all();
        $role->syncPermissions($permissions);
        $user->assignRole($role);

        $user = User::create([
            'password' => Hash::make('12345678'),
            'email' => 'writer@domain.com',
            'name' => 'Writer',
        ]);

        $role = Role::firstOrCreate(['name' => 'writer', 'guard_name' => 'admin'],['name' => 'editor', 'guard_name' => 'admin']);
    }
}
