-- init.sql
CREATE DATABASE IF NOT EXISTS scheduler_app
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE scheduler_app;

CREATE TABLE IF NOT EXISTS tasks (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_start_time (start_time),
    KEY idx_end_time (end_time)
) ENGINE=InnoDB;
