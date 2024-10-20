<?php
session_start();
require 'db/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8');
    $userId = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO todos (user_id, title) VALUES (?, ?)");
    $stmt->bind_param("is", $userId, $title);

    if ($stmt->execute()) {
        header("Location: add_todo.php?success=1");
    } else {
        echo "Gagal menambahkan to-do list.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah To-Do List</title>
    <link rel="stylesheet" href="src/todostylesoutput.css">
</head>
<body class="bg-slate-50 text-slate-600 text-xl h-screen">
    <h2 class="text-3xl font-bold py-4 border-b-2">Add New To-Do List</h2>
    <form action="add_todo.php" method="POST">
        <h3 class="my-4">Enter name of list:</h3>
        <input class="input w-full m-auto" type="text" name="title" placeholder="New To-Do List" required><br>
        <button class="btn btn-outline btn-block fixed bottom-4" type="submit">Create List</button>
    </form>
</body>
</html>
