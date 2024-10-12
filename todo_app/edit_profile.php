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
    <link rel="stylesheet" href="src/todostylesoutput.css">
</head>
<body class="bg-slate-50 text-xl text-slate-600 h-screen">
    <h2 class="text-3xl font-bold py-4 border-b">Edit Profile</h2>
    <form action="edit_profile.php" method="POST">
        <h3>New Username:</h3>
        <input class="input w-full m-auto" type="text" name="username" placeholder="Username" required><br>

        <h3 class="mt-1">New email:</h3>
        <input class="input w-full m-auto" type="email" name="email" placeholder="Email" required><br>

        <h3 class="mt-1">New Password:</h3>
        <input class="input w-full m-auto" type="password" name="password" placeholder="Password" required><br>
        <button class="btn btn-outline btn-block fixed bottom-4" type="submit">Update Profile</button>
    </form>
</body>
</html>
