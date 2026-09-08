<?php

declare(strict_types=1);

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function get_flashes(): array
{
    $flashes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);

    return $flashes;
}

/** Accepts the datetime-local format (Y-m-d\TH:i) or full Y-m-d H:i:s. */
function parse_datetime(string $value): ?DateTime
{
    $value = trim($value);
    $dt = DateTime::createFromFormat('Y-m-d\TH:i', $value)
        ?: DateTime::createFromFormat('Y-m-d H:i:s', $value);

    return $dt ?: null;
}

function json_response(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($payload);
    exit;
}

function require_csrf_header(): void
{
    $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!csrf_verify($token)) {
        json_response(['success' => false, 'error' => 'Invalid CSRF token'], 403);
    }
}
