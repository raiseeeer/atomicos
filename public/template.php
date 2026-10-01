<?php
/**
 * Page template: table + search + add/edit modal + delete confirm + pagination.
 * Copy to  public/<module>.php  and change the 4 lines below + the labels.
 */
require __DIR__ . '/../app/bootstrap.php';
Auth::requireLogin();
Auth::requireRole(['admin', 'hr']);

$pageTitle   = 'Template';                  // page title + breadcrumb
$active      = 'template';                  // must match the key in sidebar.php (if you add a menu item)
$pageScripts = ['table-sort.js', 'table-export.js', 'template.js'];   // plugins first, then your module script

require APP_PATH . '/views/layout/header.php';
?>
<div class="card atm-panel">
  <div class="card-body">
    <!-- Toolbar -->
    <div class="row g-2 mb-3">
      <div class="col-md-6 col-lg-4">
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input type="search" id="tableSearch" class="form-control" placeholder="Search...">
        </div>
      </div>
      <div class="col-md-6 col-lg-8">
        <div class="d-flex justify-content-md-end gap-2">
          <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle " data-bs-toggle="dropdown"><i class="bi bi-download me-1"></i>Export</button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="#" data-export-as="csv" data-export-target="#dataTable"><i class="bi bi-file-earmark-text me-2"></i>CSV</a></li>
              <li><a class="dropdown-item" href="#" data-export-as="xlsx" data-export-target="#dataTable"><i class="bi bi-file-earmark-excel me-2 text-success"></i>Excel (.xlsx)</a></li>
              <li><a class="dropdown-item" href="#" data-export-as="pdf" data-export-target="#dataTable"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i>PDF</a></li>
              <li><a class="dropdown-item" href="#" data-export-as="doc" data-export-target="#dataTable"><i class="bi bi-file-earmark-word me-2 text-primary"></i>Word (.doc)</a></li>
            </ul>
          </div>
          <button class="btn btn-atm" id="btnAdd"><i class="bi bi-plus-lg me-1"></i>Add New</button>
        </div>
      </div>
    </div>

    <!-- Table (rows are filled by template.js) -->
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0 table-sortable" id="dataTable" data-sort-mode="server"
             data-export-url="api/template/export.php" data-export-title="Positions" data-export-filename="positions">
        <thead class="table-light">
          <tr>
            <th style="width:60px" data-no-export>#</th>
            <th data-sort="text"   data-key="title" data-sort-default="asc">Title</th>
            <th data-sort="number" data-key="base_salary">Base Salary</th>
            <th data-sort="date"   data-key="created_at">Created</th>
            <th class="text-end" style="width:120px" data-no-export>Actions</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>

  <!-- Pagination -->
  <div class="card-footer d-flex flex-wrap justify-content-between align-items-center gap-2">
    <small class="text-secondary" id="pageInfo"></small>
    <ul class="pagination pagination-sm mb-0" id="pager"></ul>
  </div>
</div>

<!-- Add / Edit modal -->
<div class="modal fade" id="recordModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content" id="recordForm" novalidate>
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle">Add Record</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id">
        <div class="mb-3">
          <label class="form-label">Title <span class="text-danger">*</span></label>
          <input type="text" name="title" class="form-control" maxlength="100" required>
          <div class="invalid-feedback"></div>
        </div>
        <div class="mb-0">
          <label class="form-label">Base Salary</label>
          <div class="input-group has-validation">
            <span class="input-group-text">₱</span>
            <input type="number" name="base_salary" class="form-control" min="0" step="0.01" placeholder="0.00">
            <div class="invalid-feedback"></div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-atm">Save</button>
      </div>
    </form>
  </div>
</div>

<!-- Delete confirmation modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content text-center">
      <div class="modal-body p-4">
        <div class="text-danger fs-1"><i class="bi bi-exclamation-circle"></i></div>
        <h5 class="mt-2">Delete record?</h5>
        <p class="text-secondary small mb-4" id="confirmText">This action cannot be undone.</p>
        <div class="d-flex gap-2 justify-content-center">
          <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-danger" id="btnConfirmDelete">Delete</button>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require APP_PATH . '/views/layout/footer.php'; ?>
