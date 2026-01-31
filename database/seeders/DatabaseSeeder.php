<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // RoleSeeder::class,
            // UserSeeder::class,
            // DepartmentSeeder::class,
            // DepartmentSectionSeeder::class,
            // DepartmentSubSectionSeeder::class,
            // DofaSeeder::class,
            // TaskTypesSeeder::class,
            // TaskStatusSeeder::class,

            // TimeDurationSeeder::class,
            // TaskCompletorCategorySeeder::class,
            // TaskCompletorSubCategorySeeder::class,

            // PlanSeeder::class,
            // TaskSubPlanSeeder::class,
            // AppModuleRegisterSeeder::class,

            // TaskSubPlanSeeder::class,
            

        ]);
    }
}
