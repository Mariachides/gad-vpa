<?php
session_start();
if(!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin'){
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}
include '../config.php';

$admin = $_SESSION['username'];

// Fetch totals
$reports_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM reports"))['total'];
$calendar_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM calendar"))['total'];
$accomplishments_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM accomplishments"))['total'];
$feedback_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM feedback"))['total'];
$chat_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM chat"))['total'];

// Fetch new items since last_checked
$last_checked = mysqli_fetch_assoc(mysqli_query($conn, "SELECT last_checked FROM users WHERE username='$admin'"))['last_checked'];

$new_reports = $last_checked ? mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM reports WHERE created_at > '$last_checked'"))['total'] : 0;
$new_feedback = $last_checked ? mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM feedback WHERE created_at > '$last_checked'"))['total'] : 0;
$new_chat = $last_checked ? mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM chat WHERE sent_at > '$last_checked'"))['total'] : 0;

// Update last_checked to now
mysqli_query($conn, "UPDATE users SET last_checked=NOW() WHERE username='$admin'");

echo json_encode([
    'reports_count' => $reports_count,
    'calendar_count' => $calendar_count,
    'accomplishments_count' => $accomplishments_count,
    'feedback_count' => $feedback_count,
    'chat_count' => $chat_count,
    'new_reports' => $new_reports,
    'new_feedback' => $new_feedback,
    'new_chat' => $new_chat
]);
?>