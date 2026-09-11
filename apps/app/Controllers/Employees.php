<?php

namespace App\Controllers;

use Cahyadsn\Datatables\Datatables;
use Config\Database;

class Employees extends BaseController
{
    /**
     * Auto-migrates and seeds SQLite database if tables don't exist yet,
     * ensuring a zero-configuration out-of-the-box demo.
     */
    protected function ensureDatabaseReady(): void
    {
        $db = Database::connect();
        if (!$db->tableExists('employees')) {
            $migrate = \Config\Services::migrations();
            $migrate->latest();

            $seeder = Database::seeder();
            $seeder->call('App\Database\Seeds\EmployeeSeeder');
        }
    }

    /**
     * Display the main DataTables sample view
     */
    public function index()
    {
        $this->ensureDatabaseReady();

        $db = Database::connect();
        $departments = $db->table('departments')->orderBy('name', 'ASC')->get()->getResultArray();

        $stats = [
            'total_employees'   => $db->table('employees')->countAllResults(),
            'active_employees'  => $db->table('employees')->where('status', 'Active')->countAllResults(),
            'total_departments' => count($departments),
            'avg_salary'        => $db->table('employees')->selectAvg('salary')->get()->getRow()->salary ?? 0,
        ];

        return view('employees', [
            'departments' => $departments,
            'stats'       => $stats,
        ]);
    }

    /**
     * Server-side DataTables AJAX endpoint using ci-datatables
     */
    public function getData()
    {
        $this->ensureDatabaseReady();

        $datatables = new Datatables();

        // 1. Select primary and joined columns with explicit aliases for column formatting
        $datatables
            ->select('employees.id as id, employees.first_name as first_name, employees.last_name as last_name, employees.email as email, departments.name as department, employees.position as position, employees.salary as salary, employees.status as status')
            ->from('employees')
            ->join('departments', 'departments.id = employees.department_id', 'left');

        // 2. Custom runtime filtering (e.g. from custom department / status filter dropdowns)
        $deptFilter = $this->request->getPost('custom_department');
        if (!empty($deptFilter)) {
            $datatables->where('employees.department_id', $deptFilter);
        }

        $statusFilter = $this->request->getPost('custom_status');
        if (!empty($statusFilter)) {
            $datatables->where('employees.status', $statusFilter);
        }

        // 3. Modifying column content (editColumn)
        // Format salary with currency symbol
        $datatables->editColumn('salary', '$ $1', 'salary');

        // Format status badge
        $datatables->editColumn('status', '<span class="badge status-$1">$1</span>', 'status');

        // 4. Adding synthetic column with dynamic replacement (addColumn)
        // Adds an Action column with View & Edit buttons passing the employee id
        $actionBtn = '<div class="btn-group btn-group-sm" role="group">'
                   . '<button type="button" class="btn btn-outline-primary btn-sm" onclick="viewDetails($1)" title="View Details"><i class="bi bi-eye"></i></button>'
                   . '<button type="button" class="btn btn-outline-secondary btn-sm" onclick="editRow($1)" title="Edit"><i class="bi bi-pencil"></i></button>'
                   . '</div>';
        $datatables->addColumn('action', $actionBtn, 'id');

        // 5. Generate and return DataTables compliant JSON output
        $json = $datatables->generate();

        return $this->response
            ->setContentType('application/json')
            ->setBody($json);
    }

    /**
     * Quick detail endpoint for modal view
     */
    public function getEmployee($id = null)
    {
        $this->ensureDatabaseReady();

        $db = Database::connect();
        $employee = $db->table('employees')
            ->select('employees.*, departments.name as department_name')
            ->join('departments', 'departments.id = employees.department_id', 'left')
            ->where('employees.id', $id)
            ->get()
            ->getRowArray();

        if (!$employee) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Employee not found']);
        }

        return $this->response->setJSON($employee);
    }
}
