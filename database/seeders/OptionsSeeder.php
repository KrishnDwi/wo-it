<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\IssueType;
use Illuminate\Database\Seeder;

class OptionsSeeder extends Seeder
{
    public function run(): void
    {
        // Default Departments
        $departments = [
            ['name' => 'FB Kitchen', 'description' => 'Food & Beverage Kitchen'],
            ['name' => 'Housekeeping', 'description' => 'Housekeeping Department'],
            ['name' => 'Front Office', 'description' => 'Front Office Department'],
            ['name' => 'DT', 'description' => 'Daily Transactions Department'],
            ['name' => 'FB Service', 'description' => 'Food & Beverage Service'],
            ['name' => 'P&C', 'description' => 'Property & Catering'],
            ['name' => 'Security', 'description' => 'Security Department'],
            ['name' => 'Sales', 'description' => 'Sales Department'],
            ['name' => 'Acct', 'description' => 'Accounting Department'],
            ['name' => 'A&G', 'description' => 'Administration & General'],
        ];

        // Default Issue Types
        $issueTypes = [
            ['name' => 'ELECTRICAL MECHANICAL', 'description' => 'Electrical and Mechanical Problems'],
            ['name' => 'PLUMBING', 'description' => 'Plumbing Problems'],
            ['name' => 'HVAC', 'description' => 'Heating, Ventilation & Air Conditioning'],
            ['name' => 'BUILDING', 'description' => 'Building Structure Issues'],
            ['name' => 'FURNITURE', 'description' => 'Furniture & Fixtures'],
            ['name' => 'AV', 'description' => 'Audio & Visual Systems'],
            ['name' => 'SAFETY', 'description' => 'Safety & Emergency'],
            ['name' => 'KITCHEN EQUIPMENT', 'description' => 'Kitchen Equipment Issues'],
            ['name' => 'OTHER', 'description' => 'Other Issues'],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(['name' => $dept['name']], $dept);
        }

        foreach ($issueTypes as $type) {
            IssueType::firstOrCreate(['name' => $type['name']], $type);
        }
    }
}
