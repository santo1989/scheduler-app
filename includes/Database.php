<?php

declare(strict_types=1);

final class Database
{
    private static ?PDO $instance = null;

    public static function connection(): PDO
    {
        if (self::$instance === null) {
            $host = env('DB_HOST', 'localhost');
            $name = env('DB_NAME', 'scheduler_app');
            $user = env('DB_USER', 'root');
            $pass = env('DB_PASS', '');

            try {
                self::$instance = new PDO(
                    "mysql:host=$host;dbname=$name;charset=utf8mb4",
                    $user,
                    $pass,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );
            } catch (PDOException $e) {
                error_log('Database connection failed: ' . $e->getMessage());
                http_response_code(500);
                die('A server error occurred. Please try again later.');
            }
        }

        return self::$instance;
    }
}
