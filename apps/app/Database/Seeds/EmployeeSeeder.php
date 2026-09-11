<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        // 1. Insert Departments
        $departments = [
            ['id' => 1, 'name' => 'Engineering', 'description' => 'Software engineering and IT operations'],
            ['id' => 2, 'name' => 'Marketing', 'description' => 'Brand management and digital marketing'],
            ['id' => 3, 'name' => 'Human Resources', 'description' => 'Talent acquisition and people operations'],
            ['id' => 4, 'name' => 'Finance', 'description' => 'Accounting, financial planning, and payroll'],
            ['id' => 5, 'name' => 'Product Design', 'description' => 'UI/UX design and product research'],
        ];

        $this->db->table('departments')->emptyTable();
        $this->db->table('departments')->insertBatch($departments);

        // 2. Insert 50 Employees
        $firstNames = [
            'James', 'Mary', 'Robert', 'Patricia', 'John', 'Jennifer', 'Michael', 'Linda',
            'David', 'Elizabeth', 'William', 'Barbara', 'Richard', 'Susan', 'Joseph', 'Jessica',
            'Thomas', 'Sarah', 'Charles', 'Karen', 'Christopher', 'Nancy', 'Daniel', 'Lisa',
            'Matthew', 'Betty', 'Anthony', 'Margaret', 'Mark', 'Sandra', 'Donald', 'Ashley',
            'Steven', 'Kimberly', 'Paul', 'Emily', 'Andrew', 'Donna', 'Joshua', 'Michelle',
            'Kenneth', 'Carol', 'Kevin', 'Amanda', 'Brian', 'Melissa', 'George', 'Deborah',
            'Edward', 'Stephanie'
        ];

        $lastNames = [
            'Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis',
            'Rodriguez', 'Martinez', 'Hernandez', 'Lopez', 'Gonzalez', 'Wilson', 'Anderson', 'Thomas',
            'Taylor', 'Moore', 'Jackson', 'Martin', 'Lee', 'Perez', 'Thompson', 'White',
            'Harris', 'Sanchez', 'Clark', 'Ramirez', 'Lewis', 'Robinson', 'Walker', 'Young',
            'Allen', 'King', 'Wright', 'Scott', 'Torres', 'Nguyen', 'Hill', 'Flores',
            'Green', 'Adams', 'Nelson', 'Baker', 'Hall', 'Rivera', 'Campbell', 'Mitchell',
            'Carter', 'Roberts'
        ];

        $positions = [
            1 => ['Software Engineer', 'Senior Backend Engineer', 'DevOps Specialist', 'QA Engineer', 'Full Stack Developer'],
            2 => ['Content Strategist', 'SEO Specialist', 'Growth Marketer', 'Marketing Lead'],
            3 => ['HR Manager', 'Talent Recruiter', 'People Partner', 'HR Specialist'],
            4 => ['Senior Accountant', 'Financial Analyst', 'Payroll Specialist', 'Controller'],
            5 => ['UI/UX Designer', 'Lead Product Designer', 'Graphic Artist', 'UX Researcher'],
        ];

        $statuses = ['Active', 'Active', 'Active', 'Active', 'Inactive', 'On Leave'];

        $employees = [];
        $baseDate = strtotime('2024-01-15');

        for ($i = 0; $i < 50; $i++) {
            $fName = $firstNames[$i];
            $lName = $lastNames[$i];
            $deptId = ($i % 5) + 1;
            $posList = $positions[$deptId];
            $position = $posList[$i % count($posList)];
            $salary = 45000 + (($i * 1250) % 65000);
            $status = $statuses[$i % count($statuses)];
            $createdAt = date('Y-m-d H:i:s', $baseDate + ($i * 86400 * 5));

            $employees[] = [
                'first_name'    => $fName,
                'last_name'     => $lName,
                'email'         => strtolower("{$fName}.{$lName}@example.com"),
                'department_id' => $deptId,
                'position'      => $position,
                'salary'        => $salary,
                'status'        => $status,
                'created_at'    => $createdAt,
            ];
        }

        $this->db->table('employees')->emptyTable();
        $this->db->table('employees')->insertBatch($employees);
    }
}
