<?php
session_start();
require '../db/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = intval($_SESSION['user_id']); // Ensure the user ID is an integer

$stmt = mysqli_prepare($conn, "SELECT username, email FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt); // Close the select statement

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = trim($_POST['password'] ?? '');
    $password_confirm = trim($_POST['password_confirm'] ?? '');

    if (!empty($password)) {
        if ($password !== $password_confirm) {
            $errors[] = 'Passwords do not match';
        }
    }

    if (count($errors) === 0) {
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $update_stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?");
            mysqli_stmt_bind_param($update_stmt, "si", $hashed_password, $user_id);
            mysqli_stmt_execute($update_stmt);
            mysqli_stmt_close($update_stmt);
        }

        header("Location: ../index.php");
        exit;
    }
}

mysqli_close($conn); // Close the connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="../src/todostylesoutput.css">
</head>
<body class="min-h-screen bg-gradient-to-r from-blue-200 to-purple-200 m-0 p-0 overflow-visible">
    <div class="m-auto h-screen w-4/5 py-20 px-10 md:w-3/5 bg-slate-100 text-xl shadow-xl shadow-blue">   
        <h2 class="text-center text-2xl font-bold my-6 border-b">Change Password</h2>
        <?php if (count($errors) > 0): ?>
                <ul class="mb-4">
                    <?php foreach ($errors as $error): ?>
                        <li role="alert" class="alert font-semibold alert-error my-6"><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
        <?php endif; ?>
        <form action="./change_password.php" method="POST">
            <div class="mb-4">
                <label for="password" class="block text-gray-700 font-bold mb-2">New Password (optional):</label>
                <input type="password" id="password" name="password" class="w-full p-2 border border-gray-300 rounded-md">
            </div>

            <div class="mb-4">
                <label for="password_confirm" class="block text-gray-700 font-bold mb-2">Confirm New Password:</label>
                <input type="password" id="password_confirm" name="password_confirm" class="w-full p-2 border border-gray-300 rounded-md">
            </div>

            <div class="flex flex-col space-y-4 sm:flex-row sm:space-x-4 sm:space-y-0">
                <button type="submit" class="bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-600 transition w-full text-sm sm:w-auto">Update Password</button>
                <a href="../index.php" class="bg-red-500 text-white font-bold py-2 px-4 rounded hover:bg-red-600 transition w-full sm:w-auto text-center">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>


