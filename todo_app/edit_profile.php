<?php
session_start();
require 'db/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$userId = intval($_SESSION['user_id']); 

$stmt = $conn->prepare("SELECT username, email, phone, password FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = !empty(trim($_POST['username'])) ? trim($_POST['username']) : $userData['username'];
    $email = !empty(trim($_POST['email'])) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : $userData['email'];
    $phone = !empty(trim($_POST['phone'])) ? trim($_POST['phone']) : $userData['phone'];

    if (!empty(trim($_POST['password']))) {
        $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    } else {
        $password = $userData['password']; 
    }

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
        <h3>New Username: (Opsional)</h3>
        <input class="input w-full m-auto" type="text" name="username" placeholder="Username"><br>

        <h3 class="mt-1">New email: (Opsional)</h3>
        <input class="input w-full m-auto" type="email" name="email" placeholder="Email"><br>

        <h3 class="mt-1">New Phone Number: (Opsional)</h3>
        <input class="input w-full m-auto" type="text" name="phone" placeholder="Phone Number"><br>

        <h3 class="mt-1">New Password: (Opsional)</h3>
        <input class="input w-full m-auto" type="password" name="password" placeholder="Password"><br>
        <button class="btn btn-outline btn-block my-4" type="submit">Update Profile</button>
    </form>
</body>
</html>
