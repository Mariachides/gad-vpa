<?php
include('db_connection.php');
$user = mysqli_real_escape_string($conn, $_GET['user']);
$query = "SELECT * FROM chat_messages WHERE (sender_username = '$user' AND receiver_username = 'admin') OR (sender_username = 'admin' AND receiver_username = '$user') ORDER BY created_at ASC";
$result = mysqli_query($conn, $query);

while($row = mysqli_fetch_assoc($result)) {
    $isAdmin = ($row['sender_username'] == 'admin');
    $class = $isAdmin ? 'msg-admin' : 'msg-user';
    echo "<div class='$class'>
            ".htmlspecialchars($row['message'])."
          </div>";
}
?>