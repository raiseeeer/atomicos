<?php
require __DIR__ . '/../app/bootstrap.php';
Auth::requireLogin();
Auth::requireRole(['admin']);

$pageTitle   = 'UI Kit';
$active      = 'uikit';
$pageScripts = ['ui-kit.js', 'table-sort.js', 'table-export.js'];  // order matters: ui-kit.js captures the clean copy source first

/* ---------- tiny helpers to build the page ---------- */
$kitNav = [];
function kit_group(string $t): void { echo '<h4 class="kit-group">' . $t . '</h4>'; }
function kit_open(string $id, string $title, string $note = ''): void {
    global $kitNav; $kitNav[$id] = $title;
    echo '<div class="card atm-panel kit-block mb-4" id="' . $id . '" data-title="' . strtolower($title . ' ' . $note) . '">'
       . '<div class="card-header d-flex align-items-center gap-2"><div><h3 class="card-title">' . $title . '</h3>'
       . ($note ? '<div class="small text-secondary">' . $note . '</div>' : '') . '</div>'
       . '<div class="ms-auto d-flex gap-1"><button class="btn btn-sm btn-outline-secondary kit-toggle"><i class="bi bi-code-slash"></i> Code</button>'
       . '<button class="btn btn-sm btn-atm kit-copy"><i class="bi bi-clipboard"></i> Copy</button></div></div>'
       . '<div class="card-body"><div class="kit-demo">';
}
function kit_close(): void { echo '</div><pre class="kit-pre d-none"></pre></div></div>'; }
function kit_code(string $id, string $title, string $note, string $code): void {
    global $kitNav; $kitNav[$id] = $title;
    echo '<div class="card atm-panel kit-block mb-4" id="' . $id . '" data-title="' . strtolower($title . ' ' . $note) . '">'
       . '<div class="card-header d-flex align-items-center gap-2"><div><h3 class="card-title">' . $title . '</h3><div class="small text-secondary">' . $note . '</div></div>'
       . '<div class="ms-auto"><button class="btn btn-sm btn-atm kit-copy"><i class="bi bi-clipboard"></i> Copy</button></div></div>'
       . '<div class="card-body"><pre class="kit-pre">' . htmlspecialchars($code) . '</pre></div></div>';
}

ob_start();
?>

<?php kit_group('Page Structure'); ?>

<?php kit_open('page-header', 'Page Header + Toolbar', 'Title row with primary action, then a card containing filters and a table.'); ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
  <div>
    <h4 class="mb-0">Employees</h4>
    <small class="text-secondary">Manage all employee records</small>
  </div>
  <button class="btn btn-atm"><i class="bi bi-plus-lg me-1"></i>Add Employee</button>
</div>
<?php kit_close(); ?>

<?php kit_open('card-basic', 'Card (header, body, footer)', 'Default container for any content.'); ?>
<div class="card atm-panel mb-0">
  <div class="card-header"><h3 class="card-title">Card Title</h3></div>
  <div class="card-body">Card content goes here.</div>
  <div class="card-footer text-end">
    <button class="btn btn-outline-secondary">Cancel</button>
    <button class="btn btn-atm">Save</button>
  </div>
</div>
<?php kit_close(); ?>

<?php kit_open('card-collapse', 'Card (collapsible tools)', 'AdminLTE collapse button in the header.'); ?>
<div class="card atm-panel mb-0">
  <div class="card-header">
    <h3 class="card-title">Filters</h3>
    <div class="card-tools">
      <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
        <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
        <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
      </button>
    </div>
  </div>
  <div class="card-body">Hidden when collapsed.</div>
</div>
<?php kit_close(); ?>

<?php kit_open('stat-boxes', 'Stat Boxes', 'Used on dashboards. Tones: atm-teal, atm-violet, atm-amber, atm-rose.'); ?>
<div class="row">
  <div class="col-12 col-md-6 col-xl-3">
    <div class="info-box atm-info">
      <span class="info-box-icon atm-teal"><i class="bi bi-people-fill"></i></span>
      <div class="info-box-content"><span class="info-box-text text-secondary">Total Employees</span><span class="info-box-number">128</span></div>
    </div>
  </div>
  <div class="col-12 col-md-6 col-xl-3">
    <div class="info-box atm-info">
      <span class="info-box-icon atm-amber"><i class="bi bi-alarm-fill"></i></span>
      <div class="info-box-content"><span class="info-box-text text-secondary">Late Today</span><span class="info-box-number">6</span></div>
    </div>
  </div>
</div>
<?php kit_close(); ?>

<?php kit_open('profile-card', 'Profile Card', 'Employee summary header.'); ?>
<div class="card atm-panel mb-0">
  <div class="card-body d-flex align-items-center gap-3">
    <span class="atm-avatar atm-avatar-lg mb-0">JD</span>
    <div class="flex-grow-1">
      <h5 class="mb-0">Juan Dela Cruz</h5>
      <div class="text-secondary">Software Developer · Engineering</div>
      <span class="badge text-bg-success mt-1">Active</span>
    </div>
    <button class="btn btn-outline-secondary"><i class="bi bi-pencil me-1"></i>Edit</button>
  </div>
</div>
<?php kit_close(); ?>

<?php kit_open('time-widget', 'Time In / Time Out Widget', 'For the DTR "My DTR" page.'); ?>
<div class="card atm-panel text-center mb-0">
  <div class="card-body py-4">
    <div class="display-5 fw-bold font-monospace">08:00:00</div>
    <div class="text-secondary mb-3">Thursday, October 1, 2026</div>
    <div class="d-flex justify-content-center gap-2">
      <button class="btn btn-atm px-4"><i class="bi bi-box-arrow-in-right me-1"></i>Time In</button>
      <button class="btn btn-outline-danger px-4" disabled><i class="bi bi-box-arrow-right me-1"></i>Time Out</button>
    </div>
  </div>
