<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'John Admin',
            'email' => 'opaverifyadmin@mailforspam.com',
            'username' => 'admin',
            'password' => Hash::make('Admin'),
            'enabled_payment_methods' => '1,2,3',
            'role_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('users')->insert([
            'name' => 'John Moderator',
            'email' => 'opaverifymoderator@mailforspam.com',
            'username' => 'moderator',
            'password' => Hash::make('Moderator'),
            'enabled_payment_methods' => '1,2,3',
            'role_id' => 2,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('users')->insert([
            'name' => 'John User',
            'email' => 'opaverifyuser@mailforspam.com',
            'username' => 'user',
            'password' => Hash::make('User'),
            'enabled_payment_methods' => '1,2,3',
            'role_id' => 3,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
