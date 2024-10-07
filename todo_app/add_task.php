<?php
session_start();
require 'db/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $task = $_POST['task'];
    $todoId = $_POST['todo_id'];

    $stmt = $conn->prepare("INSERT INTO tasks (todo_id, task) VALUES (?, ?)");
    $stmt->bind_param("is", $todoId, $task);

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
        <input type="hidden" name="todo_id" value="<?php echo $_GET['todo_id']; ?>"><br>
        <button type="submit">Tambah</button>
    </form>
</body>
</html>
