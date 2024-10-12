<?php
session_start();
require 'db/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if (isset($_GET['task_id'])) {
    $taskId = $_GET['task_id'];

    // Prepare the DELETE query
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->bind_param("i", $taskId);

    if ($stmt->execute()) {
        header("Location: dashboard.php");
    } else {
        echo "Error deleting task.";
    }
    $stmt->close();
} else {
    echo "Invalid task ID.";
}

$conn->close();
?>
