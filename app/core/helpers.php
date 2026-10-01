<?php
declare(strict_types=1);

// Build a URL relative to the /public folder
function url(string $path = ''): string
{
    return APP_URL . '/' . ltrim($path, '/');
}

// Escape output (always use when printing user data)
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
