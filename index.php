<?php include 'db.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Task Scheduler</title>
    <link rel="stylesheet" href="assets/style.css">
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2>Add Task</h2>
        <form action="create_task.php" method="POST" class="row g-3">
            <div class="col-md-6">
                <label for="title" class="form-label">Task Title</label>
                <input type="text" id="title" name="title" class="form-control" placeholder="Enter task title" required>
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
        <div id="calendar">
            <?php
            $stmt = $pdo->query("SELECT * FROM tasks ORDER BY start_time ASC");
            $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($tasks as $task) {
                $start = (new DateTime($task['start_time']))->format('H:i');
                $end = (new DateTime($task['end_time']))->format('H:i');
                $duration = strtotime($task['end_time']) - strtotime($task['start_time']);
            ?>
                <div
                    class="task"
                    draggable="true"
                    ondragstart="drag(event)"
                    ondragend="drop(event)"
                    data-id="<?= $task['id'] ?>"
                    style="top: <?= strtotime($task['start_time']) % 86400 / 3600 * 40 ?>px; height: <?= $duration / 60 ?>px;">
                    <span><?= htmlspecialchars($task['title']) ?> (<?= $start ?> - <?= $end ?>)</span>
                </div>
            <?php } ?>
        </div>
    </div>

    <script src="assets/script.js"></script>
</body>

</html>