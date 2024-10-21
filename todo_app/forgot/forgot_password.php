<?php
session_start();
require '../db/config.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim(htmlspecialchars($_POST['username']));

    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        $_SESSION['reset_username'] = $username;
        header("Location: confirmation.php");
        exit;
    } else {
        $error = "Account with this username not found.";
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn); // Close the database connection
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../src/todostylesoutput.css">
</head>
<body class="min-h-screen bg-gradient-to-r from-blue-200 to-purple-200 m-0 p-0 overflow-visible">
    <div class="m-auto h-screen w-4/5 py-20 px-10 md:w-3/5 bg-slate-100 text-xl shadow-xl shadow-blue">
        <h2 class="text-3xl font-bold py-2 border-b">Recover your Account</h2> 
        <?php if (isset($error)): ?>
            <p role="alert" class="alert font-semibold alert-error my-6"   ><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <h3 class="font-semibold">Enter your username:</h3>
        <form method="POST" action="">
            <input class="input input-lg my-4 w-full" type="text" name="username" id="username" required>
            <button class="btn btn-outline btn-block btn-primary my-4" type="submit">Next</button>
            <a href="../index.php" class="btn btn-outline btn-block my-4">Back to Login</a>
        </form>
    </div>    
</body>
</html>


