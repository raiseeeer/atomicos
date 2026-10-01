<?php
/** Expects: $pageTitle (string), $active (string nav key) */
$user     = Auth::user();
$initials = strtoupper(substr($user['username'] ?? 'U', 0, 2));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="<?= e(Csrf::token()) ?>">
  <meta name="app-url" content="<?= e(APP_URL) ?>">
  <title><?= e($pageTitle ?? 'Dashboard') ?> · <?= e(APP_NAME) ?></title>
  <link rel="icon" type="image/svg+xml" href="<?= url('assets/img/favicon.svg') ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0/dist/css/adminlte.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link href="<?= url('assets/css/atomicos.css') ?>" rel="stylesheet">
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
<div class="app-wrapper">

  <!-- Top navbar -->
  <nav class="app-header navbar navbar-expand bg-body">
    <div class="container-fluid">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button" aria-label="Toggle sidebar"><i class="bi bi-list fs-4"></i></a>
        </li>
      </ul>
      <ul class="navbar-nav ms-auto align-items-center">
        <li class="nav-item d-none d-md-block me-3">
          <span class="atm-clock"><i class="bi bi-clock me-1"></i><span id="atmClock"></span></span>
        </li>
        <li class="nav-item dropdown user-menu">
          <a href="#" class="nav-link dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
            <span class="atm-avatar"><?= e($initials) ?></span>
            <span class="d-none d-md-inline"><?= e($user['username'] ?? '') ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
            <li class="atm-user-head">
              <span class="atm-avatar atm-avatar-lg"><?= e($initials) ?></span>
              <p class="mb-0 fw-semibold"><?= e($user['username'] ?? '') ?></p>
              <small class="text-uppercase text-secondary"><?= e($user['role'] ?? '') ?></small>
            </li>
            <li><hr class="dropdown-divider my-0"></li>
            <li class="p-2"><a href="#" id="btnLogout" class="btn btn-outline-danger w-100"><i class="bi bi-box-arrow-right me-1"></i>Sign out</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </nav>

  <?php require __DIR__ . '/sidebar.php'; ?>

  <main class="app-main">
    <div class="app-content-header">
      <div class="container-fluid">
        <div class="row align-items-center">
          <div class="col-sm-6"><h3 class="mb-0 atm-page-title"><?= e($pageTitle ?? '') ?></h3></div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-end mb-0">
              <li class="breadcrumb-item"><a href="<?= url('dashboard.php') ?>">Atomicos</a></li>
              <li class="breadcrumb-item active" aria-current="page"><?= e($pageTitle ?? '') ?></li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    <div class="app-content">
      <div class="container-fluid">
