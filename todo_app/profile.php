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
    <link rel="stylesheet" href="src/todostylesoutput.css">
</head>
<body class="bg-slate-50 text-xl text-slate-600 h-screen">
    <h2 class="text-3xl font-bold py-4 border-b">Your Profile</h2>
    <h3>Username:</h3>
    <p class="bg-sky-100"><?php echo $username; ?></p>
    <h3 class="mt-4">Email:</h3>
    <p class="bg-sky-100"><?php echo $email; ?></p>
    <a class="btn btn-outline btn-block fixed bottom-4" href="edit_profile.php">Edit Profile</a>
</body>
</html>
