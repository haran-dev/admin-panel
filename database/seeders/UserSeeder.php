<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'user_role' => 1,
            'email' => 'harananthonyrajah@gmail.com',
            'user_attempt' => 3,
            'user_otp' => 123456,
            'email_verified_at' => now(),
            'password' => Hash::make('123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $admin->assignRole('admin');
    }
}
