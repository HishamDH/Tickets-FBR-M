<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Enums\PermissionEnum;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // إنشاء جميع الصلاحيات
        $permissions = PermissionEnum::getAllPermissions();
        
        foreach ($permissions as $permission => $description) {
            Permission::firstOrCreate(
                ['name' => $permission],
                ['description' => $description]
            );
        }

        // إنشاء الأدوار وتعيين الصلاحيات
        $rolePermissions = PermissionEnum::getRolePermissions();

        foreach ($rolePermissions as $roleName => $permissionNames) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            
            // تنظيف الصلاحيات الحالية وإعادة تعيينها
            $role->permissions()->detach();
            $role->givePermissionTo($permissionNames);
            
            $this->command->info("تم إنشاء دور '{$roleName}' مع " . count($permissionNames) . " صلاحية");
        }

        $this->command->info('تم إنشاء ' . count($permissions) . ' صلاحية و ' . count($rolePermissions) . ' أدوار بنجاح!');
    }
}
