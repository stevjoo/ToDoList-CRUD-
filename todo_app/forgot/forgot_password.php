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
        $error = "Username tidak ditemukan.";
    }

    mysqli_stmt_close($stmt);
}
?>

<form method="POST" action="">
    <label for="username">Username:</label>
    <input type="text" name="username" id="username" required>
    <button type="submit">Next</button>
</form>

<?php if (isset($error)): ?>
    <p style="color:red;"><?= $error ?></p>
<?php endif; ?>
