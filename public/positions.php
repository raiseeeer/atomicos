<?php
require __DIR__ . '/../app/bootstrap.php';
Auth::requireLogin();
Auth::requireRole(['admin', 'hr']);

$pageTitle   = 'Positions';
$active      = 'positions';
$pageScripts = ['positions.js', 'table-sort.js', 'table-export.js'];

require APP_PATH . '/views/layout/header.php';

echo e('<button class="btn btn-sm btn-success">Success toast</button>');


?>

<div class="card atm-panel mb-0">
  <div class="card-body">
    <div class="row g-2 mb-3">
      <div class="col-md-4"><div class="input-group"><span class="input-group-text"><i class="bi bi-search"></i></span><input type="search" id="tableSearch" class="form-control" placeholder="Search..." aria-label="Search..."></div></div>
      <div class="col-md-2"><select class="form-select"><option value="">All departments</option><option>Engineering</option></select></div>
      <div class="col-md-2"><select class="form-select"><option value="">All status</option><option>Active</option><option>Inactive</option></select></div>
      <div class="col-md-2">
        <div class="dropdown">
          <button class="btn btn-outline-secondary dropdown-toggle w-100" data-bs-toggle="dropdown"><i class="bi bi-download me-1"></i>Export</button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="#" data-export-as="csv" data-export-target="#dataTable"><i class="bi bi-file-earmark-text me-2"></i>CSV</a></li>
            <li><a class="dropdown-item" href="#" data-export-as="xlsx" data-export-target="#dataTable"><i class="bi bi-file-earmark-excel me-2 text-success"></i>Excel (.xlsx)</a></li>
            <li><a class="dropdown-item" href="#" data-export-as="pdf" data-export-target="#dataTable"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i>PDF</a></li>
            <li><a class="dropdown-item" href="#" data-export-as="doc" data-export-target="#dataTable"><i class="bi bi-file-earmark-word me-2 text-primary"></i>Word (.doc)</a></li>
          </ul>
        </div>
      </div>
      <div class="col-md-2 text-md-end"><button class="btn btn-atm w-100"><i class="bi bi-plus-lg me-1"></i>Add</button></div>
    </div>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0 table-sortable" id="dataTable" data-export-title="Employees" data-export-filename="employees" role="table">
        <thead class="table-light">
          <tr>
            <th data-no-export="" scope="col">#</th>
            <th data-sort="text" data-key="position"  data-sort-default="asc" scope="col">Position</th>
            <th data-sort="number" data-key="salary" scope="col">Salary</th>
            <th data-sort="date" data-key="date_hired" scope="col">Date Created</th>
            <th data-sort="text" data-key="status" scope="col">Status</th>
            <th class="text-end" data-no-export="" scope="col">Actions</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>
  <div class="card-footer d-flex flex-wrap justify-content-between align-items-center gap-2">
  <div class="d-flex align-items-center gap-2">
    <small class="text-secondary">Rows</small>
    <select id="perPage" class="form-select form-select-sm w-auto"></select>
    <small class="text-secondary ms-2" id="pageInfo"></small>
  </div>
  <ul class="pagination pagination-sm mb-0" id="pager"></ul>
</div>
</div>



<?php require APP_PATH . '/views/layout/footer.php'; ?>
