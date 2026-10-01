<?php
/**
 * SAVE: one endpoint for both ADD and EDIT.
 *   - id missing or 0  -> INSERT (add)
 *   - id > 0           -> UPDATE (edit)
 *
 * Request  (POST): id, title, base_salary
 * Response (JSON): { success, message, data }  (422 returns data.errors = { field: "message" })
 *
 * HOW TO REUSE: change the spots marked  >>> CHANGE <<<  (roles, fields, validation, SQL).
 */
require __DIR__ . '/../../../app/bootstrap.php';

// ---------- 1. GUARDS ----------
Auth::requireRole(['admin', 'hr'], true);                     // >>> CHANGE <<<
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::json(false, 'Method not allowed.', [], 405);
}
Csrf::guard();

// ---------- 2. READ + CLEAN INPUT ----------
$id         = (int) ($_POST['id'] ?? 0);
$title      = trim($_POST['title'] ?? '');                    // >>> CHANGE <<< your fields
$salaryRaw  = trim($_POST['base_salary'] ?? '');

// ---------- 3. VALIDATE (collect all errors, then answer once) ----------
$errors = [];

if ($title === '') {
    $errors['title'] = 'Title is required.';
} elseif (mb_strlen($title) > 100) {
    $errors['title'] = 'Title must be 100 characters or less.';
}

if ($salaryRaw === '') {
    $baseSalary = 0;
} elseif (!is_numeric($salaryRaw) || (float) $salaryRaw < 0) {
    $errors['base_salary'] = 'Base salary must be a positive number.';
} else {
    $baseSalary = round((float) $salaryRaw, 2);
}

if ($errors) {
    Response::json(false, 'Please fix the highlighted fields.', ['errors' => $errors], 422);
}

// ---------- 4. SAVE ----------
try {
    $db = Database::connect();

    if ($id > 0) {
        // EDIT: make sure the record exists first
        $check = $db->prepare('SELECT id FROM positions WHERE id = :id');     // >>> CHANGE <<< table
        $check->execute(['id' => $id]);
        if (!$check->fetch()) {
            Response::json(false, 'Record not found.', [], 404);
        }

        $stmt = $db->prepare('UPDATE positions SET title = :title, base_salary = :salary WHERE id = :id');
        $stmt->execute(['title' => $title, 'salary' => $baseSalary, 'id' => $id]);
        Response::json(true, 'Updated successfully.');
    }

    // ADD
    $stmt = $db->prepare('INSERT INTO positions (title, base_salary) VALUES (:title, :salary)');
    $stmt->execute(['title' => $title, 'salary' => $baseSalary]);
    Response::json(true, 'Added successfully.', ['id' => (int) $db->lastInsertId()], 201);

} catch (PDOException $e) {
    // 1062 = duplicate value in a UNIQUE column (positions.title is UNIQUE)
    if (($e->errorInfo[1] ?? 0) === 1062) {
        Response::json(false, 'Please fix the highlighted fields.', ['errors' => ['title' => 'This title already exists.']], 422);
    }
    error_log($e->getMessage());
    Response::json(false, APP_ENV === 'local' ? $e->getMessage() : 'Database error.', [], 500);
}
