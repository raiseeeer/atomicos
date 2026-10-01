<?php
declare(strict_types=1);

final class Auth
{
    public static function attempt(string $username, string $password): bool
    {
        $stmt = Database::connect()->prepare(
            'SELECT id, employee_id, username, password_hash, role
               FROM users WHERE username = :u AND is_active = 1 LIMIT 1'
        );
        $stmt->execute(['u' => $username]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return false;
        }

        session_regenerate_id(true); // prevent session fixation
        $_SESSION['user'] = [
            'id'          => (int) $user['id'],
            'employee_id' => $user['employee_id'] ? (int) $user['employee_id'] : null,
            'username'    => $user['username'],
            'role'        => $user['role'],
        ];

        Database::connect()
            ->prepare('UPDATE users SET last_login_at = NOW() WHERE id = :id')
            ->execute(['id' => $user['id']]);

        return true;
    }

    public static function check(): bool
    {
        return isset($_SESSION['user']);
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    // For pages: redirect to login when not signed in
    public static function requireLogin(): void
    {
        if (!self::check()) {
            header('Location: ' . url('index.php'));
            exit;
        }
    }

    // For pages and APIs: limit by role, e.g. Auth::requireRole(['admin', 'hr'])
    public static function requireRole(array $roles, bool $isApi = false): void
    {
        if (!self::check() || !in_array($_SESSION['user']['role'], $roles, true)) {
            if ($isApi) {
                Response::json(false, 'Access denied.', [], 403);
            }
            http_response_code(403);
            exit('403 - Access denied.');
        }
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 3600, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }
}
