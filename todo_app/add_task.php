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
        echo "Failed to create task.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Task</title>
    <link rel="stylesheet" href="src/todostylesoutput.css">
</head>
<body class="bg-slate-50 text-slate-600 text-xl h-screen">
    <h2 class="text-3xl font-bold py-4 border-b-2">Add Task</h2>
    <form action="add_task.php" method="POST">
        <input class="input w-full m-auto" type="text" name="task" placeholder="Enter your task here..." required><br>
        <input type="hidden" name="todo_id" value="<?php echo $_GET['todo_id']; ?>">
        <button class="btn btn-outline btn-block fixed bottom-4" type="submit">Create Task</button>
    </form>
</body>
</html>
