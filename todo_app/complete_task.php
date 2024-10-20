<?php
session_start();
require 'db/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if (isset($_GET['task_id']) && isset($_GET['status'])) {
    $taskId = filter_input(INPUT_GET, 'task_id', FILTER_SANITIZE_NUMBER_INT);
    $status = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_NUMBER_INT);

    if ($taskId !== false && $status !== false) {
        $stmt = $conn->prepare("UPDATE tasks SET completed = ? WHERE id = ?");
        $stmt->bind_param("ii", $status, $taskId);

        if ($stmt->execute()) {
            header("Location: dashboard.php");
            exit;
        } else {
            echo "Gagal mengubah status task.";
        }
        $stmt->close();
    } else {
        echo "Data yang dikirim tidak valid.";
    }
}
?>
