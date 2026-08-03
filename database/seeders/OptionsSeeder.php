<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\IssueType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OptionsSeeder extends Seeder
{
    public function run(): void
    {
        // Default Departments
        $departments = [
            ['name' => 'FB Kitchen', 'description' => 'Food & Beverage Kitchen', 'password' => Hash::make('FB Kitchen')],
            ['name' => 'Housekeeping', 'description' => 'Housekeeping Department', 'password' => Hash::make('Housekeeping')],
            ['name' => 'Front Office', 'description' => 'Front Office Department', 'password' => Hash::make('Front Office')],
            ['name' => 'DT', 'description' => 'Daily Transactions Department', 'password' => Hash::make('DT')],
            ['name' => 'FB Service', 'description' => 'Food & Beverage Service', 'password' => Hash::make('FB Service')],
            ['name' => 'P&C', 'description' => 'Property & Catering', 'password' => Hash::make('P&C')],
            ['name' => 'Security', 'description' => 'Security Department', 'password' => Hash::make('Security')],
            ['name' => 'Sales', 'description' => 'Sales Department', 'password' => Hash::make('Sales')],
            ['name' => 'Acct', 'description' => 'Accounting Department', 'password' => Hash::make('Acct')],
            ['name' => 'A&G', 'description' => 'Administration & General', 'password' => Hash::make('A&G')],
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
