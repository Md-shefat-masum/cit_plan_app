<?php

namespace Database\Seeders;

use App\Models\TaskManagement\TimeDuration;
use App\Models\TaskManagement\TimeSubDuration;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimeDurationSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     * Seeds time_durations and time_sub_durations.
     * Pattern: key = parent (time_duration title), value = child (time_sub_duration title).
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        TimeSubDuration::truncate();
        TimeDuration::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $durationTitles = [
            'দৈনিক',
            'সাপ্তাহিক',
            'দশক',
            'পাক্ষিক',
            'মাসিক',
            'দ্বিমাসিক',
            'ত্রৈমাসিক',
            'ষান্মাসিক',
            'বার্ষিক',
        ];

        foreach ($durationTitles as $title) {
            TimeDuration::create([
                'title' => $title,
                'slug' => uniqid(),
                'status' => 1,
                'creator' => 0,
            ]);
        }

        $jsonPath = database_path('seeders/data/time_duration_sub_seed.json');
        if (!file_exists($jsonPath)) {
            $this->command->info('Time durations seeded. time_duration_sub_seed.json not found, skipping sub durations.');
            return;
        }

        $rows = json_decode(file_get_contents($jsonPath), true);
        if (!$rows) {
            $this->command->error('Invalid JSON in time_duration_sub_seed.json');
            return;
        }

        $parentChildren = [];
        foreach ($rows as $row) {
            foreach ($row as $parent => $child) {
                $parent = trim((string) $parent);
                $child = is_string($child) ? trim($child) : '';
                if ($child !== '') {
                    if (!isset($parentChildren[$parent])) {
                        $parentChildren[$parent] = [];
                    }
                    if (!in_array($child, $parentChildren[$parent], true)) {
                        $parentChildren[$parent][] = $child;
                    }
                }
            }
        }

        $durationsByTitle = TimeDuration::all()->keyBy(fn ($d) => trim($d->title));
        $subCreated = 0;

        foreach ($parentChildren as $durationTitle => $subTitles) {
            $duration = $durationsByTitle->get($durationTitle);
            if (!$duration) continue;

            foreach ($subTitles as $subTitle) {
                TimeSubDuration::create([
                    'time_duration_id' => $duration->id,
                    'title' => $subTitle,
                    'slug' => uniqid(),
                    'status' => 1,
                    'creator' => 0,
                ]);
                $subCreated++;
            }
        }

        $this->command->info("Time duration seeding completed! 9 durations, {$subCreated} sub durations created.");
    }
}
