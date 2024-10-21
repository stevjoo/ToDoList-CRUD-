<?php
session_start();
require '../db/config.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim(htmlspecialchars($_POST['email']));
    $phone = trim(htmlspecialchars($_POST['phone']));

    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ? AND phone = ?");
    mysqli_stmt_bind_param($stmt, "ss", $email, $phone);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: ./change_password.php");
        exit();
    } else {
        $error = "Invalid login credentials.";
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
    <title>Confirmation</title>
    <link rel="stylesheet" href="../src/todostylesoutput.css">
</head>
<body class="min-h-screen bg-gradient-to-r from-blue-200 to-purple-200 m-0 p-0 overflow-visible">
    <div class="m-auto h-screen w-4/5 py-20 px-10 md:w-3/5 bg-slate-100 text-xl shadow-xl shadow-blue"> 
        <h2 class="text-3xl font-bold py-2 border-b">Confirmation</h2>
        <?php if (isset($error)): ?>
            <p role="alert" class="alert font-semibold alert-error my-6"   ><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <label class="font-semibold" for="email">Email:</label>
            <input class="input input-lg my-4 w-full" type="email" name="email" id="email" required>
            <label class="font-semibold" for="phone">Phone:</label>
            <input class="input input-lg my-4 w-full" type="text" name="phone" id="phone" required>
            <button class="btn btn-outline btn-block btn-primary my-4" type="submit">Next</button>
            <a href="../index.php" class="btn btn-outline btn-block my-4">Back to Login</a>
        </form>
    </div>
    
</body>
</html>




