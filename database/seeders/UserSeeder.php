<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User 1: user_role_id = -1
        User::create([
            'user_role_id' => -1,
            'name' => 'user_1',
            'username' => 'user_1',
            'email' => 'user_1@example.com',
            'password' => Hash::make('password'),
            'status' => 1,
            'slug' => uniqid() . time(),
        ]);

        // User 2: user_role_id = 0, status = 0
        User::create([
            'user_role_id' => 0,
            'name' => 'user_2',
            'username' => 'user_2',
            'email' => 'user_2@example.com',
            'password' => Hash::make('password'),
            'status' => 0,
            'slug' => uniqid() . time(),
        ]);

        // User 3: user_role_id = 1
        User::create([
            'user_role_id' => 1,
            'name' => 'user_3',
            'username' => 'user_3',
            'email' => 'user_3@example.com',
            'password' => Hash::make('password'),
            'status' => 1,
            'slug' => uniqid() . time(),
        ]);
    }
}
