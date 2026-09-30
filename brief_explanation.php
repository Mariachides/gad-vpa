<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brief Explanation - GAD VPA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; color: #333; }
        .navbar { background-color: #6f42c1; }
        .navbar-brand, .navbar-nav .nav-link { color: #fff !important; }
        .section-padding { padding: 60px 0; }
        .text-purple { color: #6f42c1; }
        footer { background-color: #6f42c1; color: #fff; padding: 20px 0; margin-top: 40px; }
        .explanation-card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">GAD VPA</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<section class="section-padding">
    <div class="container">
        <div class="explanation-card">
            <h1 class="text-purple mb-4">Brief Explanation of the System</h1>
            <p class="lead">The <strong>GAD Violence Prevention and Awareness (VPA) System</strong> is a dedicated digital platform designed for the CvSU-Naic community.</p>
            
            <hr>

            <h3 class="text-purple mt-4">Our Core Purpose</h3>
            <p>This system serves as a bridge between students/staff and the Gender and Development (GAD) office. It focuses on three main pillars:</p>
            <ul>
                <li><strong>Awareness:</strong> Providing educational materials and legislative information (like RA 9262) to empower individuals.</li>
                <li><strong>Prevention:</strong> Scheduling workshops and seminars to foster a culture of respect and safety.</li>
                <li><strong>Response:</strong> Offering a secure, confidential environment for reporting incidents and seeking immediate virtual assistance.</li>
            </ul>

            <h3 class="text-purple mt-4">Confidentiality & Security</h3>
            <p>We understand the sensitivity of gender-based issues. The system is built with <strong>end-to-end encryption</strong> and options for <strong>anonymous reporting</strong>, ensuring that your identity remains protected while you seek the support you need.</p>
            
            <div class="mt-5">
                <a href="index.php" class="btn btn-primary" style="background-color: #6f42c1; border: none;">Back to Dashboard</a>
            </div>
        </div>
    </div>
</section>

<footer>
    <div class="container text-center">
        <p>&copy; 2025 GAD VPA </p>
    </div>
</footer>

</body>
</html>