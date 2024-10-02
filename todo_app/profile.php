<?php
session_start();
require 'db/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Ambil data profil user
$query = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$query->bind_param("i", $userId);
$query->execute();
$query->bind_result($username, $email);
$query->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
</head>
<body>
    <h2>Profil Saya</h2>
    <p>Username: <?php echo $username; ?></p>
    <p>Email: <?php echo $email; ?></p>
    <a href="edit_profile.php">Edit Profil</a>
</body>
</html>
