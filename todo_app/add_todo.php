<?php
session_start();
require 'db/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $userId = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO todos (user_id, title) VALUES (?, ?)");
    $stmt->bind_param("is", $userId, $title);

    if ($stmt->execute()) {
        header("Location: dashboard.php");
    } else {
        echo "Gagal menambahkan to-do list.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah To-Do List</title>
</head>
<body>
    <h2>Tambah To-Do List</h2>
    <form action="add_todo.php" method="POST">
        <input type="text" name="title" placeholder="Judul To-Do List" required><br>
        <button type="submit">Tambah</button>
    </form>
</body>
</html>
