<?php

namespace Database\Seeders;

use App\Models\TaskManagement\Department;
use App\Models\TaskManagement\DepartmentSection;
use App\Models\TaskManagement\DepartmentSubSection;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DepartmentSubSection::truncate();
        DepartmentSection::truncate();
        Department::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $titles = ['IT', 'Planning', 'Social Media', 'Publication'];

        foreach ($titles as $title) {
            Department::create([
                'title' => $title,
                'slug' => uniqid(),
                'status' => 1,
                'creator' => 0,
            ]);
        }

        $this->command->info('Department seeding completed!');
    }
}