</div>
<?php kit_close(); ?>

<?php kit_open('empty-state', 'Empty State', 'When a table or list has no data.'); ?>
<div class="text-center py-5 text-secondary">
  <i class="bi bi-inbox fs-1 d-block mb-2"></i>
  <h6 class="mb-1">No records found</h6>
  <p class="small mb-3">Try changing your filters or add a new record.</p>
  <button class="btn btn-atm btn-sm"><i class="bi bi-plus-lg me-1"></i>Add Record</button>
</div>
<?php kit_close(); ?>

<?php kit_group('Buttons &amp; Text'); ?>

<?php kit_open('buttons', 'Buttons', 'Atomicos gradient (btn-atm), Bootstrap solid and outline variants.'); ?>
<div class="d-flex flex-wrap gap-2 mb-3">
  <button class="btn btn-atm">Primary (Atomicos)</button>
  <button class="btn btn-primary">Primary</button>
  <button class="btn btn-secondary">Secondary</button>
  <button class="btn btn-success">Success</button>
  <button class="btn btn-warning">Warning</button>
  <button class="btn btn-danger">Danger</button>
  <button class="btn btn-info">Info</button>
  <button class="btn btn-dark">Dark</button>
  <button class="btn btn-light border">Light</button>
</div>
<div class="d-flex flex-wrap gap-2">
  <button class="btn btn-outline-primary">Outline</button>
  <button class="btn btn-outline-secondary">Outline</button>
  <button class="btn btn-outline-danger">Outline</button>
  <button class="btn btn-link">Link button</button>
  <button class="btn btn-atm" disabled>Disabled</button>
</div>
<?php kit_close(); ?>

<?php kit_open('buttons-sizes', 'Buttons: sizes, icons, loading, groups'); ?>
<div class="d-flex flex-wrap align-items-center gap-2 mb-3">
  <button class="btn btn-atm btn-lg">Large</button>
  <button class="btn btn-atm">Default</button>
  <button class="btn btn-atm btn-sm">Small</button>
  <button class="btn btn-atm"><i class="bi bi-save me-1"></i>Save</button>
  <button class="btn btn-atm" disabled><span class="spinner-border spinner-border-sm me-1"></span>Saving...</button>
  <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Edit"><i class="bi bi-pencil"></i></button>
  <button class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="Delete"><i class="bi bi-trash"></i></button>
</div>
<div class="btn-group" role="group">
  <button class="btn btn-outline-secondary active">Day</button>
  <button class="btn btn-outline-secondary">Week</button>
  <button class="btn btn-outline-secondary">Month</button>
</div>
<?php kit_close(); ?>

<?php kit_open('typography', 'Typography &amp; Text', 'Headings, muted text, links, lists, code.'); ?>
<h1>Heading 1</h1><h2>Heading 2</h2><h3>Heading 3</h3><h4>Heading 4</h4><h5>Heading 5</h5><h6>Heading 6</h6>
<p class="lead">Lead paragraph for introductions.</p>
<p>Normal paragraph with a <a href="#">link</a>, <strong>bold</strong>, <em>italic</em>, <code>inline code</code> and <mark>highlight</mark>.</p>
<p class="text-secondary mb-1">Muted / secondary text</p>
<p class="small mb-1">Small text</p>
<p class="text-success mb-1">Success</p><p class="text-danger mb-1">Danger</p><p class="text-warning mb-3">Warning</p>
<ul><li>Bullet item</li><li>Another item</li></ul>
<?php kit_close(); ?>

<?php kit_open('badges', 'Badges &amp; Status Pills', 'Employee and DTR statuses.'); ?>
<div class="d-flex flex-wrap gap-2 mb-3">
  <span class="badge text-bg-success">Active</span>
  <span class="badge text-bg-secondary">Inactive</span>
  <span class="badge text-bg-primary">Primary</span>
  <span class="badge text-bg-warning">Pending</span>
  <span class="badge text-bg-danger">Rejected</span>
  <span class="badge rounded-pill text-bg-info">Pill</span>
</div>
<div class="d-flex flex-wrap gap-2">
  <span class="badge bg-success-subtle text-success-emphasis">Present</span>
  <span class="badge bg-warning-subtle text-warning-emphasis">Late</span>
  <span class="badge bg-info-subtle text-info-emphasis">Undertime</span>
  <span class="badge bg-danger-subtle text-danger-emphasis">Absent</span>
</div>
<?php kit_close(); ?>

<?php kit_group('Forms'); ?>

<?php kit_open('form-inputs', 'Inputs: text, email, number, date, time', 'Standard labelled controls with helper text.'); ?>
<form>
  <div class="row g-3">
    <div class="col-md-6"><label class="form-label">Text <span class="text-danger">*</span></label><input type="text" class="form-control" placeholder="Enter text" required></div>
    <div class="col-md-6"><label class="form-label">Email</label><input type="email" class="form-control" placeholder="name@company.com"><div class="form-text">We never share this.</div></div>
    <div class="col-md-4"><label class="form-label">Number</label><input type="number" class="form-control" min="0" step="0.01" placeholder="0.00"></div>
    <div class="col-md-4"><label class="form-label">Date</label><input type="date" class="form-control"></div>
    <div class="col-md-4"><label class="form-label">Time</label><input type="time" class="form-control"></div>
    <div class="col-md-6"><label class="form-label">Disabled</label><input type="text" class="form-control" value="Cannot edit" disabled></div>
    <div class="col-md-6"><label class="form-label">Read only</label><input type="text" class="form-control-plaintext border-bottom" value="EMP-0001" readonly></div>
  </div>
</form>
<?php kit_close(); ?>

