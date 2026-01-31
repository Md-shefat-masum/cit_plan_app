<?php

namespace App\Http\Actions\TaskManagement\TaskPlan;

class TaskPlanStoreAction
{
    /**
     * Generate unique serial: department_id + year + (100001, 100101, 100201...).
     * Each serial has 100 gap. If exists, generate another.
     */
    public static function generateSi(int $departmentId): int
    {
        $model = new \App\Models\TaskManagement\TaskPlan;
        $year = (int) date('Y');
        $prefix = (int) ($departmentId . $year);
        $baseSi = $prefix * 1000000 + 100001;
        $maxSi = $model::where('si', '>=', $baseSi)
            ->where('si', '<=', $prefix * 1000000 + 999999)
            ->max('si');

        $nextSeq = $maxSi ? (($maxSi % 1000000) + 100) : 100001;
        $si = $prefix * 1000000 + $nextSeq;

        while ($model::where('si', $si)->exists()) {
            $nextSeq += 100;
            $si = $prefix * 1000000 + $nextSeq;
        }

        return $si;
    }

    public static function execute($model, $table_name, array $data)
    {
        $departmentId = (int) ($data['department_id'] ?? 0);
        $departmentId = $departmentId > 0 ? $departmentId : 1;
        $data['si'] = self::generateSi($departmentId);

        $data['slug'] = $data['slug'] ?? uniqid();
        $data['creator'] = $data['creator'] ?? (auth('api')->id() ?? 0);
        $data['status'] = $data['status'] ?? 1;

        return $model::create($data);
    }
}
