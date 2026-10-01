<?php
// key => [label, icon, href, allowed roles]
$nav = [
    'MAIN' => [
        'dashboard' => ['Dashboard', 'bi-speedometer2', 'dashboard.php', ['admin', 'hr', 'employee']],
    ],
    'HUMAN RESOURCES' => [
        'employees' => ['Employees',         'bi-people-fill',    'employees.php', ['admin', 'hr']],
        'positions' => ['Positions',         'bi-briefcase-fill', 'positions.php', ['admin', 'hr']],
        'dtr'       => ['Daily Time Record', 'bi-clock-history',  'dtr.php',       ['admin', 'hr', 'employee']],
    ],
    'DEVELOPER' => [
        'uikit'     => ['UI Kit', 'bi-palette2', 'ui-kit.php', ['admin']],
    ],
];
$role = Auth::user()['role'] ?? '';
?>
<aside class="app-sidebar atm-sidebar shadow" data-bs-theme="dark">
  <div class="sidebar-brand">
    <a href="<?= url('dashboard.php') ?>" class="brand-link">
      <img src="<?= url('assets/img/logo.svg') ?>" alt="Atomicos" class="brand-image rounded-3">
      <span class="brand-text">atomicos</span>
    </a>
  </div>
  <div class="sidebar-wrapper">
    <nav class="mt-2">
      <ul class="nav sidebar-menu flex-column" role="menu">
        <?php foreach ($nav as $group => $items): ?>
          <?php
            $visible = array_filter($items, fn($i) => in_array($role, $i[3], true));
            if (!$visible) continue;
          ?>
          <li class="nav-header"><?= e($group) ?></li>
          <?php foreach ($visible as $key => [$label, $icon, $href]): ?>
            <li class="nav-item">
              <a href="<?= url($href) ?>" class="nav-link <?= ($active ?? '') === $key ? 'active' : '' ?>">
                <i class="nav-icon bi <?= $icon ?>"></i>
                <p><?= e($label) ?></p>
              </a>
            </li>
          <?php endforeach; ?>
        <?php endforeach; ?>
      </ul>
    </nav>
  </div>
  <div class="atm-sidebar-foot">HR Module · v0.1</div>
</aside>