<?php kit_open('form-input-groups', 'Input groups &amp; password toggle', 'Icons, prefixes, suffixes, buttons.'); ?>
<div class="row g-3">
  <div class="col-md-6"><label class="form-label">With icon</label>
    <div class="input-group"><span class="input-group-text"><i class="bi bi-person"></i></span><input type="text" class="form-control" placeholder="Username"></div></div>
  <div class="col-md-6"><label class="form-label">Password</label>
    <div class="input-group"><span class="input-group-text"><i class="bi bi-lock"></i></span><input type="password" class="form-control" value="secret"><button class="btn btn-outline-secondary kit-pw-toggle" type="button"><i class="bi bi-eye"></i></button></div></div>
  <div class="col-md-6"><label class="form-label">Currency</label>
    <div class="input-group"><span class="input-group-text">₱</span><input type="number" class="form-control" placeholder="0.00"><span class="input-group-text">PHP</span></div></div>
  <div class="col-md-6"><label class="form-label">Search</label>
    <div class="input-group"><input type="search" class="form-control" placeholder="Search employee..."><button class="btn btn-atm" type="button"><i class="bi bi-search"></i></button></div></div>
</div>
<?php kit_close(); ?>

<?php kit_open('form-select', 'Select, multi-select, textarea', 'Use data-* or JS to load options from the API.'); ?>
<div class="row g-3">
  <div class="col-md-4"><label class="form-label">Select</label>
    <select class="form-select"><option value="">-- Choose --</option><option value="1">Human Resources</option><option value="2">Engineering</option></select></div>
  <div class="col-md-4"><label class="form-label">Select (small)</label>
    <select class="form-select form-select-sm"><option>Active</option><option>Inactive</option></select></div>
  <div class="col-md-4"><label class="form-label">Multi-select</label>
    <select class="form-select" multiple size="3"><option>Option 1</option><option>Option 2</option><option>Option 3</option></select></div>
  <div class="col-12"><label class="form-label">Textarea</label><textarea class="form-control" rows="3" placeholder="Address / remarks"></textarea></div>
</div>
<?php kit_close(); ?>

<?php kit_open('form-checks', 'Checkbox, radio, switch, range, file', 'Selection controls and uploads.'); ?>
<div class="row g-3">
  <div class="col-md-4">
    <div class="form-check"><input class="form-check-input" type="checkbox" id="c1" checked><label class="form-check-label" for="c1">Checkbox</label></div>
    <div class="form-check"><input class="form-check-input" type="checkbox" id="c2"><label class="form-check-label" for="c2">Another option</label></div>
  </div>
  <div class="col-md-4">
    <div class="form-check"><input class="form-check-input" type="radio" name="r" id="r1" checked><label class="form-check-label" for="r1">Male</label></div>
    <div class="form-check"><input class="form-check-input" type="radio" name="r" id="r2"><label class="form-check-label" for="r2">Female</label></div>
  </div>
  <div class="col-md-4"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" id="s1" checked><label class="form-check-label" for="s1">Active</label></div></div>
  <div class="col-md-6"><label class="form-label">File upload</label><input type="file" class="form-control" accept=".jpg,.png,.pdf"></div>
  <div class="col-md-6"><label class="form-label">Range</label><input type="range" class="form-range" min="0" max="100"></div>
</div>
<?php kit_close(); ?>

<?php kit_open('form-floating', 'Floating labels', 'Compact, modern fields.'); ?>
<div class="row g-3">
  <div class="col-md-6"><div class="form-floating"><input type="text" class="form-control" id="fl1" placeholder="First name"><label for="fl1">First name</label></div></div>
  <div class="col-md-6"><div class="form-floating"><select class="form-select" id="fl2"><option>Engineering</option><option>Finance</option></select><label for="fl2">Department</label></div></div>
</div>
<?php kit_close(); ?>

<?php kit_open('form-validation', 'Validation states', 'Add is-valid / is-invalid and a feedback div.'); ?>
<div class="row g-3">
  <div class="col-md-6"><label class="form-label">Valid</label><input type="text" class="form-control is-valid" value="Looks good"><div class="valid-feedback">Looks good!</div></div>
  <div class="col-md-6"><label class="form-label">Invalid</label><input type="text" class="form-control is-invalid" value=""><div class="invalid-feedback">This field is required.</div></div>
</div>
<?php kit_close(); ?>

<?php kit_open('form-full', 'Full Form: Employee (sectioned, 2-column)', 'Copy as the base for any add/edit form.'); ?>
<form id="employeeForm" novalidate>
  <input type="hidden" name="id" value="">
  <h6 class="text-uppercase text-secondary small fw-bold mb-3">Personal Information</h6>
  <div class="row g-3 mb-4">
    <div class="col-md-3"><label class="form-label">Employee No. <span class="text-danger">*</span></label><input type="text" name="employee_no" class="form-control" required></div>
    <div class="col-md-3"><label class="form-label">First Name <span class="text-danger">*</span></label><input type="text" name="first_name" class="form-control" required></div>
    <div class="col-md-3"><label class="form-label">Middle Name</label><input type="text" name="middle_name" class="form-control"></div>
    <div class="col-md-3"><label class="form-label">Last Name <span class="text-danger">*</span></label><input type="text" name="last_name" class="form-control" required></div>
    <div class="col-md-3"><label class="form-label">Birthdate</label><input type="date" name="birthdate" class="form-control"></div>
    <div class="col-md-3"><label class="form-label">Gender</label><select name="gender" class="form-select"><option value="">-- Select --</option><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select></div>
    <div class="col-md-3"><label class="form-label">Contact No.</label><input type="text" name="contact_no" class="form-control"></div>
    <div class="col-md-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control"></div>
    <div class="col-12"><label class="form-label">Address</label><textarea name="address" class="form-control" rows="2"></textarea></div>
  </div>
  <h6 class="text-uppercase text-secondary small fw-bold mb-3">Job Details</h6>
  <div class="row g-3 mb-4">
    <div class="col-md-4"><label class="form-label">Department</label><select name="department_id" class="form-select"><option value="">-- Select --</option></select></div>
    <div class="col-md-4"><label class="form-label">Position</label><select name="position_id" class="form-select"><option value="">-- Select --</option></select></div>
    <div class="col-md-4"><label class="form-label">Date Hired</label><input type="date" name="date_hired" class="form-control"></div>
    <div class="col-12"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="status" value="active" id="empStatus" checked><label class="form-check-label" for="empStatus">Active employee</label></div></div>
  </div>
  <div class="d-flex justify-content-end gap-2 border-top pt-3">
    <button type="reset" class="btn btn-outline-secondary">Reset</button>
    <button type="submit" class="btn btn-atm"><i class="bi bi-save me-1"></i>Save Employee</button>
  </div>
