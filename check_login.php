<?php
session_start();
include 'config.php';

$username = $_POST['username'];
$password = $_POST['password'];

$query = "SELECT * FROM users WHERE username='$username'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['username'] = $user['username']; // ⭐ VERY IMPORTANT
    header("Location: index.php"); // landing page
    exit();
} else {
    echo "Invalid login";
}
