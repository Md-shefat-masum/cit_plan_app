<?php

namespace Database\Seeders;

use App\Models\TaskManagement\TaskCompletorCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskCompletorCategorySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        TaskCompletorCategory::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $titles = [
            'বিভাগীয় দায়িত্বশীল',
            'বিভাগীয় সদস্য',
            'বিভাগীয় স্টাফ',
            'শাখা',
            'অন্য বিভাগ',
        ];

        foreach ($titles as $title) {
            TaskCompletorCategory::create([
                'title' => $title,
                'slug' => uniqid(),
                'status' => 1,
                'creator' => 0,
            ]);
        }

        $this->command->info('Task completor categories seeding completed!');
    }
}
