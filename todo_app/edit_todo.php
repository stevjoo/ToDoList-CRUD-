<?php
session_start();
require 'db/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if (isset($_GET['todo_id'])) {
    $todoId = $_GET['todo_id'];
    $query = "SELECT * FROM todos WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $todoId);
    $stmt->execute();
    $result = $stmt->get_result();
    $todo = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $todoId = $_POST['todo_id'];
    $todoTitle = $_POST['title'];

    $updateQuery = "UPDATE todos SET title = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("si", $todoTitle, $todoId);
    $updateStmt->execute();

    header("Location: dashboard.php");
    exit;
}
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
