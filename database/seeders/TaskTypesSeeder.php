<?php

namespace Database\Seeders;

use App\Models\TaskManagement\TaskType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskTypesSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        TaskType::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $titles = [
            'তাত্ত্বিক',
            'নিয়মিত কাজ',
            'সময়সীমাভিত্তিক',
            'সংখ্যাগত',
            'উদ্দেশ্যভিত্তিক',
        ];

        foreach ($titles as $title) {
            TaskType::create([
                'title' => $title,
                'slug' => uniqid(),
                'status' => 1,
                'creator' => 0,
            ]);
        }

        $this->command->info('Task types seeding completed!');
    }
}