</form>
<?php kit_close(); ?>

<?php kit_group('Tables &amp; Lists'); ?>

<?php kit_open('table-data', 'Data Table (sortable headers, toolbar, badges, actions, pagination)', 'Click a header to sort; use Export for CSV, Excel, PDF or Word. Fill <tbody> from your API.'); ?>
<div class="card atm-panel mb-0">
  <div class="card-body">
    <div class="row g-2 mb-3">
      <div class="col-md-4"><div class="input-group"><span class="input-group-text"><i class="bi bi-search"></i></span><input type="search" id="tableSearch" class="form-control" placeholder="Search..."></div></div>
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
      <table class="table table-hover align-middle mb-0 table-sortable" id="dataTable" data-export-title="Employees" data-export-filename="employees">
        <thead class="table-light">
          <tr>
            <th data-no-export>#</th>
            <th data-sort="text" data-key="name" data-sort-default="asc">Employee</th>
            <th data-sort="text" data-key="position">Position</th>
            <th data-sort="text" data-key="department">Department</th>
            <th data-sort="number" data-key="salary">Salary</th>
            <th data-sort="date" data-key="date_hired">Date Hired</th>
            <th data-sort="text" data-key="status">Status</th>
            <th class="text-end" data-no-export>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="row-num">1</td>
            <td data-value="Dela Cruz, Juan" data-export-value="Juan Dela Cruz"><div class="d-flex align-items-center gap-2"><span class="atm-avatar">JD</span><div><div class="fw-semibold">Juan Dela Cruz</div><small class="text-secondary">EMP-0001</small></div></div></td>
            <td>Software Developer</td><td>Engineering</td>
            <td data-value="40000">&#8369;40,000.00</td>
            <td data-value="2024-01-15">Jan 15, 2024</td>
            <td><span class="badge text-bg-success">Active</span></td>
            <td class="text-end text-nowrap">
              <button class="btn btn-sm btn-outline-secondary btn-view" title="View"><i class="bi bi-eye"></i></button>
              <button class="btn btn-sm btn-outline-primary btn-edit" title="Edit"><i class="bi bi-pencil"></i></button>
              <button class="btn btn-sm btn-outline-danger btn-delete" title="Delete"><i class="bi bi-trash"></i></button>
            </td>
          </tr>
          <tr>
            <td class="row-num">2</td>
            <td data-value="Santos, Maria" data-export-value="Maria Santos"><div class="d-flex align-items-center gap-2"><span class="atm-avatar">MS</span><div><div class="fw-semibold">Maria Santos</div><small class="text-secondary">EMP-0002</small></div></div></td>
            <td>HR Manager</td><td>Human Resources</td>
            <td data-value="45000">&#8369;45,000.00</td>
            <td data-value="2022-06-01">Jun 01, 2022</td>
            <td><span class="badge text-bg-secondary">Inactive</span></td>
            <td class="text-end text-nowrap">
              <button class="btn btn-sm btn-outline-secondary btn-view" title="View"><i class="bi bi-eye"></i></button>
              <button class="btn btn-sm btn-outline-primary btn-edit" title="Edit"><i class="bi bi-pencil"></i></button>
              <button class="btn btn-sm btn-outline-danger btn-delete" title="Delete"><i class="bi bi-trash"></i></button>
            </td>
          </tr>
          <tr>
            <td class="row-num">3</td>
            <td data-value="Reyes, Pedro" data-export-value="Pedro Reyes"><div class="d-flex align-items-center gap-2"><span class="atm-avatar">PR</span><div><div class="fw-semibold">Pedro Reyes</div><small class="text-secondary">EMP-0003</small></div></div></td>
            <td>Accountant</td><td>Finance</td>
            <td data-value="32000">&#8369;32,000.00</td>
            <td data-value="2023-03-20">Mar 20, 2023</td>
            <td><span class="badge text-bg-success">Active</span></td>
            <td class="text-end text-nowrap">
              <button class="btn btn-sm btn-outline-secondary btn-view" title="View"><i class="bi bi-eye"></i></button>
              <button class="btn btn-sm btn-outline-primary btn-edit" title="Edit"><i class="bi bi-pencil"></i></button>
              <button class="btn btn-sm btn-outline-danger btn-delete" title="Delete"><i class="bi bi-trash"></i></button>
            </td>
          </tr>
          <tr>
            <td class="row-num">4</td>
            <td data-value="Lopez, Ana" data-export-value="Ana Lopez"><div class="d-flex align-items-center gap-2"><span class="atm-avatar">AL</span><div><div class="fw-semibold">Ana Lopez</div><small class="text-secondary">EMP-0004</small></div></div></td>
            <td>Operations Staff</td><td>Operations</td>
            <td data-value="22000">&#8369;22,000.00</td>
            <td data-value="2025-08-11">Aug 11, 2025</td>
            <td><span class="badge text-bg-success">Active</span></td>
            <td class="text-end text-nowrap">
              <button class="btn btn-sm btn-outline-secondary btn-view" title="View"><i class="bi bi-eye"></i></button>
              <button class="btn btn-sm btn-outline-primary btn-edit" title="Edit"><i class="bi bi-pencil"></i></button>
              <button class="btn btn-sm btn-outline-danger btn-delete" title="Delete"><i class="bi bi-trash"></i></button>
            </td>
          </tr>
          <tr>
            <td class="row-num">5</td>
            <td data-value="Mendoza, Carlo" data-export-value="Carlo Mendoza"><div class="d-flex align-items-center gap-2"><span class="atm-avatar">CM</span><div><div class="fw-semibold">Carlo Mendoza</div><small class="text-secondary">EMP-0005</small></div></div></td>
            <td>Software Developer</td><td>Engineering</td>
            <td data-value="38500.5">&#8369;38,500.50</td>
            <td data-value="2021-11-05">Nov 05, 2021</td>
            <td><span class="badge text-bg-success">Active</span></td>
            <td class="text-end text-nowrap">
              <button class="btn btn-sm btn-outline-secondary btn-view" title="View"><i class="bi bi-eye"></i></button>
              <button class="btn btn-sm btn-outline-primary btn-edit" title="Edit"><i class="bi bi-pencil"></i></button>
              <button class="btn btn-sm btn-outline-danger btn-delete" title="Delete"><i class="bi bi-trash"></i></button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <div class="card-footer d-flex flex-wrap justify-content-between align-items-center gap-2">
    <small class="text-secondary">Showing 1 to 5 of 5 entries</small>
    <ul class="pagination pagination-sm mb-0">
      <li class="page-item disabled"><a class="page-link" href="#">&laquo;</a></li>
      <li class="page-item active"><a class="page-link" href="#">1</a></li>
      <li class="page-item"><a class="page-link" href="#">2</a></li>
      <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
    </ul>
  </div>
