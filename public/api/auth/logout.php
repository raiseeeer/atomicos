<?php
require __DIR__ . '/../../../app/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::json(false, 'Method not allowed.', [], 405);
}
Csrf::guard();
Auth::logout();

Response::json(true, 'Logged out.', ['redirect' => url('index.php')]);
