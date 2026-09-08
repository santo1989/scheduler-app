<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$stmt = $pdo->query('SELECT * FROM tasks ORDER BY start_time ASC');
$tasks = $stmt->fetchAll();
$token = csrf_token();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Task Scheduler</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>
    <div class="container mt-5">
        <?php foreach (get_flashes() as $item): ?>
            <div class="alert alert-<?= $item['type'] === 'error' ? 'danger' : 'success' ?>">
                <?= htmlspecialchars($item['message']) ?>
            </div>
        <?php endforeach; ?>

        <h2>Add Task</h2>
        <form action="create_task.php" method="POST" class="row g-3">
            <?= csrf_field() ?>
            <div class="col-md-6">
                <label for="title" class="form-label">Task Title</label>
                <input type="text" id="title" name="title" class="form-control" maxlength="255"
                    placeholder="Enter task title" required>
            </div>
            <div class="col-md-3">
                <label for="start_time" class="form-label">Start Time</label>
                <input type="datetime-local" id="start_time" name="start_time" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label for="end_time" class="form-label">End Time</label>
                <input type="datetime-local" id="end_time" name="end_time" class="form-control" required>
            </div>
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">Add Task</button>
            </div>
        </form>
    </div>

    <div class="container">
        <h1>Task Scheduler</h1>
        <div id="calendar" data-csrf="<?= htmlspecialchars($token) ?>">
            <?php foreach ($tasks as $task): ?>
                <?php
                $startDt = new DateTime($task['start_time']);
                $endDt = new DateTime($task['end_time']);
                $midnight = (clone $startDt)->setTime(0, 0, 0);
                $top = ($startDt->getTimestamp() - $midnight->getTimestamp()) / 3600 * 40;
                $durationMinutes = ($endDt->getTimestamp() - $startDt->getTimestamp()) / 60;
                ?>
                <div class="task" draggable="true" data-id="<?= (int) $task['id'] ?>"
                    style="top: <?= $top ?>px; height: <?= $durationMinutes ?>px;">
                    <span class="task-title">
                        <?= htmlspecialchars($task['title']) ?>
                        (<?= $startDt->format('H:i') ?> - <?= $endDt->format('H:i') ?>)
                    </span>
                    <button type="button" class="btn-close btn-close-white task-delete" aria-label="Delete task"
                        data-id="<?= (int) $task['id'] ?>"></button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="assets/script.js"></script>
</body>

</html>
