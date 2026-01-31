<?php

namespace Database\Seeders;

use App\Models\TaskManagement\Department;
use App\Models\TaskManagement\TaskCompletorCategory;
use App\Models\TaskManagement\TaskCompletorSubCategory;
use App\Models\TaskManagement\TaskSubPlan;
use App\Models\TaskManagement\TimeDuration;
use App\Models\TaskManagement\TimeSubDuration;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskSubPlanSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     * Requires: PlanSeeder, TimeDurationSeeder, TaskCompletorCategorySeeder, TaskCompletorSubCategorySeeder, DepartmentSeeder
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        TaskSubPlan::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $jsonPath = database_path('seeders/data/task_sub_plan_seed.json');
        if (!file_exists($jsonPath)) {
            $this->command->error('task_sub_plan_seed.json not found');
            return;
        }

        $items = json_decode(file_get_contents($jsonPath), true);
        if (!$items) {
            $this->command->error('Invalid JSON in task_sub_plan_seed.json');
            return;
        }

        $durationsByTitle = TimeDuration::all()->keyBy(fn ($d) => trim($d->title));
        $subDurations = TimeSubDuration::with('timeDuration')->get();
        $categoriesByTitle = TaskCompletorCategory::all()->keyBy(fn ($c) => trim($c->title));
        $subCategoriesByTitle = TaskCompletorSubCategory::all()->keyBy(fn ($c) => trim($c->title));
        $departmentsByTitle = Department::all()->keyBy(fn ($d) => trim($d->title));

        $created = 0;

        foreach ($items as $item) {
            $taskPlanId = (int) ($item['task_plan_id'] ?? 0);
            $description = trim((string) ($item['descriptin'] ?? $item['description'] ?? ''));
            $durationTitle = trim((string) ($item['time_duration_id'] ?? ''));
            $subDurationTitle = trim((string) ($item['time_sub_duration_id'] ?? ''));
            $categoryTitle = trim((string) ($item['task_completor_catagory_id'] ?? $item['task_completor_category_id'] ?? ''));
            $subCategoryTitle = trim((string) ($item['task_completor_sub_category_id'] ?? ''));
            $umbrellaDeptTitle = trim((string) ($item['umbrella_department_id'] ?? ''));

            $timeDurationId = null;
            $timeSubDurationId = null;
            if ($durationTitle) {
                $duration = $durationsByTitle->get($durationTitle);
                if ($duration) {
                    $timeDurationId = $duration->id;
                    if ($subDurationTitle) {
                        $sub = $subDurations->firstWhere(
                            fn ($s) => $s->time_duration_id === $duration->id && trim($s->title) === $subDurationTitle
                        );
                        $timeSubDurationId = $sub?->id;
                    }
                }
            }

            $taskCompletorCategoryId = $categoryTitle ? ($categoriesByTitle->get($categoryTitle)?->id) : null;
            $taskCompletorSubCategoryId = $subCategoryTitle ? ($subCategoriesByTitle->get($subCategoryTitle)?->id) : null;
            $umbrellaDepartmentId = $umbrellaDeptTitle ? ($departmentsByTitle->get($umbrellaDeptTitle)?->id) : null;

            TaskSubPlan::create([
                'task_plan_id' => $taskPlanId ?: 1,
                'description' => $description,
                'time_duration_id' => $timeDurationId,
                'time_sub_duration_id' => $timeSubDurationId,
                'task_completor_category_id' => $taskCompletorCategoryId,
                'task_completor_sub_category_id' => $taskCompletorSubCategoryId,
                'umbrella_department_id' => $umbrellaDepartmentId,
                'slug' => uniqid(),
                'status' => 1,
                'creator' => 0,
            ]);
            $created++;
        }

        $this->command->info("Task sub plans seeding completed! {$created} records created.");
    }
}
