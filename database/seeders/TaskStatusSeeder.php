<?php

namespace Database\Seeders;

use App\Models\TaskManagement\TaskStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskStatusSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        TaskStatus::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $titles = [
            'বিগত',
            'নতুন',
            'পরিমার্জন',
            'বাতিল',
        ];

        foreach ($titles as $title) {
            TaskStatus::create([
                'title' => $title,
                'slug' => uniqid(),
                'status' => 1,
                'creator' => 0,
            ]);
        }

        $this->command->info('Task status seeding completed!');
    }
}