</div>
<?php kit_close(); ?>

<?php kit_open('export-button', 'Export Button (CSV, Excel, PDF, Word)', 'Put in the toolbar. Change data-export-target to your table id. Needs table-export.js.'); ?>
<div class="dropdown">
  <button class="btn btn-outline-secondary dropdown-toggle w-100" data-bs-toggle="dropdown"><i class="bi bi-download me-1"></i>Export</button>
  <ul class="dropdown-menu dropdown-menu-end">
    <li><a class="dropdown-item" href="#" data-export-as="csv" data-export-target="#dataTable"><i class="bi bi-file-earmark-text me-2"></i>CSV</a></li>
    <li><a class="dropdown-item" href="#" data-export-as="xlsx" data-export-target="#dataTable"><i class="bi bi-file-earmark-excel me-2 text-success"></i>Excel (.xlsx)</a></li>
    <li><a class="dropdown-item" href="#" data-export-as="pdf" data-export-target="#dataTable"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i>PDF</a></li>
    <li><a class="dropdown-item" href="#" data-export-as="doc" data-export-target="#dataTable"><i class="bi bi-file-earmark-word me-2 text-primary"></i>Word (.doc)</a></li>
  </ul>
</div>
<?php kit_close(); ?>

<?php kit_open('table-dtr', 'DTR Table (sortable: date, time, hours, status)', 'Date, time and number columns use data-value so they sort chronologically and numerically.'); ?>
<div class="table-responsive">
  <table class="table table-striped align-middle mb-0 table-sortable">
    <thead class="table-light">
      <tr>
        <th data-sort="date" data-sort-default="desc">Date</th>
        <th data-sort="text">Time In</th>
        <th data-sort="text">Time Out</th>
        <th data-sort="number">Hours</th>
        <th data-sort="text">Status</th>
      </tr>
    </thead>
    <tbody>
      <tr><td data-value="2026-10-01">Oct 01, 2026</td><td data-value="08:02:00">08:02 AM</td><td data-value="17:01:00">05:01 PM</td><td>8.0</td><td><span class="badge bg-warning-subtle text-warning-emphasis">Late</span></td></tr>
      <tr><td data-value="2026-09-30">Sep 30, 2026</td><td data-value="07:55:00">07:55 AM</td><td data-value="17:05:00">05:05 PM</td><td>8.0</td><td><span class="badge bg-success-subtle text-success-emphasis">Present</span></td></tr>
      <tr><td data-value="2026-09-29">Sep 29, 2026</td><td data-value="08:00:00">08:00 AM</td><td data-value="15:30:00">03:30 PM</td><td>6.5</td><td><span class="badge bg-info-subtle text-info-emphasis">Undertime</span></td></tr>
      <tr><td data-value="2026-09-28">Sep 28, 2026</td><td></td><td></td><td></td><td><span class="badge bg-danger-subtle text-danger-emphasis">Absent</span></td></tr>
    </tbody>
  </table>
</div>
<?php kit_close(); ?>

<?php kit_open('list-group', 'List group &amp; description list'); ?>
<div class="row g-3">
  <div class="col-md-6">
    <ul class="list-group">
      <li class="list-group-item d-flex justify-content-between align-items-center">Present <span class="badge text-bg-success rounded-pill">120</span></li>
      <li class="list-group-item d-flex justify-content-between align-items-center">Late <span class="badge text-bg-warning rounded-pill">6</span></li>
      <li class="list-group-item d-flex justify-content-between align-items-center">Absent <span class="badge text-bg-danger rounded-pill">2</span></li>
    </ul>
  </div>
  <div class="col-md-6">
    <dl class="row mb-0">
      <dt class="col-5 text-secondary fw-normal">Employee No.</dt><dd class="col-7">EMP-0001</dd>
      <dt class="col-5 text-secondary fw-normal">Department</dt><dd class="col-7">Engineering</dd>
      <dt class="col-5 text-secondary fw-normal">Date Hired</dt><dd class="col-7">Jan 15, 2024</dd>
    </dl>
  </div>
