<?php
/**
 * ============================================================================
 * FILE     : public/api/positions/list.php
 * MODULE   : Positions
 * ACTION   : LIST (read)
 * ----------------------------------------------------------------------------
 * PURPOSE
 *   Returns one page of position records, filtered by search/status and
 *   sorted by the chosen column, plus the totals needed to draw the pager.
 *
 * USE CASE
 *   Positions page: initial load, search, sort, status filter, page change,
 *   page-size change, and reload after add/edit/delete.
 *
 * ACCESS   : admin, hr
 * METHOD   : POST (CSRF token required)
 * TABLES   : positions (read)
 * CALLED BY: public/assets/js/positions.js -> loadList()
 *
 * REQUEST (POST fields)
 *   search    string  no   matches title (partial)
 *   status    int     no   0 = all, 1 = Active, 2 = Inactive
 *   page      int     no   default 1 (corrected if out of range)
 *   per_page  int     no   10 to 100 in steps of 10, default 10
 *   sort      string  no   title | base_salary | created_at | position_status
 *   dir       string  no   asc | desc
 *
 * RESPONSE (JSON)
 *   { success:true, message:"", data:{ rows:[...], total, page, pages } }
 *
 * STATUS CODES
 *   200 OK | 403 Forbidden | 405 Wrong method | 419 Bad CSRF | 500 Server error
 *
 * FLOW
 *   1. Guards: role, method, CSRF
 *   2. Read input; whitelist per_page and sort column
 *   3. Build WHERE once (shared by both queries)
 *   4. Query 1: COUNT(*) of matching rows
 *   5. Page math (pages, clamp page, offset)
 *   6. Query 2: SELECT ... LIMIT/OFFSET for this page
 *   7. Return rows + paging data
 *
 * CHANGELOG
 *   2026-10-02  Created from api/template/list.php
 * ============================================================================
 */

require __DIR__ . '/../../../app/bootstrap.php';

Auth::requireRole(['admin', 'hr'], true);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::json(false, 'Method not allowed.', [], 405);
}
Csrf::guard();

// ---------- 1. INPUT ----------
$search = trim($_POST['search'] ?? '');
$status = (int) ($_POST['status'] ?? 0);                      // 0 = all, 1 = Active, 2 = Inactive
$page   = max(1, (int) ($_POST['page'] ?? 1));

$perPage = (int) ($_POST['per_page'] ?? 10);
if (!in_array($perPage, range(10, 100, 10), true)) {
    $perPage = 10;
}

// sort KEY from the browser -> real column (whitelist: never put raw input in ORDER BY)
$sortable = [
    'title'           => 'title',
    'base_salary'     => 'base_salary',
    'created_at'      => 'created_at',
    'position_status' => 'position_status',
];
$sortCol = $sortable[$_POST['sort'] ?? ''] ?? 'title';
$sortDir = strtolower($_POST['dir'] ?? 'asc') === 'desc' ? 'DESC' : 'ASC';

try {
    $db = Database::connect();

    // ---------- 2. WHERE (built once, used by BOTH queries) ----------
    $conditions = [];
    $params     = [];

    if ($search !== '') {
        $conditions[]     = 'title LIKE :search';
        $params['search'] = '%' . $search . '%';
    }
    if (in_array($status, [1, 2], true)) {
        $conditions[]     = 'position_status = :status';
        $params['status'] = $status;
    }
    $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';


    // ---------- 3. QUERY 1: how many rows match? ----------
    $stmt = $db->prepare("SELECT COUNT(*) FROM positions $where");
    $stmt->execute($params);
    $total = (int) $stmt->fetchColumn();

    // ---------- 4. PAGE MATH ----------
    $pages  = max(1, (int) ceil($total / $perPage));
    $page   = min($page, $pages);                             // keep the page valid
    $offset = ($page - 1) * $perPage;

    // ---------- 5. QUERY 2: only this page's rows ----------
    $stmt = $db->prepare(
        "SELECT id, title, base_salary, position_status, created_at
           FROM positions $where
          ORDER BY $sortCol $sortDir, id ASC
          LIMIT :limit OFFSET :offset"
    );
    foreach ($params as $key => $value) {
        $stmt->bindValue(':' . $key, $value);
    }
    $stmt->bindValue(':limit',  $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset,  PDO::PARAM_INT);
    $stmt->execute();

    Response::json(true, '', [
        'rows'  => $stmt->fetchAll(),
        'total' => $total,
        'page'  => $page,
        'pages' => $pages,
    ]);

} catch (PDOException $e) {
    error_log($e->getMessage());
    Response::json(false, APP_ENV === 'local' ? $e->getMessage() : 'Database error.', [], 500);
}