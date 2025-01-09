<?php
include 'db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['id'], $data['newStartTime'])) {
    $id = $data['id'];
    $newStartTime = (int)$data['newStartTime'];

    // Fetch the task
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
    $stmt->execute([$id]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($task) {
        $newStart = date('Y-m-d H:i:s', strtotime($task['start_time']) + $newStartTime);
        $newEnd = date('Y-m-d H:i:s', strtotime($newStart) + (strtotime($task['end_time']) - strtotime($task['start_time'])));

        // Update task time
        $stmt = $pdo->prepare("UPDATE tasks SET start_time = ?, end_time = ? WHERE id = ?");
        $stmt->execute([$newStart, $newEnd, $id]);

        // Reschedule subsequent tasks
        $stmt = $pdo->prepare("UPDATE tasks SET start_time = DATE_ADD(?, INTERVAL 1 SECOND) WHERE id > ?");
        $stmt->execute([$newEnd, $id]);

        echo json_encode(['success' => true]);
        exit;
    }
}

echo json_encode(['success' => false]);