</div>
<?php kit_close(); ?>

<?php kit_group('Feedback &amp; Overlays'); ?>

<?php kit_open('modal-form', 'Modal: Add / Edit form', 'Use one modal for both add and edit; fill fields when editing.'); ?>
<button class="btn btn-atm" data-bs-toggle="modal" data-bs-target="#kitFormModal"><i class="bi bi-plus-lg me-1"></i>Open form modal</button>
<div class="modal fade" id="kitFormModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <form class="modal-content" id="recordForm" novalidate>
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle">Add Record</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id">
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label">Name <span class="text-danger">*</span></label><input type="text" name="name" class="form-control" required></div>
          <div class="col-md-6"><label class="form-label">Category</label><select name="category" class="form-select"><option>Option 1</option></select></div>
          <div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3"></textarea></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-atm">Save</button>
      </div>
    </form>
  </div>
</div>
<?php kit_close(); ?>

<?php kit_open('modal-confirm', 'Modal: Confirm delete', 'Set the record id on the confirm button before showing.'); ?>
<button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#kitConfirmModal"><i class="bi bi-trash me-1"></i>Open confirm modal</button>
<div class="modal fade" id="kitConfirmModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content text-center">
      <div class="modal-body p-4">
        <div class="text-danger fs-1"><i class="bi bi-exclamation-circle"></i></div>
        <h5 class="mt-2">Delete record?</h5>
        <p class="text-secondary small mb-4">This action cannot be undone.</p>
        <div class="d-flex gap-2 justify-content-center">
          <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-danger" id="btnConfirmDelete" data-id="">Delete</button>
        </div>
      </div>
    </div>
  </div>
</div>
<?php kit_close(); ?>

<?php kit_open('alerts', 'Alerts &amp; Toasts', 'Inline alerts, and Atomicos.toast(message, type) from app.js.'); ?>
<div class="alert alert-success" role="alert"><i class="bi bi-check-circle me-2"></i>Record saved successfully.</div>
<div class="alert alert-danger" role="alert"><i class="bi bi-x-octagon me-2"></i>Something went wrong.</div>
<div class="alert alert-warning alert-dismissible fade show" role="alert"><i class="bi bi-exclamation-triangle me-2"></i>Dismissible warning.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<div class="alert alert-info mb-3" role="alert"><i class="bi bi-info-circle me-2"></i>Helpful information.</div>
<div class="d-flex flex-wrap gap-2">
  <button class="btn btn-sm btn-success" onclick="Atomicos.toast('Saved successfully','success')">Success toast</button>
  <button class="btn btn-sm btn-danger" onclick="Atomicos.toast('Something went wrong','danger')">Error toast</button>
  <button class="btn btn-sm btn-warning" onclick="Atomicos.toast('Please check the form','warning')">Warning toast</button>
</div>
<?php kit_close(); ?>

<?php kit_open('progress', 'Progress &amp; Spinners'); ?>
<div class="progress mb-3" style="height:10px"><div class="progress-bar bg-success" style="width:65%"></div></div>
<div class="progress mb-3"><div class="progress-bar progress-bar-striped progress-bar-animated" style="width:40%">40%</div></div>
<div class="d-flex gap-3 align-items-center">
  <div class="spinner-border text-primary" role="status"></div>
  <div class="spinner-grow text-secondary" role="status"></div>
  <span class="text-secondary">Loading...</span>
</div>
<?php kit_close(); ?>

<?php kit_group('Navigation'); ?>

<?php kit_open('tabs', 'Tabs', 'Great for employee profile (Info, Job History, DTR).'); ?>
<ul class="nav nav-tabs" role="tablist">
  <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-a" type="button">Information</button></li>
  <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-b" type="button">Job History</button></li>
  <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-c" type="button">DTR</button></li>
</ul>
<div class="tab-content border border-top-0 rounded-bottom p-3">
  <div class="tab-pane fade show active" id="tab-a">Information content</div>
  <div class="tab-pane fade" id="tab-b">Job history content</div>
  <div class="tab-pane fade" id="tab-c">DTR content</div>
</div>
<?php kit_close(); ?>

<?php kit_open('accordion', 'Accordion'); ?>
<div class="accordion" id="kitAcc">
  <div class="accordion-item">
    <h2 class="accordion-header"><button class="accordion-button" data-bs-toggle="collapse" data-bs-target="#acc1">Section one</button></h2>
    <div id="acc1" class="accordion-collapse collapse show" data-bs-parent="#kitAcc"><div class="accordion-body">Content one.</div></div>
  </div>
  <div class="accordion-item">
    <h2 class="accordion-header"><button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#acc2">Section two</button></h2>
    <div id="acc2" class="accordion-collapse collapse" data-bs-parent="#kitAcc"><div class="accordion-body">Content two.</div></div>
  </div>
</div>
<?php kit_close(); ?>

<?php kit_open('dropdown-breadcrumb', 'Dropdown &amp; Breadcrumb'); ?>
<nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="#">Home</a></li><li class="breadcrumb-item"><a href="#">Employees</a></li><li class="breadcrumb-item active">Profile</li></ol></nav>
<div class="dropdown">
  <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
  <ul class="dropdown-menu">
    <li><a class="dropdown-item" href="#"><i class="bi bi-pencil me-2"></i>Edit</a></li>
    <li><a class="dropdown-item" href="#"><i class="bi bi-download me-2"></i>Export</a></li>
    <li><hr class="dropdown-divider"></li>
    <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-trash me-2"></i>Delete</a></li>
  </ul>
