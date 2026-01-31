<?php

namespace Database\Seeders;

use App\Models\TaskManagement\TaskCompletorCategory;
use App\Models\TaskManagement\TaskCompletorSubCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskCompletorSubCategorySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     * Requires TaskCompletorCategorySeeder to be run first.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        TaskCompletorSubCategory::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $categorySubs = [
            'বিভাগীয় দায়িত্বশীল' => [
                'বিভাগীয় সম্পাদক',
                'সহকারী সম্পাদক',
            ],
        ];

        $categoriesByTitle = TaskCompletorCategory::all()->keyBy(fn ($c) => trim($c->title));
        $created = 0;

        foreach ($categorySubs as $categoryTitle => $subTitles) {
            $category = $categoriesByTitle->get($categoryTitle);
            if (!$category) continue;

            foreach ($subTitles as $subTitle) {
                TaskCompletorSubCategory::create([
                    'task_completor_category_id' => $category->id,
                    'title' => $subTitle,
                    'slug' => uniqid(),
                    'status' => 1,
                    'creator' => 0,
                ]);
                $created++;
            }
        }

        $this->command->info("Task completor sub categories seeding completed! {$created} records created.");
    }
}
