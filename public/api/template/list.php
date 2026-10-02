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



/**
 * LIST: returns rows with search + pagination.
 *
 * Request  (POST): search, page, per_page
 * Response (JSON): { success, message, data: { rows[], total, page, pages } }
 *
 * HOW TO REUSE: change the 4 spots marked  >>> CHANGE <<<  (table, roles, search column, columns).
 */
require __DIR__ . '/../../../app/bootstrap.php';

// ---------- 1. GUARDS (same 3 lines in every endpoint) ----------
Auth::requireRole(['admin', 'hr'], true);                     // >>> CHANGE <<< who may call this
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::json(false, 'Method not allowed.', [], 405);
}
Csrf::guard();                                                // blocks forged requests

// ---------- 2. READ INPUT ----------
$search  = trim($_POST['search'] ?? '');
$perPage = min(100, max(1, (int) ($_POST['per_page'] ?? 10))); // between 1 and 100
$page    = max(1, (int) ($_POST['page'] ?? 1));

try {
    $db = Database::connect();

    // ---------- 3. BUILD THE WHERE CLAUSE ----------
    $where  = '';
    $params = [];
    if ($search !== '') {
        $where = 'WHERE title LIKE :search';                  // >>> CHANGE <<< column(s) to search
        $params['search'] = '%' . $search . '%';
    }

    // ---------- 4. COUNT, then clamp the page ----------
    $stmt = $db->prepare("SELECT COUNT(*) FROM positions $where"); // >>> CHANGE <<< table
    $stmt->execute($params);
    $total  = (int) $stmt->fetchColumn();
    $pages  = max(1, (int) ceil($total / $perPage));
    $page   = min($page, $pages);                             // e.g. deleted the last row of the last page
    $offset = ($page - 1) * $perPage;

    // ---------- 5. FETCH THE ROWS ----------
    $stmt = $db->prepare(
        "SELECT id, title, base_salary, created_at            -- >>> CHANGE <<< columns
           FROM positions $where
          ORDER BY title ASC
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
    error_log($e->getMessage());                              // full error goes to the log
    Response::json(false, APP_ENV === 'local' ? $e->getMessage() : 'Database error.', [], 500);
}
