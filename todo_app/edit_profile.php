<?php
session_start();
require 'db/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Update profil user
    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
    $stmt->bind_param("sssi", $username, $email, $password, $userId);

    if ($stmt->execute()) {
        header("Location: profile.php");
    } else {
        echo "Gagal mengupdate profil.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil</title>
</head>
<body>
    <h2>Edit Profil</h2>
    <form action="edit_profile.php" method="POST">
        <input type="text" name="username" placeholder="Username baru" required><br>
        <input type="email" name="email" placeholder="Email baru" required><br>
        <input type="password" name="password" placeholder="Password baru" required><br>
        <button type="submit">Update</button>
    </form>
</body>
</html>
