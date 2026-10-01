<?php
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__, 2));
define('APP_PATH', BASE_PATH . '/app');

// --- Tiny .env loader (no Composer needed) ---
$envFile = BASE_PATH . '/.env';
if (is_file($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) continue;
        [$key, $value] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

function env(string $key, ?string $default = null): ?string
{
    return $_ENV[$key] ?? $default;
}

define('APP_NAME', env('APP_NAME', 'Atomicos'));
define('APP_ENV', env('APP_ENV', 'local'));
define('APP_URL', rtrim((string) env('APP_URL', ''), '/'));
define('WORK_START', env('WORK_START', '08:00:00'));
define('WORK_END', env('WORK_END', '17:00:00'));

date_default_timezone_set('Asia/Manila');

if (APP_ENV === 'local') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
}
