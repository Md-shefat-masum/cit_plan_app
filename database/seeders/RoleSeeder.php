<?php

namespace Database\Seeders;

use App\Models\UserManagement\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks to allow truncating tables with foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Force truncate user_roles table
        UserRole::truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $roles = [
            [
                'title' => 'Planning Department',
                'slug' => 'planning_department',
            ],
            [
                'title' => 'Department',
                'slug' => 'department',
            ],
            [
                'title' => 'Department Assistant',
                'slug' => 'department_assistant',
            ],
            [
                'title' => 'Viewer',
                'slug' => 'viewer',
            ],
        ];

        foreach ($roles as $role) {
            // Check if role already exists by slug
            $existingRole = UserRole::where('slug', $role['slug'])->first();
            
            if (!$existingRole) {
                UserRole::create([
                    'title' => $role['title'],
                    'slug' => $role['slug'],
                    'status' => 1,
                    'creator' => 0,
                ]);
                $this->command->info("  ✓ Created role: {$role['title']} ({$role['slug']})");
            } else {
                $this->command->warn("  ⊘ Role already exists: {$role['title']} ({$role['slug']})");
            }
        }

        $this->command->info('Role seeding completed!');
    }
}
