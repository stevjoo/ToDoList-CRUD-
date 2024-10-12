<?php
session_start();
require 'db/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "No todo ID provided.";
    exit;
}

$todoId = $_GET['id'];

// Fetch the todo details
$stmt = $conn->prepare("SELECT * FROM todos WHERE id = ?");
$stmt->bind_param("i", $todoId);
$stmt->execute();
$todo = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];

    // Update the todo
    $updateStmt = $conn->prepare("UPDATE todos SET title = ? WHERE id = ?");
    $updateStmt->bind_param("si", $title, $todoId);

    if ($updateStmt->execute()) {
        echo "<script>window.top.location.reload();</script>"; // Reload the parent page
    } else {
        echo "Error updating todo.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Todo</title>
</head>
<body>
    <h1>Edit Todo</h1>
    <form method="POST">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($todo['title']); ?>" required>
        <br>
        <button type="submit">Save</button>
    </form>
</body>
</html>
