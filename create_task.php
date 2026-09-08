<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

if (!csrf_verify($_POST['csrf_token'] ?? null)) {
    flash('error', 'Invalid request. Please try again.');
    redirect('index.php');
}

$title = trim((string) ($_POST['title'] ?? ''));
$start = parse_datetime((string) ($_POST['start_time'] ?? ''));
$end = parse_datetime((string) ($_POST['end_time'] ?? ''));

if ($title === '' || mb_strlen($title) > 255 || !$start || !$end) {
    flash('error', 'Please provide a valid title and start/end time.');
    redirect('index.php');
}

if ($start >= $end) {
    flash('error', 'End time must be after the start time.');
    redirect('index.php');
}

$stmt = $pdo->prepare('INSERT INTO tasks (title, start_time, end_time) VALUES (?, ?, ?)');
$stmt->execute([$title, $start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s')]);

flash('success', 'Task added.');
redirect('index.php');