</div>
<?php kit_close(); ?>

<?php kit_group('Code Recipes (PHP + AJAX)'); ?>

<?php kit_code('recipe-page', 'New Module: page skeleton', 'Save as public/<module>.php. Add it to app/views/layout/sidebar.php.', <<<'CODE'
<?php
require __DIR__ . '/../app/bootstrap.php';
Auth::requireLogin();
Auth::requireRole(['admin', 'hr']);

$pageTitle   = 'Departments';
$active      = 'departments';          // must match the key in sidebar.php
$pageScripts = ['departments.js'];     // loads public/assets/js/departments.js

require APP_PATH . '/views/layout/header.php';
?>

<!-- paste components from this UI Kit here -->

<?php require APP_PATH . '/views/layout/footer.php'; ?>
CODE); ?>

<?php kit_code('recipe-api', 'New Module: API endpoint (list, save, delete)', 'Save as public/api/<module>/save.php. Copy it for list.php and delete.php.', <<<'CODE'
<?php
require __DIR__ . '/../../../app/bootstrap.php';
Auth::requireRole(['admin', 'hr'], true);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::json(false, 'Method not allowed.', [], 405);
}
Csrf::guard();

$id   = (int) ($_POST['id'] ?? 0);
$name = trim($_POST['name'] ?? '');

// 1) Validate
if ($name === '') {
    Response::json(false, 'Name is required.', ['errors' => ['name' => 'Required']], 422);
}

// 2) Insert or update with prepared statements
$db = Database::connect();
try {
    if ($id > 0) {
        $db->prepare('UPDATE departments SET name = :n WHERE id = :id')->execute(['n' => $name, 'id' => $id]);
        Response::json(true, 'Updated successfully.');
    }
    $db->prepare('INSERT INTO departments (name) VALUES (:n)')->execute(['n' => $name]);
    Response::json(true, 'Added successfully.', ['id' => (int) $db->lastInsertId()], 201);
} catch (PDOException $e) {
    $dup = $e->errorInfo[1] === 1062; // duplicate key
    Response::json(false, $dup ? 'That name already exists.' : 'Database error.', [], $dup ? 409 : 500);
}

// list.php  : SELECT ... then Response::json(true, '', ['rows' => $rows]);
// delete.php: UPDATE ... SET status='inactive' WHERE id=:id   (soft delete)
CODE); ?>

<?php kit_code('recipe-js', 'New Module: page JavaScript (load, add/edit, delete)', 'Save as public/assets/js/<module>.js. Matches the Data Table, Modal and Confirm blocks above.', <<<'CODE'
$(function () {
  const modal   = new bootstrap.Modal('#recordModal');
  const confirm = new bootstrap.Modal('#confirmModal');

  // ---- LIST: fetch rows and render the table body ----
  function loadList() {
    Atomicos.post('api/departments/list.php', { search: $('#tableSearch').val() })
      .done(res => {
        const rows = res.data.rows;
        if (!rows.length) {
          return $('#dataTable tbody').html('<tr><td colspan="4" class="text-center text-secondary py-4">No records found</td></tr>');
        }
        $('#dataTable tbody').html(rows.map((r, i) => `
          <tr data-id="${r.id}">
            <td>${i + 1}</td>
            <td>${$('<div>').text(r.name).html()}</td>
            <td><span class="badge text-bg-success">Active</span></td>
            <td class="text-end">
              <button class="btn btn-sm btn-outline-primary btn-edit"><i class="bi bi-pencil"></i></button>
              <button class="btn btn-sm btn-outline-danger btn-delete"><i class="bi bi-trash"></i></button>
            </td>
          </tr>`).join(''));
      });
  }

  // ---- ADD: open empty modal ----
  $('#btnAdd').on('click', () => {
    $('#recordForm')[0].reset();
    $('#recordForm [name=id]').val('');
    $('#modalTitle').text('Add Record');
    modal.show();
  });

  // ---- EDIT: fill the modal from the row ----
  $(document).on('click', '.btn-edit', function () {
    const $tr = $(this).closest('tr');
    $('#recordForm [name=id]').val($tr.data('id'));
    $('#recordForm [name=name]').val($tr.find('td:eq(1)').text());
    $('#modalTitle').text('Edit Record');
    modal.show();
  });

  // ---- SAVE (add or edit) ----
  $('#recordForm').on('submit', function (e) {
    e.preventDefault();
    if (!this.checkValidity()) { $(this).addClass('was-validated'); return; }
    const $btn = $(this).find('[type=submit]').prop('disabled', true);
    Atomicos.post('api/departments/save.php', $(this).serializeArray().reduce((o, f) => (o[f.name] = f.value, o), {}))
      .done(res => { Atomicos.toast(res.message); modal.hide(); loadList(); })
      .always(() => $btn.prop('disabled', false));
  });

  // ---- DELETE with confirmation ----
  $(document).on('click', '.btn-delete', function () {
    $('#btnConfirmDelete').data('id', $(this).closest('tr').data('id'));
    confirm.show();
  });
  $('#btnConfirmDelete').on('click', function () {
    Atomicos.post('api/departments/delete.php', { id: $(this).data('id') })
      .done(res => { Atomicos.toast(res.message); confirm.hide(); loadList(); });
  });

  // ---- SEARCH (debounced) ----
  let t; $('#tableSearch').on('input', () => { clearTimeout(t); t = setTimeout(loadList, 300); });

  loadList();
});
CODE); ?>

