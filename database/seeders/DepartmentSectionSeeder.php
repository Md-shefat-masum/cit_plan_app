<?php

namespace Database\Seeders;

use App\Models\TaskManagement\DepartmentSection;
use App\Models\TaskManagement\DepartmentSubSection;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSectionSeeder extends Seeder
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

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $it_sections = [
            'ওয়েব অ্যাপ ডেভেলপমেন্ট',
            'মোবাইল অ্যাপ ডেভেলপমেন্ট',
            'ডেস্কটপ অ্যাপ ডেভেলপমেন্ট',
            'প্রজেক্ট ম্যানেজমেন্ট',
            'আইটি ট্রেনিং',
            'টেক ম্যাটারিয়ালস',
            'টেক সোশ্যাল প্লাটফর্ম',
            'টেকনিক্যাল সাপোর্ট',
            'সার্ভিসিং',
            'পারচেজ',
            'ডিজাইন প্রোডাকশন',
            'ভিডিও প্রোডাকশন',
            'UI/UX প্রোডাকশন',
            'সার্ভার ম্যানেজমেন্ট',
            'সিকিউরিটি',
            'রিসার্চ এন্ড ডেভেলপমেন্ট',
            'টেক টিম',
            'ডাটা প্রসেসিং',
            'ডিজিটাল মার্কেটিং',
            'টেক ক্যারিয়ার',
            'ইনভেন্টরি',
            'ডিজিটাল কন্টেন্ট',
            'অ্যানিমেশন',
            'রিপোর্ট সফটওয়্যার',
            'প্রশিক্ষণ সাইট',
            'শহীদ সাইট',
            'আইটিউব',
            'আর্টিকেল',
            'অর্থ',
            'শাখা তত্ত্বাবধান',
            'অফিস ব্যবস্থাপনা',
            'ব্যাকআপ',
            'রিপোর্টিং',
            'স্প্রিচুয়াল ট্রেনিং',
            'বাসা',
            'বিশেষ প্রজেক্ট',
            'অন্যান্য',
        ];

        foreach ($it_sections as $item) {
            DepartmentSection::create([
                'department_id' => 1,
                'title' => $item,
                'slug' => uniqid(),
                'status' => 1,
                'creator' => 1
            ]);
        }

        $sections = [
            // IT (department_id: 1)
            // ['department_id' => 1, 'title' => 'App Development'],

            // Planning (department_id: 2)
            ['department_id' => 2, 'title' => 'BM'],
            ['department_id' => 2, 'title' => 'Purchase'],

            // Social Media (department_id: 3)
            ['department_id' => 3, 'title' => 'Graphics'],
            ['department_id' => 3, 'title' => 'Video Editor'],

            // Publication (department_id: 4)
            ['department_id' => 4, 'title' => 'Graphics'],
            ['department_id' => 4, 'title' => 'Sales'],
        ];

        foreach ($sections as $item) {
            DepartmentSection::create([
                'department_id' => $item['department_id'],
                'title' => $item['title'],
                'slug' => uniqid(),
                'status' => 1,
                'creator' => 0,
            ]);
        }

        $this->command->info('Department section seeding completed!');
    }
}
