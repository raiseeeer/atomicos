<?php
require __DIR__ . '/../app/bootstrap.php';
Auth::requireLogin();
Auth::requireRole(['admin', 'hr']);

$pageTitle   = 'Positions';
$active      = 'positions';
$pageScripts = ['positions.js'];

require APP_PATH . '/views/layout/header.php';
?>
<div class="atm-card">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h6 mb-0">Positions</h2>
    <button class="btn btn-atm btn-sm" disabled><i class="bi bi-plus-lg me-1"></i>Add</button>
  </div>
  <p class="text-secondary mb-0">Placeholder. Build the table, modal form, and AJAX calls here (see <code>assets/js/positions.js</code>).</p>
</div>
<?php require APP_PATH . '/views/layout/footer.php'; ?>
