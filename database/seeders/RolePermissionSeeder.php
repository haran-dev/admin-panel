<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'read dashboard', 
            'create categories', 
            'read categories', 
            'update categories', 
            'delete categories',
            'create roles',
            'read roles',
            'update roles',
            'delete roles',
            'create user',
            'read user',
            'update user',
            'delete user',

        ];
        
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());


    }
}
