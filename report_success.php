<?php
session_start();
if(isset($_SESSION['last_report'])) {
    $report = $_SESSION['last_report'];
    echo "<h2>Incident Report Submitted Successfully!</h2>";
    echo "<p><strong>Title:</strong> " . htmlspecialchars($report['title']) . "</p>";
    echo "<p><strong>Date:</strong> " . htmlspecialchars($report['incident_date']) . "</p>";
    echo "<a href='report.php' class='btn btn-primary'>Go Back</a>";
} else {
    header("Location: report.php");
    exit();
}
?>
