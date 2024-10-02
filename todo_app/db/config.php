<?php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'todo_app';

// Buat koneksi
$conn = new mysqli($host, $user, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
