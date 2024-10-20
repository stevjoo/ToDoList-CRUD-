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
</head>
<body>
    <form method="POST">
        <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
        <input type="text" name="task" value="<?php echo htmlspecialchars($task['task']); ?>" required>
        <button type="submit">Save Changes</button>
    </form>
</body>
</html>
