<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AllMenuSeeder extends Seeder
{
    public function run()
    {
        DB::table('all_menu')->truncate();

        DB::table('all_menu')->insert([
            [
                'title' => 'Dashboard',
                'link' => '/dashboard',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Categories',
                'link' => '/categories/view',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'User Roles',
                'link' => '/roles/view',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'User Managements',
                'link' => '/user-management/view',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'SMS Sending',
                'link' => '/sms/view',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Email Sending',
                'link' => '/email/view',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Settings',
                'link' => '/settings/view',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
