<?php
session_start();
require 'db/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if (isset($_GET['todo_id'])) {
    $todoId = intval($_GET['todo_id']); // Ensure the todo ID is an integer
    $query = "SELECT * FROM todos WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $todoId);
    $stmt->execute();
    $result = $stmt->get_result();
    $todo = $result->fetch_assoc();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $todoId = intval($_POST['todo_id']); // Ensure the todo ID is an integer
    $todoTitle = trim($_POST['title']); // Trim whitespace from todo title

    $updateQuery = "UPDATE todos SET title = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("si", $todoTitle, $todoId);
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
    <title>Edit Todo</title>
</head>
<body>
    <form method="POST">
        <input type="hidden" name="todo_id" value="<?php echo $todo['id']; ?>">
        <input type="text" name="title" value="<?php echo htmlspecialchars($todo['title']); ?>" required>
        <button type="submit">Save Changes</button>
    </form>
</body>
</html>
