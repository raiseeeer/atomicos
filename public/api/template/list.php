<?php
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
