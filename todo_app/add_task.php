<?php
session_start();
require 'db/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $task = $_POST['task'];
    $userId = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO tasks (user_id, task) VALUES (?, ?)");
    $stmt->bind_param("is", $userId, $task);

    if ($stmt->execute()) {
        header("Location: dashboard.php");
    } else {
        echo "Gagal menambahkan task.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Task</title>
</head>
<body>
    <h2>Tambah Task</h2>
    <form action="add_task.php" method="POST">
        <input type="text" name="task" placeholder="Nama Task" required><br>
        <button type="submit">Tambah</button>
    </form>
</body>
</html>
