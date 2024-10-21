<?php
session_start();
require 'db/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if (isset($_GET['task_id'])) {
    $taskId = intval($_GET['task_id']); // Ensure the task ID is an integer
    $query = "SELECT * FROM tasks WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $taskId);
    $stmt->execute();
    $result = $stmt->get_result();
    $task = $result->fetch_assoc();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $taskId = intval($_POST['task_id']); // Ensure the task ID is an integer
    $taskDescription = trim($_POST['task']); // Trim whitespace from task description

    $updateQuery = "UPDATE tasks SET task = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("si", $taskDescription, $taskId);
    $updateStmt->execute();
    $updateStmt->close();

    header("Location: dashboard.php");
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Task</title>
    <link rel="stylesheet" href="src/todostylesoutput.css">
</head>
<body class="bg-gradient-to-r h-screen from-blue-300 to-purple-200">
    <div class="m-auto h-screen w-full p-10 md:w-3/5 bg-slate-50 text-slate-600 text-xl"> 
        <h2 class="text-3xl font-bold py-4 border-b-2">Edit Task</h2>
        <h3 class="my-4">Enter task name:</h3>
        <form class="my-12" method="POST">
            <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
            <input class="input input-lg w-full m-auto" type="text" name="task" value="<?php echo htmlspecialchars($task['task']); ?>" required>
            <button class="btn btn-outline btn-success btn-block mx-auto mt-12" type="submit">Save Changes</button>
            <a class="btn btn-outline btn-neutral btn-block mx-auto mt-6" href="dashboard.php">Cancel</a>
        </form>
    </div>
</body>
</html>
