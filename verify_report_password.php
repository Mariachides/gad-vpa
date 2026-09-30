<?php
session_start();
header('Content-Type: application/json');

// Siguraduhing naka-login bilang admin bago kahit ano pang mangyari
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

include 'config.php';

// Kunin ang mga sent na data (report ID at password na in-type)
$reportId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$password = $_POST['password'] ?? '';

if (!$reportId || $password === '') {
    echo json_encode(['success' => false, 'message' => 'Missing data']);
    exit();
}

// 1) Kunin ang naka-store na password hash ng kasalukuyang naka-login na admin
$stmt = mysqli_prepare($conn, "SELECT password FROM users WHERE username = ? AND role = 'admin'");
mysqli_stmt_bind_param($stmt, "s", $_SESSION['username']);
mysqli_stmt_execute($stmt);
$adminResult = mysqli_stmt_get_result($stmt);
$adminRow = mysqli_fetch_assoc($adminResult);
mysqli_stmt_close($stmt);

if (!$adminRow) {
    echo json_encode(['success' => false, 'message' => 'Admin account not found.']);
    exit();
}

// 2) I-verify ang in-type na password laban sa naka-store na hash
if (!password_verify($password, $adminRow['password'])) {
    echo json_encode(['success' => false, 'message' => 'Maling password. Hindi maaaring ipakita ang detalye.']);
    exit();
}

// 3) Tama ang password — ngayon lang kukunin ang description mula sa database
$stmt = mysqli_prepare($conn, "SELECT description FROM incident_reports WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $reportId);
mysqli_stmt_execute($stmt);
$reportResult = mysqli_stmt_get_result($stmt);
$reportRow = mysqli_fetch_assoc($reportResult);
mysqli_stmt_close($stmt);

if (!$reportRow) {
    echo json_encode(['success' => false, 'message' => 'Report not found.']);
    exit();
}

echo json_encode(['success' => true, 'description' => $reportRow['description']]);