<?php
declare(strict_types=1);

// Every page and API file starts with: require __DIR__ . '/../app/bootstrap.php';
require __DIR__ . '/config/config.php';
require APP_PATH . '/core/Database.php';
require APP_PATH . '/core/Response.php';
require APP_PATH . '/core/Csrf.php';
require APP_PATH . '/core/Auth.php';
require APP_PATH . '/core/helpers.php';

session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Lax',
    'secure'   => !empty($_SERVER['HTTPS']),
]);
session_start();
