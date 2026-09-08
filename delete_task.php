<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

require_csrf_header();

$data = json_decode(file_get_contents('php://input'), true) ?: [];
$id = isset($data['id']) ? (int) $data['id'] : 0;

if ($id <= 0) {
    json_response(['success' => false, 'error' => 'Invalid task id'], 400);
}

$stmt = $pdo->prepare('DELETE FROM tasks WHERE id = ?');
$stmt->execute([$id]);

json_response(['success' => $stmt->rowCount() > 0]);
