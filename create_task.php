<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars(strip_tags($_POST['title']));
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    // Validate input
    if (!empty($title) && !empty($start_time) && !empty($end_time)) {
        if (strtotime($start_time) < strtotime($end_time)) {
            // Insert into database
            $stmt = $pdo->prepare("INSERT INTO tasks (title, start_time, end_time) VALUES (?, ?, ?)");
            $stmt->execute([$title, $start_time, $end_time]);

            header('Location: index.php');
            exit;
        } else {
            echo "End time must be after the start time.";
        }
    } else {
        echo "All fields are required.";
    }
} else {
    header('Location: index.php');
    exit;
}
