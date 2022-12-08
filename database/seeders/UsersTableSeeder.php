<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->truncate();
        DB::table('users')->insert([
            'first_name' => 'Usama',
            'last_name' => 'Aslam',
            'email' => 'programmer@brand786.com',
            'email_verified_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'password' => \Hash::make('Brand1234#'),
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        
        DB::table('users')->insert([
            'first_name' => 'sheerazabbas',
            'last_name' => 'Testing',
            'email' => 'sheerazabbas@brand786.com',
            'email_verified_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'password' => \Hash::make('Brand1234#'),
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

    }
}
