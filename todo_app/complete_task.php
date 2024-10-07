<?php
session_start();
require 'db/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if (isset($_GET['task_id'])) {
    $taskId = $_GET['task_id'];
    $status = $_GET['status'];
    $stmt = $conn->prepare("UPDATE tasks SET completed = ? WHERE id = ?");
    $stmt->bind_param("ii", $status, $taskId);

    if ($stmt->execute()) {
        header("Location: dashboard.php");
    } else {
        echo "Gagal mengubah status task.";
    }
}
?>
