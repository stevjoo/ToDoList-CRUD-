<?php
require 'db/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "The email you entered has already been registered.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password);

        if ($stmt->execute()) {
            header("Location: index.php");
        } else {
            echo "Failed to register user.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="src/todostylesoutput.css">

</head>
<body class="min-h-screen bg-gradient-to-r from-blue-200 to-purple-200 m-0 p-0 overflow-visible">
    <div class="m-auto h-screen w-4/5 p-10 md:w-3/5 bg-slate-100 text-xl shadow-xl shadow-blue">
        <h2 class="m-4 py-4 text-3xl text-center font-bold border-b-2">Register</h2>
        <form action="register.php" method="POST" class="flex flex-col items-center">
            <input class="input input-lg my-4 md:w-3/5" type="text" name="username" placeholder="Username" required><br>
            <input class="input input-lg my-4 md:w-3/5" type="email" name="email" placeholder="Email" required><br>
            <input class="input input-lg my-4 md:w-3/5" type="password" name="password" placeholder="Password" required>
            <button class="my-4 block btn btn-primary text-xl" type="submit">Create your account</button>
            <a class="my-4 block text-center text-blue-600 hover:underline" href="register.php">Already have an account?</a>
        </form>
    </div>
</body>
</html>
