<?php
session_start();
require 'db/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "No task ID provided.";
    exit;
}

$taskId = $_GET['id'];

// Fetch the task details
$stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ?");
$stmt->bind_param("i", $taskId);
$stmt->execute();
$task = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $taskName = $_POST['task_name'];
    $completed = isset($_POST['completed']) ? 1 : 0;

    // Update the task
    $updateStmt = $conn->prepare("UPDATE tasks SET task = ?, completed = ? WHERE id = ?");
    $updateStmt->bind_param("sii", $taskName, $completed, $taskId);

    if ($updateStmt->execute()) {
        echo "<script>window.top.location.reload();</script>"; // Reload the parent page
    } else {
        echo "Error updating task.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Task</title>
</head>
<body>
    <h1>Edit Task</h1>
    <form method="POST">
        <label for="task_name">Task Name:</label>
        <input type="text" id="task_name" name="task_name" value="<?php echo htmlspecialchars($task['task']); ?>" required>
        <br>
        <label for="completed">Completed:</label>
        <input type="checkbox" id="completed" name="completed" <?php echo $task['completed'] ? 'checked' : ''; ?>>
        <br>
        <button type="submit">Save</button>
    </form>
</body>
</html>
