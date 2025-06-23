<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // ❗ Clear cache of spatie
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ❗ Clean tables to avoid duplicates
        DB::table('role_has_permissions')->truncate();
        DB::table('permissions')->truncate();
        DB::table('roles')->truncate();

        // ✅ Your permission list
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
            'create sms',
            'read sms',
            'update sms',
            'delete sms',
            'create email',
            'read email',
            'update email',
            'delete email',
            'create settings',
            'read settings',
            'update settings',
            'delete settings',
        ];

        foreach ($permissions as $perm) {
            Permission::create(['name' => $perm]);
        }

        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());
    }
}
