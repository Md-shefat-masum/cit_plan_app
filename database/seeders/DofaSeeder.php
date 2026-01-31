<?php

namespace Database\Seeders;

use App\Models\DofaManagement\Dofa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DofaSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Dofa::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $titles = [
            '১। দাওয়াত',
            '২। সংগঠন',
            '৩। প্রশিক্ষণ',
            '৪। ইসলামী শিক্ষা আন্দোলন ও ছাত্র সমস্যা সমাধান',
            '৫। ইসলামী সমাজ বিনির্মাণ',
        ];

        foreach ($titles as $title) {
            Dofa::create([
                'title' => $title,
                'slug' => uniqid(),
                'status' => 1,
                'creator' => 0,
            ]);
        }

        $this->command->info('Dofa seeding completed!');
    }
}
