<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::create(['name' => 'SuperAdmin']);
        
        // Create permissions
        $permissions = [
            'create_user',
            'update_user',
            'delete_user'
        ];
        
        foreach ($permissions as $permissionName) {
            Permission::create(['name' => $permissionName]);
        }

        $role->syncPermissions($permissions);
    }
}
