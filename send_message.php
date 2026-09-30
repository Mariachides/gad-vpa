<?php
session_start();
include('db_connection.php');

if (isset($_POST['message']) && isset($_SESSION['username'])) {
    $sender = $_SESSION['username'];
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    
    $query = "INSERT INTO chat_messages (sender_username, message, receiver_username) VALUES ('$sender', '$message', 'admin')";
    mysqli_query($conn, $query);
}
?>