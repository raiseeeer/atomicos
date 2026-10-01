<?php
require __DIR__ . '/../../../app/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::json(false, 'Method not allowed.', [], 405);
}
Csrf::guard();

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    Response::json(false, 'Please enter your username and password.', [], 422);
}

try {
    if (!Auth::attempt($username, $password)) {
        // Same message for wrong user or wrong password (don't leak which)
        Response::json(false, 'Invalid username or password.', [], 401);
    }
} catch (PDOException $ex) {
    Response::json(false, APP_ENV === 'local' ? $ex->getMessage() : 'Server error.', [], 500);
}

Response::json(true, 'Login successful.', ['redirect' => url('dashboard.php')]);
