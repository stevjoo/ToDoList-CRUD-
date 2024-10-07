<?php
session_start();
require 'db/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$userId = $_SESSION['user_id'];
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'all';
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

$query = "SELECT * FROM todos WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <h2>Dashboard</h2>
    <a href="add_todo.php">Tambah To-Do List</a> | <a href="profile.php">Profil Saya</a> | <a href="logout.php">Logout</a>

    <h3>Filter Tasks:</h3>
    <a href="dashboard.php?status=all">Semua</a> | 
    <a href="dashboard.php?status=completed">Selesai</a> | 
    <a href="dashboard.php?status=incomplete">Belum Selesai</a>

    <h3>Cari Task:</h3>
    <form action="dashboard.php" method="GET">
        <input type="text" name="search" placeholder="Cari Task..." value="<?php echo htmlspecialchars($searchQuery); ?>">
        <button type="submit">Cari</button>
    </form>

    <h3>To-Do Lists</h3>

    <?php 
    if ($searchQuery) {
        $searchStmt = $conn->prepare("SELECT * FROM tasks WHERE task LIKE ? AND todo_id IN (SELECT id FROM todos WHERE user_id = ?)");
        $likeQuery = "%" . $searchQuery . "%";
        $searchStmt->bind_param("si", $likeQuery, $userId);
        $searchStmt->execute();
        $tasks = $searchStmt->get_result();

        echo "<h3>Hasil Pencarian untuk: " . htmlspecialchars($searchQuery) . "</h3>";
        while ($task = $tasks->fetch_assoc()) {
            echo "<p>" . $task['task'] . " [" . ($task['completed'] ? 'Selesai' : 'Belum Selesai') . "]</p>";
        }
    } else {
        while ($row = $result->fetch_assoc()) {
            $todoId = $row['id'];
            echo "<div><h4>" . htmlspecialchars($row['title']) . "</h4>";
            
            $filterQuery = "SELECT * FROM tasks WHERE todo_id = ?";
            if ($statusFilter == 'completed') {
                $filterQuery .= " AND completed = 1";
            } elseif ($statusFilter == 'incomplete') {
                $filterQuery .= " AND completed = 0";
            }

            $taskStmt = $conn->prepare($filterQuery);
            $taskStmt->bind_param("i", $todoId);
            $taskStmt->execute();
            $tasks = $taskStmt->get_result();

            while ($task = $tasks->fetch_assoc()) {
                echo "<p>" . htmlspecialchars($task['task']);
                echo $task['completed'] ? " [Selesai]" : " [Belum Selesai]";
                echo " <a href='complete_task.php?task_id=" . $task['id'] . "&status=" . ($task['completed'] ? "0" : "1") . "'>";
                echo $task['completed'] ? "Tandai Belum Selesai" : "Tandai Selesai";
                echo "</a></p>";
            }

            echo "<a href='add_task.php?todo_id=" . $todoId . "'>Tambah Task</a> | <a href='delete_todo.php?id=" . $todoId . "'>Hapus To-Do List</a></div><br>";
        }
    }
    ?>
</body>
</html>
