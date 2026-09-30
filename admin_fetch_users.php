<?php
include('db_connection.php');
$query = "SELECT DISTINCT sender_username FROM chat_messages WHERE sender_username != 'admin' ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
$users = [];
while($row = mysqli_fetch_assoc($result)) { $users[] = $row['sender_username']; }
echo json_encode($users);
?>