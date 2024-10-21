<?php
session_start();
require 'db/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']); // Sanitize email input
    $password = trim($_POST['password']); // Sanitize password input

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($userId, $hashedPassword);
    
    if ($stmt->fetch() && password_verify($password, $hashedPassword)) {
        $_SESSION['user_id'] = $userId;
        header("Location: dashboard.php");
        exit;
    } else {
        echo '
        <script>confirm("Incorrect email or password.");</script>
        ';
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
    <title>Login</title>
    <link rel="stylesheet" href="src/todostylesoutput.css">
</head>
<body class="min-h-screen bg-gradient-to-r from-blue-200 to-purple-200 m-0 p-0 overflow-visible">
    <div class="m-auto h-screen w-4/5 p-10 md:w-3/5 bg-slate-100 text-xl shadow-xl shadow-blue">
        <h2 class="m-4 py-4 text-3xl text-center font-bold border-b-2">Login to To-Do List</h2>
        <form action="index.php" method="POST" class="flex flex-col items-center">
            <input class="input input-lg my-4 md:w-3/5" type="email" name="email" placeholder="Email" required>
            <input class="input input-lg my-4 md:w-3/5" type="password" name="password" placeholder="Password" required>
            <button class="m4-2 block btn btn-primary text-xl" type="submit"> Login</button>
            <a class="my-4 block text-center text-blue-600 hover:underline" href="register.php">Don't have an account?</a>
            <a class="my-4 block text-center text-blue-600 hover:underline" href="./forgot/forgot_password.php">Forgot Password</a>
        </form>
    </div>
</body>

</html>
