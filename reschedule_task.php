<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

require_csrf_header();

$data = json_decode(file_get_contents('php://input'), true) ?: [];
$id = isset($data['id']) ? (int) $data['id'] : 0;
$deltaSeconds = isset($data['newStartTime']) ? (int) $data['newStartTime'] : null;

if ($id <= 0 || $deltaSeconds === null) {
    json_response(['success' => false, 'error' => 'Invalid input'], 400);
}

$stmt = $pdo->prepare('SELECT * FROM tasks WHERE id = ?');
$stmt->execute([$id]);
$task = $stmt->fetch();

if (!$task) {
    json_response(['success' => false, 'error' => 'Task not found'], 404);
}

$duration = strtotime($task['end_time']) - strtotime($task['start_time']);
$newStart = (new DateTime($task['start_time']))->modify("{$deltaSeconds} seconds");
$newEnd = (clone $newStart)->modify("{$duration} seconds");

$pdo->beginTransaction();
try {
    $update = $pdo->prepare('UPDATE tasks SET start_time = ?, end_time = ? WHERE id = ?');
    $update->execute([$newStart->format('Y-m-d H:i:s'), $newEnd->format('Y-m-d H:i:s'), $id]);

    // Push any subsequent tasks forward so they no longer overlap, preserving each one's own duration.
    $stmt = $pdo->prepare('SELECT id, start_time, end_time FROM tasks WHERE id > ? ORDER BY start_time ASC');
    $stmt->execute([$id]);
    $subsequent = $stmt->fetchAll();

    $cursor = clone $newEnd;
    foreach ($subsequent as $next) {
        $nextDuration = strtotime($next['end_time']) - strtotime($next['start_time']);
        $cursor->modify('+1 second');
        $nextStart = clone $cursor;
        $cursor->modify("+{$nextDuration} seconds");
        $nextEnd = clone $cursor;

        $update->execute([$nextStart->format('Y-m-d H:i:s'), $nextEnd->format('Y-m-d H:i:s'), $next['id']]);
    }

    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
    error_log('Reschedule failed: ' . $e->getMessage());
    json_response(['success' => false, 'error' => 'Server error'], 500);
}

json_response(['success' => true]);
