<?php

declare(strict_types=1);

/**
 * Reads a config value from the environment, falling back to .env (not committed)
 * then to $default. .env format: KEY=VALUE, one per line, # for comments.
 */
function env(string $key, $default = null)
{
    static $loaded = false;

    if (!$loaded) {
        $envFile = __DIR__ . '/.env';
        if (is_file($envFile)) {
            foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                    continue;
                }
                [$k, $v] = explode('=', $line, 2);
                putenv(trim($k) . '=' . trim($v));
            }
        }
        $loaded = true;
    }

    $value = getenv($key);

    return $value === false ? $default : $value;
}

define('APP_ENV', env('APP_ENV', 'production'));

error_reporting(E_ALL);
ini_set('display_errors', APP_ENV === 'development' ? '1' : '0');
ini_set('log_errors', '1');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: no-referrer-when-downgrade');
