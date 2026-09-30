<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
include 'config.php';

// Fetch chat messages
$chat = mysqli_query($conn, "SELECT * FROM chat ORDER BY sent_at ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin - Chatbox</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <a href="admin_landing.php" class="btn btn-secondary mb-3">Back to Dashboard</a>
    <h3>Chatbox</h3>
    <div class="border p-3 mb-3" style="height:300px; overflow-y:scroll;">
        <?php while($msg = mysqli_fetch_assoc($chat)): ?>
            <p><b><?php echo htmlspecialchars($msg['from_user']); ?>:</b> <?php echo htmlspecialchars($msg['message']); ?></p>
        <?php endwhile; ?>
    </div>
    <form method="POST" action="send_message.php">
        <textarea name="message" class="form-control mb-2" placeholder="Type your reply..." required></textarea>
        <button type="submit" class="btn btn-primary">Send</button>
    </form>
</div>
</body>
</html>