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

// Query nampilin semua task dari user
$query = "SELECT * FROM tasks WHERE user_id = ?";
if ($statusFilter == 'completed') {
    $query .= " AND completed = 1";
} elseif ($statusFilter == 'incomplete') {
    $query .= " AND completed = 0";
}
if ($searchQuery) {
    $query .= " AND task LIKE ?";
    $searchTerm = "%" . $searchQuery . "%";
}

$stmt = $conn->prepare($query);
if ($searchQuery) {
    $stmt->bind_param("is", $userId, $searchTerm);
} else {
    $stmt->bind_param("i", $userId);
}
$stmt->execute();
$tasks = $stmt->get_result();
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
    <a href="add_task.php">Tambah Task</a> | <a href="profile.php">Profil Saya</a> | <a href="logout.php">Logout</a>

    <h3>Filter Tasks:</h3>
    <a href="dashboard.php?status=all">Semua</a> | 
    <a href="dashboard.php?status=completed">Selesai</a> | 
    <a href="dashboard.php?status=incomplete">Belum Selesai</a>

    <h3>Cari Task:</h3>
    <form action="dashboard.php" method="GET">
        <input type="text" name="search" placeholder="Cari Task..." value="<?php echo htmlspecialchars($searchQuery); ?>">
        <button type="submit">Cari</button>
    </form>

    <h3>Tasks</h3>

    <?php 
    if ($tasks->num_rows > 0) {
        while ($task = $tasks->fetch_assoc()) {
            echo "<p>" . htmlspecialchars($task['task']);
            echo $task['completed'] ? " [Selesai]" : " [Belum Selesai]";
            echo " <a href='complete_task.php?task_id=" . $task['id'] . "&status=" . ($task['completed'] ? "0" : "1") . "'>";
            echo $task['completed'] ? "Tandai Belum Selesai" : "Tandai Selesai";
            echo "</a> | <a href='delete_task.php?id=" . $task['id'] . "'>Hapus Task</a></p>";
        }
    } else {
        echo "<p>Tidak ada task ditemukan.</p>";
    }
    ?>
</body>
</html>
