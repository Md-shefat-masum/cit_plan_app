<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks to allow truncating tables with foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Force truncate users table
        User::truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

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
            'username' => 'planning',
            'email' => 'planing@example.com',
            'password' => Hash::make('password'),
            'status' => 1,
            'slug' => uniqid() . time(),
        ]);

        // User 3: user_role_id = 1
        User::create([
            'user_role_id' => 2,
            'name' => 'user_4',
            'username' => 'department',
            'email' => 'department@example.com',
            'password' => Hash::make('password'),
            'status' => 1,
            'slug' => uniqid() . time(),
        ]);
        // User 3: user_role_id = 1
        User::create([
            'user_role_id' => 3,
            'name' => 'user_5',
            'username' => 'assistant',
            'email' => 'assistant@example.com',
            'password' => Hash::make('password'),
            'status' => 1,
            'slug' => uniqid() . time(),
        ]);
        // User 3: user_role_id = 1
        User::create([
            'user_role_id' => 4,
            'name' => 'user_6',
            'username' => 'viewer',
            'email' => 'viewer@example.com',
            'password' => Hash::make('password'),
            'status' => 1,
            'slug' => uniqid() . time(),
        ]);
    }
}
