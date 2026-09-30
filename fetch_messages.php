<?php
session_start();
include('db_connection.php');

$current_user = $_SESSION['username'];
// Kinukuha ang convo sa pagitan ng user at admin
$query = "SELECT * FROM chat_messages WHERE sender_username = '$current_user' OR receiver_username = '$current_user' ORDER BY created_at ASC";
$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    $isMe = ($row['sender_username'] == $current_user);
    $sender = $isMe ? 'You' : 'GAD Admin';
    $bg = $isMe ? "#6f42c1" : "#ffffff";
    $color = $isMe ? "white" : "black";
    $border = $isMe ? "" : "border: 1px solid #dee2e6;";
    $align = $isMe ? "align-self: flex-end;" : "align-self: flex-start;";
    
    echo "<div style='max-width: 80%; padding: 8px 12px; border-radius: 15px; font-size: 0.85rem; background: $bg; color: $color; $border $align'>
            <small style='display: block; font-weight: 700; margin-bottom: 2px; opacity: 0.7;'>$sender</small>
            " . htmlspecialchars($row['message']) . "
          </div>";
}
?>