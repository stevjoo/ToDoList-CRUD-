<?php
session_start();
require 'db/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$userId = intval($_SESSION['user_id']); // Ensure the user ID is an integer

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone = trim($_POST['phone']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, phone = ?, password = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $username, $email, $phone, $password, $userId);

    if ($stmt->execute()) {
        header("Location: profile.php");
    } else {
        echo "Gagal mengupdate profil.";
    }

    $stmt->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil</title>
    <link rel="stylesheet" href="src/todostylesoutput.css">
</head>
<body class="bg-slate-50 text-xl text-slate-600 h-screen font-semibold">
    <form action="edit_profile.php" method="POST">
        <h3>New Username:</h3>
        <input class="input w-full m-auto" type="text" name="username" placeholder="Username" required><br>

        <h3 class="mt-1">New email:</h3>
        <input class="input w-full m-auto" type="email" name="email" placeholder="Email" required><br>

        <h3 class="mt-1">New Phone Number:</h3>
        <input class="input w-full m-auto" type="text" name="phone" placeholder="Phone Number" required><br>

        <h3 class="mt-1">New Password:</h3>
        <input class="input w-full m-auto" type="password" name="password" placeholder="Password" required><br>
        <button class="btn btn-outline btn-block my-4" type="submit">Update Profile</button>
    </form>
</body>
</html>
