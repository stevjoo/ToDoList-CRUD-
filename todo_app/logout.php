<?php
session_start();
$_SESSION = []; // Clear all session variables
session_destroy();
header("Location: index.php");
exit;
?>
