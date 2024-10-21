<?php
session_start();
require 'db/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $task = htmlspecialchars($_POST['task'], ENT_QUOTES, 'UTF-8');
    $todoId = intval($_POST['todo_id']);

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
    <title>Add Task</title>
    <link rel="stylesheet" href="src/todostylesoutput.css">
</head>
<body class="bg-gradient-to-r h-screen from-blue-300 to-purple-200">
    <div class="m-auto h-screen w-full p-10 md:w-3/5 bg-slate-50 text-slate-600 text-xl">
        <form class="my-12" action="add_task.php" method="POST">
            <h2 class="text-3xl font-bold py-4 border-b-2">Add Task</h2>
            <input class="input input-lg w-full m-auto" type="text" name="task" placeholder="Enter your task here..." required><br>
            <input type="hidden" name="todo_id" value="<?php echo htmlspecialchars($_GET['todo_id'], ENT_QUOTES, 'UTF-8'); ?>">
            <button class="btn btn-outline btn-success btn-block mx-auto mt-12" type="submit">Create Task</button>
            <a class="btn btn-outline btn-neutral btn-block mx-auto mt-6" href="dashboard.php">Cancel</a>
        </form>
    </div>
</body>
</html>
