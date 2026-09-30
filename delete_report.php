<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
include 'config.php';

// ================== VALIDATE INPUT ==================
$reportId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($reportId <= 0) {
    $_SESSION['delete_error'] = "Invalid report ID.";
    header("Location: reports.php");
    exit();
}

// ================== CONFIRM THE REPORT EXISTS ==================
$checkStmt = mysqli_prepare($conn, "SELECT id FROM `incident_reports` WHERE id = ?");
mysqli_stmt_bind_param($checkStmt, "i", $reportId);
mysqli_stmt_execute($checkStmt);
$checkResult = mysqli_stmt_get_result($checkStmt);

if (mysqli_num_rows($checkResult) === 0) {
    mysqli_stmt_close($checkStmt);
    $_SESSION['delete_error'] = "Report not found. It may have already been deleted.";
    header("Location: reports.php");
    exit();
}
mysqli_stmt_close($checkStmt);

// ================== DELETE THE REPORT ==================
$deleteStmt = mysqli_prepare($conn, "DELETE FROM `incident_reports` WHERE id = ?");
mysqli_stmt_bind_param($deleteStmt, "i", $reportId);

if (mysqli_stmt_execute($deleteStmt)) {
    $_SESSION['delete_success'] = "Incident report deleted successfully.";
} else {
    $_SESSION['delete_error'] = "Something went wrong while deleting the report. Please try again.";
}
mysqli_stmt_close($deleteStmt);

// ================== REDIRECT BACK ==================
// Send the admin back to wherever they came from (preserves any active month/year filter),
// falling back to the main reports page if no referrer is available.
$redirectTo = 'reports.php';
if (!empty($_SERVER['HTTP_REFERER'])) {
    $refererHost = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
    if ($refererHost === $_SERVER['HTTP_HOST']) {
        $redirectTo = $_SERVER['HTTP_REFERER'];
    }
}

header("Location: " . $redirectTo);
exit();