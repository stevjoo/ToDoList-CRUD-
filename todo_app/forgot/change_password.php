<?php
session_start();
require '../db/config.php'; // Ensure this initializes the $conn variable for MySQLi

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user data from the database
$stmt = mysqli_prepare($conn, "SELECT username, email FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get password from POST request
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // Validate password if provided
    if (!empty($password)) {
        if ($password !== $password_confirm) {
            $errors[] = 'Passwords do not match';
        }
    }

    // If no errors, proceed with updating the password
    if (count($errors) === 0) {
        if (!empty($password)) {
            // Hash the new password and update it in the database
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $update_stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?");
            mysqli_stmt_bind_param($update_stmt, "si", $hashed_password, $user_id);
            mysqli_stmt_execute($update_stmt);
            mysqli_stmt_close($update_stmt);
        }

        // Redirect after successful update
        header("Location: ../index.php");
        exit;
    }
}
?>

<!-- HTML Form -->
<div class="container mx-auto my-10">
    <h1 class="text-center text-2xl font-bold my-6">Change Password</h1>

    <form action="./change_password.php" method="POST" class="bg-white p-6 rounded-lg shadow-lg">
        <div class="mb-4">
            <label for="password" class="block text-gray-700 font-bold mb-2">New Password (optional):</label>
            <input type="password" id="password" name="password" class="w-full p-2 border border-gray-300 rounded-md">
        </div>

        <div class="mb-4">
            <label for="password_confirm" class="block text-gray-700 font-bold mb-2">Confirm New Password:</label>
            <input type="password" id="password_confirm" name="password_confirm" class="w-full p-2 border border-gray-300 rounded-md">
        </div>

        <!-- Display errors if any -->
        <?php if (count($errors) > 0): ?>
            <ul class="mb-4">
                <?php foreach ($errors as $error): ?>
                    <li class="text-red-500"><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <div class="flex flex-col space-y-4 sm:flex-row sm:space-x-4 sm:space-y-0">
            <button type="submit" class="bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-600 transition w-full text-sm sm:w-auto">Update Password</button>
            <a href="./change_password.php" class="bg-red-500 text-white font-bold py-2 px-4 rounded hover:bg-red-600 transition w-full sm:w-auto text-center">Cancel</a>
        </div>
    </form>
</div>

<!-- Tailwind CSS -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
