<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ci-datatables | CodeIgniter 4 Sample Application</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- DataTables 2.x Bootstrap 5 CSS -->
    <link href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        :root {
            --bs-primary-rgb: 37, 99, 235;
        }
        body {
            background-color: #f8fafc;
            color: #334155;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }
        .navbar-brand {
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .card {
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 600;
        }
        .stat-card {
            transition: transform 0.15s ease-in-out;
        }
        .stat-card:hover {
            transform: translateY(-2px);
        }
        .badge.status-Active {
            background-color: #dcfce7;
            color: #166534;
            font-weight: 600;
            padding: 0.35em 0.65em;
        }
        .badge.status-Inactive {
            background-color: #fee2e2;
            color: #991b1b;
            font-weight: 600;
            padding: 0.35em 0.65em;
        }
        .badge.status-On\ Leave,
        .badge[class*="status-On"] {
            background-color: #fef3c7;
            color: #92400e;
            font-weight: 600;
            padding: 0.35em 0.65em;
        }
        table.dataTable thead th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .code-block {
            background-color: #0f172a;
            color: #f8fafc;
            border-radius: 0.5rem;
            padding: 1rem;
            font-size: 0.85rem;
            overflow-x: auto;
        }
    </style>
</head>
<body>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center gap-2" href="<?= site_url() ?>">
                <i class="bi bi-table text-primary"></i>
                <span>ci-datatables</span>
                <span class="badge bg-primary text-white ms-1 fs-6">v2.1</span>
            </a>
            <div class="d-flex align-items-center gap-3">
                <span class="navbar-text d-none d-md-inline text-light opacity-75">
                    CodeIgniter 4 Server-Side Processing
                </span>
                <a href="https://github.com/cahyadsn/ci-datatables" target="_blank" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-github me-1"></i> GitHub Repo
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container-fluid px-4 py-4">

        <!-- Page Header -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Employee Directory & Management</h2>
                <p class="text-muted mb-0">
                    Live demo showcasing server-side pagination, multi-column search, table joins, and custom column transformations.
                </p>
            </div>
            <div class="mt-2 mt-md-0">
                <span class="badge bg-secondary me-1"><i class="bi bi-code-slash me-1"></i>CI 4.4+</span>
                <span class="badge bg-info text-dark me-1"><i class="bi bi-cpu me-1"></i>PHP 8.3+</span>
                <span class="badge bg-success"><i class="bi bi-database me-1"></i>SQLite / MySQL</span>
            </div>
        </div>

        <!-- Metric Statistics Cards -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card stat-card border-start border-4 border-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted text-uppercase mb-1 small">Total Employees</h6>
                                <h3 class="fw-bold mb-0 text-primary"><?= esc($stats['total_employees']) ?></h3>
                            </div>
                            <div class="fs-1 text-primary opacity-25"><i class="bi bi-people-fill"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card stat-card border-start border-4 border-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted text-uppercase mb-1 small">Active Status</h6>
                                <h3 class="fw-bold mb-0 text-success"><?= esc($stats['active_employees']) ?></h3>
                            </div>
                            <div class="fs-1 text-success opacity-25"><i class="bi bi-person-check-fill"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card stat-card border-start border-4 border-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted text-uppercase mb-1 small">Departments</h6>
                                <h3 class="fw-bold mb-0 text-info"><?= esc($stats['total_departments']) ?></h3>
                            </div>
                            <div class="fs-1 text-info opacity-25"><i class="bi bi-building"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card stat-card border-start border-4 border-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted text-uppercase mb-1 small">Avg Salary</h6>
                                <h3 class="fw-bold mb-0 text-warning">$<?= number_format((float) $stats['avg_salary'], 0) ?></h3>
                            </div>
                            <div class="fs-1 text-warning opacity-25"><i class="bi bi-cash-stack"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Control Panel -->
        <div class="card mb-4">
            <div class="card-body py-3">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-4">
                        <label for="filter-department" class="form-label small fw-semibold mb-1">Filter by Department (JOIN):</label>
                        <select id="filter-department" class="form-select form-select-sm">
                            <option value="">-- All Departments --</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?= esc($dept['id']) ?>"><?= esc($dept['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="filter-status" class="form-label small fw-semibold mb-1">Filter by Status:</label>
                        <select id="filter-status" class="form-select form-select-sm">
                            <option value="">-- All Statuses --</option>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                            <option value="On Leave">On Leave</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-5 d-flex gap-2 justify-content-md-end">
                        <button id="btn-reset-filters" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filters
                        </button>
                        <button id="btn-reload" class="btn btn-primary btn-sm">
                            <i class="bi bi-arrow-repeat me-1"></i> Reload Table
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- DataTables Card -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center py-3">
                <span class="d-flex align-items-center gap-2">
                    <i class="bi bi-grid-3x3 text-primary"></i>
                    <span>Server-Side Data Output</span>
                </span>
                <span class="text-muted small">Powered by <code>Cahyadsn\Datatables\Datatables</code></span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="employees-table" class="table table-striped table-hover align-middle w-100">
                        <thead>
                            <tr>
                                <th style="width: 50px;">ID</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Department</th>
                                <th>Position</th>
                                <th>Salary</th>
                                <th style="width: 100px;">Status</th>
                                <th style="width: 80px;" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Loaded asynchronously via DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Code Explanation Accordion -->
        <div class="accordion mb-5" id="codeAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingCode">
                    <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCode" aria-expanded="false" aria-controls="collapseCode">
                        <i class="bi bi-file-earmark-code me-2 text-primary"></i> View Server-side Controller & Client-side Implementation Code
                    </button>
                </h2>
                <div id="collapseCode" class="accordion-collapse collapse" aria-labelledby="headingCode" data-bs-parent="#codeAccordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                            <div class="col-12 col-lg-6">
                                <h6 class="fw-bold mb-2"><i class="bi bi-filetype-php me-1 text-primary"></i> Controller (<code>app/Controllers/Employees.php</code>)</h6>
                                <pre class="code-block"><code>$datatables = new Datatables();

$datatables
    ->select('employees.id, employees.first_name, employees.last_name, employees.email, departments.name as department, employees.position, employees.salary, employees.status')
    ->from('employees')
    ->join('departments', 'departments.id = employees.department_id', 'left');

// Custom runtime filters
if (!empty($deptFilter)) {
    $datatables->where('employees.department_id', $deptFilter);
}

// Modify column (editColumn)
$datatables->editColumn('salary', '$ $1', 'salary');
$datatables->editColumn('status', '&lt;span class="badge status-$1"&gt;$1&lt;/span&gt;', 'status');

// Add synthetic column (addColumn)
$datatables->addColumn('action', '&lt;button onclick="viewDetails($1)"&gt;&lt;i class="bi bi-eye"&gt;&lt;/i&gt;&lt;/button&gt;', 'id');

return $this->response->setJSON($datatables->generate());</code></pre>
                            </div>
                            <div class="col-12 col-lg-6">
                                <h6 class="fw-bold mb-2"><i class="bi bi-filetype-js me-1 text-warning"></i> Client-Side DataTables Script</h6>
                                <pre class="code-block"><code>new DataTable('#employees-table', {
    processing: true,
    serverSide: true,
    ajax: {
        url: '&lt;?= site_url('employees/getData') ?&gt;',
        type: 'POST',
        data: function(d) {
            // Include custom UI filter values
            var params = mapDataTablesRequest(d);
            params.custom_department = $('#filter-department').val();
            params.custom_status = $('#filter-status').val();
            return params;
        },
        dataFilter: function(data) {
            return mapDataTablesResponse(data);
        }
    },
    order: [[0, 'asc']],
    pageLength: 10
});</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Employee Detail Modal -->
    <div class="modal fade" id="employeeModal" tabindex="-1" aria-labelledby="employeeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="employeeModalLabel">Employee Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modal-employee-content">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
        <div id="liveToast" class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toast-message">
                    Action executed successfully.
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <!-- jQuery 3.7+ -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables 2.x JS -->
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.min.js"></script>

    <script>
    $(document).ready(function() {

        /**
         * Helper function to map DataTables 2.x request payload
         * to ci-datatables server-side expected parameters.
         */
        function mapDataTablesRequest(d) {
            var params = {
                sEcho: d.draw,
                iDisplayStart: d.start,
                iDisplayLength: d.length,
                sSearch: d.search ? d.search.value : '',
                iColumns: d.columns ? d.columns.length : 0,
                iSortingCols: d.order ? d.order.length : 0
            };

            if (d.columns) {
                d.columns.forEach(function(col, i) {
                    params['bSearchable_' + i] = col.searchable ? 'true' : 'false';
                    params['bSortable_' + i] = col.orderable ? 'true' : 'false';
                    params['sSearch_' + i] = col.search ? col.search.value : '';
                    params['mDataProp_' + i] = i;
                });
            }

            if (d.order) {
                d.order.forEach(function(ord, i) {
                    params['iSortCol_' + i] = ord.column;
                    params['sSortDir_' + i] = ord.dir;
                });
            }

            return params;
        }

        /**
         * Helper function to map ci-datatables response format
         * into DataTables 2.x expected properties.
         */
        function mapDataTablesResponse(raw) {
            var json = typeof raw === 'string' ? JSON.parse(raw) : raw;
            return JSON.stringify({
                draw: json.sEcho,
                recordsTotal: json.iTotalRecords,
                recordsFiltered: json.iTotalDisplayRecords,
                data: json.aaData
            });
        }

        // Initialize DataTable
        var table = $('#employees-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= site_url('employees/getData') ?>',
                type: 'POST',
                data: function(d) {
                    var params = mapDataTablesRequest(d);
                    // Append custom filters
                    params.custom_department = $('#filter-department').val();
                    params.custom_status = $('#filter-status').val();
                    return params;
                },
                dataFilter: function(data) {
                    return mapDataTablesResponse(data);
                }
            },
            columns: [
                { data: 0, className: 'fw-semibold text-muted' },
                { data: 1 },
                { data: 2 },
                { data: 3 },
                { data: 4, render: function(d) { return d ? '<span class="badge bg-light text-dark border">' + d + '</span>' : '-'; } },
                { data: 5 },
                { data: 6, className: 'font-monospace' },
                { data: 7 },
                { data: 8, orderable: false, searchable: false, className: 'text-center' }
            ],
            order: [[0, 'asc']],
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search employees...",
                processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading records...'
            }
        });

        // Filter event listeners
        $('#filter-department, #filter-status').on('change', function() {
            table.draw();
        });

        $('#btn-reset-filters').on('click', function() {
            $('#filter-department').val('');
            $('#filter-status').val('');
            table.search('').draw();
        });

        $('#btn-reload').on('click', function() {
            table.ajax.reload(null, false);
        });

        // Modal for viewing details
        window.viewDetails = function(id) {
            var modal = new bootstrap.Modal(document.getElementById('employeeModal'));
            $('#modal-employee-content').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>');
            modal.show();

            $.getJSON('<?= site_url('employees/getEmployee') ?>/' + id, function(data) {
                var html = '<table class="table table-bordered mb-0">' +
                    '<tr><th class="w-35 bg-light">ID</th><td>' + data.id + '</td></tr>' +
                    '<tr><th class="bg-light">Full Name</th><td>' + data.first_name + ' ' + data.last_name + '</td></tr>' +
                    '<tr><th class="bg-light">Email</th><td><a href="mailto:' + data.email + '">' + data.email + '</a></td></tr>' +
                    '<tr><th class="bg-light">Department</th><td>' + (data.department_name || '-') + '</td></tr>' +
                    '<tr><th class="bg-light">Position</th><td>' + data.position + '</td></tr>' +
                    '<tr><th class="bg-light">Salary</th><td>$' + parseFloat(data.salary).toLocaleString() + '</td></tr>' +
                    '<tr><th class="bg-light">Status</th><td><span class="badge status-' + data.status + '">' + data.status + '</span></td></tr>' +
                    '<tr><th class="bg-light">Joined Date</th><td>' + (data.created_at || '-') + '</td></tr>' +
                    '</table>';
                $('#modal-employee-content').html(html);
            }).fail(function() {
                $('#modal-employee-content').html('<div class="alert alert-danger mb-0">Failed to load employee details.</div>');
            });
        };

        // Quick edit row action placeholder
        window.editRow = function(id) {
            $('#toast-message').text('Edit clicked for employee #' + id);
            var toastEl = document.getElementById('liveToast');
            var toast = new bootstrap.Toast(toastEl);
            toast.show();
        };

    });
    </script>
</body>
</html>
