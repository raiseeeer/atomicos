<?php
require __DIR__ . '/../app/bootstrap.php';

if (Auth::check()) {
    header('Location: ' . url('dashboard.php'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="<?= e(Csrf::token()) ?>">
  <meta name="app-url" content="<?= e(APP_URL) ?>">
  <title>Sign in · <?= e(APP_NAME) ?></title>
  <link rel="icon" type="image/svg+xml" href="<?= url('assets/img/favicon.svg') ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link href="<?= url('assets/css/login.css') ?>" rel="stylesheet">
</head>
<body class="lg-body">
  <canvas id="lgCanvas" aria-hidden="true"></canvas>
  <div class="lg-glow lg-glow-1"></div>
  <div class="lg-glow lg-glow-2"></div>

  <main class="lg-stage">

    <!-- Left: brand message (desktop only) -->
    <section class="lg-hero">
      <div class="lg-hero-brand">
        <svg viewBox="0 0 100 100" width="44" height="44" aria-hidden="true">
          <rect width="100" height="100" rx="26" fill="#0B1020" stroke="rgba(255,255,255,.15)" stroke-width="2"/>
          <path d="M25 77 L50 25 L75 77" fill="none" stroke="#2DE2C0" stroke-width="10" stroke-linecap="round" stroke-linejoin="round"/>
          <circle cx="50" cy="62" r="7.5" fill="#2DE2C0"/><circle cx="50" cy="25" r="5" fill="#FFB547"/>
        </svg>
        <span>atomicos</span>
      </div>
      <h2 class="lg-headline">Every great team<br>starts with <span class="lg-rotator" id="lgRotator">people</span>.</h2>
      <p class="lg-sub">Employee records, job history, and daily time logs. One clean workspace for your HR.</p>
      <ul class="lg-chips">
        <li style="--d:.9s"><i class="bi bi-people"></i>Employee records</li>
        <li style="--d:1.05s"><i class="bi bi-diagram-3"></i>Job &amp; position history</li>
        <li style="--d:1.2s"><i class="bi bi-clock-history"></i>Daily time records</li>
      </ul>
    </section>

    <!-- Right: login card -->
    <section class="lg-card" id="lgCard">
      <svg class="lg-logo" viewBox="0 0 100 100" width="72" height="72" aria-label="Atomicos">
        <defs><linearGradient id="lgG" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#2DE2C0"/><stop offset="1" stop-color="#8B7DFF"/></linearGradient></defs>
        <rect width="100" height="100" rx="26" fill="#0B1020" stroke="rgba(255,255,255,.18)" stroke-width="2"/>
        <path class="lg-a" d="M25 77 L50 25 L75 77" pathLength="100" fill="none" stroke="url(#lgG)" stroke-width="10" stroke-linecap="round" stroke-linejoin="round"/>
        <circle class="lg-nucleus" cx="50" cy="62" r="7.5" fill="#2DE2C0"/>
        <circle class="lg-pulse" cx="50" cy="25" r="5" fill="none" stroke="#FFB547" stroke-width="2"/>
        <circle class="lg-electron" cx="50" cy="25" r="5" fill="#FFB547"/>
      </svg>

      <h1 class="lg-title lg-rise" style="--d:.55s">Welcome back</h1>
      <p class="lg-muted lg-rise" style="--d:.65s">Sign in to your Atomicos workspace</p>

      <div id="loginAlert" class="lg-alert d-none" role="alert"></div>

      <form id="loginForm" class="lg-form" novalidate>
        <div class="form-floating lg-rise" style="--d:.75s">
          <input type="text" class="form-control" id="username" name="username" placeholder="Username" autocomplete="username" autofocus required>
          <label for="username"><i class="bi bi-person me-1"></i>Username</label>
        </div>
        <div class="form-floating lg-rise" style="--d:.85s">
          <input type="password" class="form-control" id="password" name="password" placeholder="Password" autocomplete="current-password" required>
          <label for="password"><i class="bi bi-lock me-1"></i>Password</label>
          <button class="lg-eye" type="button" id="togglePw" tabindex="-1" aria-label="Show password"><i class="bi bi-eye"></i></button>
        </div>
        <button class="lg-btn lg-rise" style="--d:.95s" type="submit" id="btnLogin">
          <span class="lg-btn-text">Sign in</span><i class="bi bi-arrow-right"></i>
        </button>
      </form>

      <p class="lg-foot lg-rise" style="--d:1.1s">&copy; <?= date('Y') ?> Atomicos · HR Module</p>
    </section>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
  <script src="<?= url('assets/js/app.js') ?>"></script>
  <script src="<?= url('assets/js/login.js') ?>"></script>
</body>
</html>
