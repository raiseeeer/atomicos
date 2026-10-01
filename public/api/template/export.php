<?php
/**
 * EXPORT: returns ALL records that match the current search + sort (no pagination),
 * so the browser can build a CSV / Excel / PDF / Word file with every record, not only page 1.
 *
 * Request  (POST): search, sort, dir
 * Response (JSON): { success, data: { rows[], total, limit } }
 *
 * HOW TO REUSE: copy the table, WHERE and $sortable list from this module's list.php (keep them in sync).
 */
require __DIR__ . '/../../../app/bootstrap.php';

// ---------- 1. GUARDS ----------
Auth::requireRole(['admin', 'hr'], true);                     // >>> CHANGE <<<
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::json(false, 'Method not allowed.', [], 405);
}
Csrf::guard();

// ---------- 2. INPUT (same rules as list.php) ----------
$limit  = 5000;                                               // safety cap so a huge table can't freeze the server
$search = trim($_POST['search'] ?? '');

$sortable = [                                                 // >>> CHANGE <<< must match list.php
    'title'       => 'title',
    'base_salary' => 'base_salary',
    'created_at'  => 'created_at',
];
$sortCol = $sortable[$_POST['sort'] ?? ''] ?? 'title';
$sortDir = strtolower($_POST['dir'] ?? 'asc') === 'desc' ? 'DESC' : 'ASC';

try {
    $db = Database::connect();

    $where  = '';
    $params = [];
    if ($search !== '') {
        $where = 'WHERE title LIKE :search';                  // >>> CHANGE <<< must match list.php
        $params['search'] = '%' . $search . '%';
    }

    $stmt = $db->prepare("SELECT COUNT(*) FROM positions $where");               // >>> CHANGE <<< table
    $stmt->execute($params);
    $total = (int) $stmt->fetchColumn();

    $stmt = $db->prepare(
        "SELECT title, base_salary, created_at                -- >>> CHANGE <<< the field names must match the th data-key values
           FROM positions $where
          ORDER BY $sortCol $sortDir, id ASC
          LIMIT $limit"                                       // safe: $limit is a fixed integer set above
    );
    $stmt->execute($params);

    Response::json(true, '', ['rows' => $stmt->fetchAll(), 'total' => $total, 'limit' => $limit]);

} catch (PDOException $e) {
    error_log($e->getMessage());
    Response::json(false, APP_ENV === 'local' ? $e->getMessage() : 'Database error.', [], 500);
}
