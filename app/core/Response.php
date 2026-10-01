<?php
declare(strict_types=1);

final class Response
{
    // Standard JSON shape for every API: {"success":bool,"message":"","data":{}}
    public static function json(bool $success, string $message = '', array $data = [], int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => $success, 'message' => $message, 'data' => $data]);
        exit;
    }
}
