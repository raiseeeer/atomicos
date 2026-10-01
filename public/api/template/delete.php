<?php
/**
 * DELETE: removes one record, but only if nothing else is using it.
 *
 * Request  (POST): id
 * Response (JSON): { success, message }
 *
 * HOW TO REUSE: change the spots marked  >>> CHANGE <<<.
 * TIP: for records you must never lose (e.g. employees) don't DELETE:
 *      run  UPDATE employees SET status = 'inactive' WHERE id = :id   (soft delete).
 */
require __DIR__ . '/../../../app/bootstrap.php';

// ---------- 1. GUARDS ----------
Auth::requireRole(['admin', 'hr'], true);                     // >>> CHANGE <<<
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::json(false, 'Method not allowed.', [], 405);
}
Csrf::guard();

// ---------- 2. READ INPUT ----------
$id = (int) ($_POST['id'] ?? 0);
if ($id <= 0) {
    Response::json(false, 'Invalid record.', [], 422);
}

try {
    $db = Database::connect();

    // ---------- 3. DOES IT EXIST? ----------
    $stmt = $db->prepare('SELECT title FROM positions WHERE id = :id');       // >>> CHANGE <<<
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch();
    if (!$row) {
        Response::json(false, 'Record not found.', [], 404);
    }

    // ---------- 4. IS IT IN USE? (protect related data) ----------
    $stmt = $db->prepare('SELECT COUNT(*) FROM employee_jobs WHERE position_id = :id'); // >>> CHANGE <<< child table + column
    $stmt->execute(['id' => $id]);
    $inUse = (int) $stmt->fetchColumn();
    if ($inUse > 0) {
        Response::json(false, "Cannot delete \"{$row['title']}\": it is assigned to {$inUse} employee record(s).", [], 409);
    }

    // ---------- 5. DELETE ----------
    $stmt = $db->prepare('DELETE FROM positions WHERE id = :id');
    $stmt->execute(['id' => $id]);
    Response::json(true, 'Deleted successfully.');

} catch (PDOException $e) {
    // 1451 = a foreign key still points to this row (safety net)
    if (($e->errorInfo[1] ?? 0) === 1451) {
        Response::json(false, 'Cannot delete: this record is still in use.', [], 409);
    }
    error_log($e->getMessage());
    Response::json(false, APP_ENV === 'local' ? $e->getMessage() : 'Database error.', [], 500);
}
