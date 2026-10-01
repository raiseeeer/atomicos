# Atomicos — HR System (v0.1 template)

Native PHP 8 · MySQL · Bootstrap 5 · jQuery/AJAX. The HR module is the first "atom" of a future ERP.

## Folder structure

```
atomicos/
├─ app/                  PHP logic (NOT web-accessible)
│  ├─ config/            config.php, database.php
│  ├─ core/              Database, Auth, Csrf, Response, helpers
│  ├─ views/layout/      header, sidebar, footer (shared page shell)
│  └─ bootstrap.php      loaded first by every page and API
├─ public/               WEB ROOT: only this folder is served
│  ├─ api/               JSON endpoints (auth/ done; add employees/, positions/, dtr/)
│  ├─ assets/            css/, js/, img/
│  └─ *.php              pages (index = login, dashboard, employees, ...)
├─ database/             schema.sql, seed.sql
├─ .env.example          copy to .env
└─ README.md
```

Why `public/`? Config, DB credentials, and core code sit outside the web root, so a browser can never open them.

## Setup (XAMPP / Laragon / any PHP 8+ server)

1. Put the `atomicos` folder in `htdocs` (XAMPP) or `www` (Laragon).
2. In phpMyAdmin, import `database/schema.sql`, then `database/seed.sql`.
3. Copy `.env.example` to `.env` and set your DB credentials.
   - `APP_URL=/atomicos/public` for `http://localhost/atomicos/public`.
   - Or run `php -S localhost:8000 -t public` from the project root with `APP_URL=` (empty).
4. Sign in with **admin / Admin@123**, then change the password.

## How to add a feature (the pattern)

1. **Page:** `public/employees.php` renders the shell, table, and modal.
2. **Script:** `public/assets/js/employees.js` calls `Atomicos.post('api/employees/save.php', data)`.
3. **API:** `public/api/employees/save.php` → `Csrf::guard()` → `Auth::requireRole([...], true)` → validate → PDO prepared statement → `Response::json(...)`.

## Conventions

- PDO prepared statements always; escape printed data with `e()`.
- Every POST endpoint calls `Csrf::guard()`.
- Protected pages call `Auth::requireLogin()`; restricted ones add `Auth::requireRole([...])`.
- JSON shape: `{"success": true, "message": "", "data": {}}`.
- Soft delete employees (`status = inactive`).

## Roadmap

- [x] Phase 1: Setup, layout, theme
- [x] Phase 2: Login / Logout, sessions, roles
- [ ] Phase 3: Departments and Positions CRUD
- [ ] Phase 4: Employee CRUD and job history
- [ ] Phase 5: Daily Time Record (time in/out)
- [ ] Phase 6: Dashboard stats, polish, security review

## Brand

Ink `#0B1020` · Teal `#2DE2C0` · Violet `#6C5CE7` · Amber `#FFB547` · Rose `#EF476F`
Fonts: Space Grotesk (headings), Inter (body). Logos live in `public/assets/img/`.
