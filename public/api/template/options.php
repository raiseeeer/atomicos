<?php
/**
 * OPTIONS: tiny list (id + label) used to fill <select> dropdowns in OTHER modules.
 * Example: the Employee form's "Position" dropdown calls api/positions/options.php.
 *
 * Request  (POST): none
 * Response (JSON): { success, data: { options: [ {id, label}, ... ] } }
 *
 * HOW TO REUSE: change the table and the label column.
 */
require __DIR__ . '/../../../app/bootstrap.php';

// Any logged-in user may load dropdown options
Auth::requireRole(['admin', 'hr', 'employee'], true);         // >>> CHANGE <<<
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::json(false, 'Method not allowed.', [], 405);
}
Csrf::guard();

try {
    $stmt = Database::connect()->query(
        'SELECT id, title AS label FROM positions ORDER BY title ASC'          // >>> CHANGE <<<
    );
    Response::json(true, '', ['options' => $stmt->fetchAll()]);

} catch (PDOException $e) {
    error_log($e->getMessage());
    Response::json(false, APP_ENV === 'local' ? $e->getMessage() : 'Database error.', [], 500);
}
