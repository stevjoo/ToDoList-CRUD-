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
    <link rel="stylesheet" href="src/todostylesoutput.css">
</head>
<body class="bg-gradient-to-r h-screen from-blue-300 to-purple-200">
    <div class="m-auto h-screen w-full p-10 md:w-3/5 bg-slate-50 text-slate-600 text-xl">
        <form class="my-12" method="POST">
            <h2 class="text-3xl font-bold py-4 border-b-2">Edit To-Do List</h2>
            <h3 class="my-4">Enter new list name:</h3>
            <input type="hidden" name="todo_id" value="<?php echo $todo['id']; ?>">
            <input class="input input-lg w-full m-auto" type="text" name="title" value="<?php echo htmlspecialchars($todo['title']); ?>" required>
            <button class="btn btn-outline btn-success btn-block mx-auto mt-12" type="submit">Save Changes</button>
            <a class="btn btn-outline btn-neutral btn-block mx-auto mt-6" href="dashboard.php">Cancel</a>
        </form>
    </div>
    
</body>
</html>
