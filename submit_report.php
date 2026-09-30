<?php
session_start();
include 'config.php'; // Make sure $conn exists

if (isset($_POST['submit_report'])) {

    // Get form data
    $title = $_POST['title'];
    $description = $_POST['description'];
    $incident_date = $_POST['incident_date'];
    $anonymous = isset($_POST['anonymous']) ? 1 : 0;
    $username = $anonymous ? NULL : $_SESSION['username'];

    // Prepare SQL to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO incident_reports (title, description, incident_date, anonymous, reported_by) VALUES (?, ?, ?, ?, ?)");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param(
        "sssis",
        $title,
        $description,
        $incident_date,
        $anonymous,
        $username
    );

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect to success page
        header("Location: report_success.php");
        exit();
    } else {
        echo "Error submitting report: " . $stmt->error;
    }

    $stmt->close();
}
?>
