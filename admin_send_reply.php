<?php
session_start();
include('db_connection.php');
if($_POST['message'] && $_POST['receiver']) {
    $receiver = mysqli_real_escape_string($conn, $_POST['receiver']);
    $msg = mysqli_real_escape_string($conn, $_POST['message']);
    mysqli_query($conn, "INSERT INTO chat_messages (sender_username, receiver_username, message) VALUES ('admin', '$receiver', '$msg')");
}
?>