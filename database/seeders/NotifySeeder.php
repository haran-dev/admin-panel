<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NotifySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sms_api_settings')->insert([
            'api_key'    => 'rAkrXoLB6RHWg9z8GVYN',
            'user_code'  => '29664',
            'sender_id'  => 'NotifyDEMO',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
