<?php
session_start();
require '../db/config.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);

    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user && $phone) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: ./change_password.php");
        exit();
    } else {
        $error = "Invalid login credentials.";
    }

    mysqli_stmt_close($stmt); 
}
?>

<form method="POST" action="">
    <label for="email">Email:</label>
    <input type="email" name="email" id="email" required>
    <label for="phone">Phone:</label>
    <input type="text" name="phone" id="phone" required>
    <button type="submit">Next</button>
</form>

<?php if (isset($error)): ?>
    <p style="color:red;"><?= $error ?></p>
<?php endif; ?>
