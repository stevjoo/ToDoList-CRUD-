<?php
session_start();
require 'db/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if (isset($_GET['id'])) {
    $todoId = intval($_GET['id']); // Ensure that the ID is an integer

    $stmt = $conn->prepare("DELETE FROM todos WHERE id = ?");
    $stmt->bind_param("i", $todoId);

    if ($stmt->execute()) {
        header("Location: dashboard.php");
    } else {
        echo "Gagal menghapus to-do list.";
    }

    $stmt->close();
}

$conn->close();
?>
