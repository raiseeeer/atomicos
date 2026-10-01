<?php
require __DIR__ . '/../app/bootstrap.php';
Auth::requireLogin();

$pageTitle = 'Dashboard';
$active    = 'dashboard';

// TODO: replace with real counts from the database
$stats = [
    ['Total Employees', '0', 'bi-people-fill',     'teal'],
    ['Present Today',   '0', 'bi-check2-circle',   'violet'],
    ['Late Today',      '0', 'bi-alarm-fill',      'amber'],
    ['Absent Today',    '0', 'bi-x-circle-fill',   'rose'],
];

require APP_PATH . '/views/layout/header.php';
?>
<!-- Welcome banner -->
<div class="atm-welcome mb-4">
  <div>
    <h4 class="mb-1">Welcome back, <?= e(Auth::user()['username']) ?> 👋</h4>
    <p class="mb-0 opacity-75">Here is what's happening in your workspace today.</p>
  </div>
  <i class="bi bi-diagram-3 atm-welcome-icon"></i>
</div>

<!-- Stat boxes -->
<div class="row">
  <?php foreach ($stats as [$label, $value, $icon, $tone]): ?>
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="info-box atm-info">
      <span class="info-box-icon atm-<?= $tone ?>"><i class="bi <?= $icon ?>"></i></span>
      <div class="info-box-content">
        <span class="info-box-text text-secondary"><?= e($label) ?></span>
        <span class="info-box-number"><?= e($value) ?></span>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<div class="row">
  <div class="col-lg-8">
    <div class="card atm-panel">
      <div class="card-header"><h3 class="card-title">Today's Attendance</h3></div>
      <div class="card-body text-center py-5 text-secondary">
        <i class="bi bi-calendar2-check fs-1 d-block mb-2"></i>
        No attendance logs yet. Logs will appear here once the DTR module is built.
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card atm-panel">
      <div class="card-header"><h3 class="card-title">Quick Actions</h3></div>
      <div class="card-body d-grid gap-2">
        <a href="<?= url('employees.php') ?>" class="btn btn-atm"><i class="bi bi-person-plus me-2"></i>Add Employee</a>
        <a href="<?= url('positions.php') ?>" class="btn btn-outline-secondary"><i class="bi bi-briefcase me-2"></i>Manage Positions</a>
        <a href="<?= url('dtr.php') ?>" class="btn btn-outline-secondary"><i class="bi bi-clock-history me-2"></i>Open DTR</a>
      </div>
    </div>
  </div>
</div>
<?php require APP_PATH . '/views/layout/footer.php'; ?>
