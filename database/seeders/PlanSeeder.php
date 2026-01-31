<?php

namespace Database\Seeders;

use App\Http\Actions\TaskManagement\TaskPlan\TaskPlanStoreAction;
use App\Models\DofaManagement\Dofa;
use App\Models\TaskManagement\DepartmentSection;
use App\Models\TaskManagement\DepartmentSubSection;
use App\Models\TaskManagement\TaskPlan;
use App\Models\TaskManagement\TaskStatus;
use App\Models\TaskManagement\TaskType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     * Requires: DepartmentSeeder, DepartmentSectionSeeder, DepartmentSubSectionSeeder, DofaSeeder, TaskTypesSeeder, TaskStatusSeeder
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        TaskPlan::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $jsonPath = database_path('seeders/data/plan_seed.json');
        if (!file_exists($jsonPath)) {
            $this->command->error('plan_seed.json not found');
            return;
        }

        $items = json_decode(file_get_contents($jsonPath), true);
        if (!$items) {
            $this->command->error('Invalid JSON in plan_seed.json');
            return;
        }

        $sections = DepartmentSection::all()->keyBy(fn ($s) => trim(str_replace("\t", '', $s->title)));
        $dofas = Dofa::all()->keyBy(fn ($d) => trim($d->title));
        $taskTypes = TaskType::all()->keyBy(fn ($t) => trim($t->title));
        $taskStatuses = TaskStatus::all()->keyBy(fn ($s) => trim($s->title));

        $model = TaskPlan::class;
        $tableName = 'task_plans';
        $departmentId = 1;
        $created = 0;

        foreach ($items as $item) {
            $sectionTitle = trim(str_replace("\t", '', (string) ($item['department_section'] ?? '')));
            $subSectionTitle = trim((string) ($item['department_sub_section'] ?? ''));
            $dofaTitle = trim((string) ($item['dofa'] ?? ''));
            $taskTypeTitle = trim((string) ($item['task_type'] ?? ''));
            $taskStatusTitle = trim((string) ($item['task_status'] ?? ''));

            $section = $sections->get($sectionTitle);
            $departmentSectionId = $section?->id;
            $departmentSubSectionId = null;
            if ($section && $subSectionTitle) {
                $subSection = DepartmentSubSection::where('department_section_id', $section->id)
                    ->where('title', $subSectionTitle)
                    ->first();
                $departmentSubSectionId = $subSection?->id;
            }

            $dofa = $dofaTitle ? $dofas->get($dofaTitle) : null;
            $taskType = $taskTypeTitle ? $taskTypes->get($taskTypeTitle) : null;
            $taskStatus = $taskStatusTitle ? $taskStatuses->get($taskStatusTitle) : null;

            $qtyRaw = $item['qty'] ?? '';
            $qty = 0;
            if (is_numeric(str_replace(',', '', (string) $qtyRaw))) {
                $qty = (int) str_replace(',', '', (string) $qtyRaw);
            }

            $data = [
                'department_id' => $departmentId,
                'department_section_id' => $departmentSectionId,
                'department_sub_section_id' => $departmentSubSectionId,
                'dofa_id' => $dofa?->id,
                'description' => $item['description'] ?? null,
                'qty' => $qty,
                'task_type_id' => $taskType?->id,
                'task_status_id' => $taskStatus?->id,
                'status' => 1,
                'creator' => 0,
            ];

            TaskPlanStoreAction::execute($model, $tableName, $data);
            $created++;
        }

        $this->command->info("Plan seeding completed! {$created} records created.");
    }
}
