<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LeaveType::create([
            'name' => 'Sick Leave',
        ]);
        LeaveType::create([
            'name' => 'Annual Leave',
        ]);
        LeaveType::create([
            'name' => 'Personal Leave',
        ]);
        LeaveType::create([
            'name' => 'Maternity Leave',
        ]);
        LeaveType::create([
            'name' => 'Free Leave',
        ]);
    }
}