<?php kit_code('recipe-sort', 'Sortable table: how to use', 'Works for text, numbers and dates. Needs assets/js/table-sort.js.', <<<'CODE'
// 1) Load the plugin on your page, BEFORE your module script
$pageScripts = ['table-sort.js', 'your-module.js'];

<!-- 2) Mark the table, then every sortable header (no data-sort = not sortable) -->
<table class="table table-hover table-sortable">
  <thead><tr>
    <th>#</th>
    <th data-sort="text"   data-key="name" data-sort-default="asc">Name</th>
    <th data-sort="number" data-key="salary">Salary</th>
    <th data-sort="date"   data-key="date_hired">Date Hired</th>
    <th>Actions</th>
  </tr></thead>
  <tbody>
    <tr>
      <td class="row-num">1</td>                      <!-- renumbered after sorting -->
      <td>Juan Dela Cruz</td>
      <td data-value="45000">&#8369;45,000.00</td>    <!-- data-value = the real number -->
      <td data-value="2024-01-15">Jan 15, 2024</td>   <!-- data-value = ISO date (YYYY-MM-DD) -->
      <td>...</td>
    </tr>
  </tbody>
</table>

NOTES
- data-sort="text" is alphabetical (A-Z, natural order: "EMP-2" before "EMP-10").
- data-sort="number" is numeric (9 before 10). Currency symbols and commas are ignored.
- data-sort="date" is chronological. Always put the ISO date in data-value.
- Times: use data-sort="text" with data-value="08:02:00" (24-hour) so they order correctly.
- Cells with complex content (avatar + name): put the sortable text in data-value.
- Empty cells always go to the bottom, ascending or descending.

// 3) SERVER MODE (for tables loaded from the API, with pagination).
//    Add data-sort-mode="server" to the <table>, then in your module JS:
$('#dataTable').on('atm:sort', function (e, key, dir) {
  state.sort = key;  state.dir = dir;     // key = the th data-key, dir = 'asc' | 'desc'
  loadList(1);                            // send sort + dir to list.php
});
CODE); ?>

<?php kit_code('recipe-export', 'Export: how to use', 'Works with any table. CSV is built in; Excel and PDF libraries load from a CDN only when clicked.', <<<'CODE'
// 1) Load the plugin on your page
$pageScripts = ['table-sort.js', 'table-export.js', 'your-module.js'];

<!-- 2) Add the Export dropdown to your toolbar (see the "Export Button" block above) -->

<!-- 3) Describe the table. data-no-export hides columns; data-sort gives each column its type -->
<table class="table" id="dataTable" data-export-title="Employees" data-export-filename="employees">
  <thead><tr>
    <th data-no-export>#</th>
    <th data-sort="text">Name</th>
    <th data-sort="number">Salary</th>
    <th data-sort="date">Date Hired</th>
    <th data-no-export>Actions</th>
  </tr></thead>
  ...
</table>

CLIENT MODE (static table or all rows loaded): exports every row in the table body.
  Cell value used: data-export-value, else data-value, else the cell text.
  <td data-value="40000" >&#8369;40,000.00</td>          -> exported as the number 40000
  <td data-value="2024-01-15">Jan 15, 2024</td>          -> exported as a real date
  <td data-export-value="Juan Dela Cruz">...avatar + name markup...</td>

SERVER MODE (paginated tables): exports ALL matching records, not only the current page.
  a) Add the API url and a data-key on every exported column:
     <table ... data-export-url="api/positions/export.php">
     <th data-sort="text" data-key="title">Title</th>        (data-key = field name returned by export.php)
  b) Tell the plugin which search/sort is active, in your module JS:
     $('#dataTable').data('exportParams', () => ({
       search: $('#tableSearch').val().trim(),
       sort:   state.sort,
       dir:    state.dir
     }));
  c) Create api/<module>/export.php (copy api/template/export.php).

FORMATS
  CSV   UTF-8 (opens correctly in Excel), numbers stay numeric, text cells are protected against formula injection.
  XLSX  real numbers and real dates, number formats and auto column widths.
  PDF   A4, landscape when more than 6 columns, title, record count, striped rows, "Page x of y".
  DOC   formatted table that opens in Microsoft Word / LibreOffice.
CODE); ?>

<?php
$body = ob_get_clean();
require APP_PATH . '/views/layout/header.php';
?>
<style>
  .kit-group{font-weight:700;margin:34px 0 14px;padding-bottom:8px;border-bottom:2px solid var(--atm-teal);display:inline-block}
  .kit-demo{padding:4px}
  .kit-pre{background:#0B1020;color:#cfe8e2;border-radius:12px;padding:16px;margin:16px 0 0;max-height:440px;overflow:auto;font-size:.8rem;line-height:1.55;white-space:pre}
  .kit-code-only .kit-pre{margin-top:0}
  .kit-index a{display:inline-block;font-size:.8rem;padding:4px 12px;margin:0 6px 6px 0;border:1px solid var(--atm-line);border-radius:99px;background:#fff;color:inherit;text-decoration:none}
  .kit-index a:hover{border-color:var(--atm-teal);color:#087E6B}
</style>

<div class="atm-welcome mb-4">
  <div>
    <h4 class="mb-1">Atomicos UI Kit</h4>
    <p class="mb-0 opacity-75">Scan, click <b>Copy</b>, paste into your module. Click <b>Code</b> to preview the markup first.</p>
  </div>
  <i class="bi bi-palette2 atm-welcome-icon"></i>
</div>

<div class="input-group mb-3">
  <span class="input-group-text"><i class="bi bi-search"></i></span>
  <input type="search" id="kitSearch" class="form-control" placeholder="Search components (e.g. modal, select, table)...">
</div>
<div class="kit-index mb-2">
  <?php foreach ($kitNav as $id => $label): ?><a href="#<?= $id ?>"><?= $label ?></a><?php endforeach; ?>
</div>

<?= $body ?>

<?php require APP_PATH . '/views/layout/footer.php'; ?>
