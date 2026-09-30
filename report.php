<?php
session_start();

// SECURITY: redirect if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$message = "";

$activePage = 'report.php';

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gad_vpa"; 

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $incident_date = $_POST['incident_date'];
    $anonymous = isset($_POST['anonymous']) ? 1 : 0;
    $reported_by = $anonymous ? null : $_SESSION['username'];
    $status = "Pending";
    $handled_by = NULL;

    $stmt = $conn->prepare("INSERT INTO incident_reports 
        (title, description, incident_date, anonymous, reported_by, created_at, status, handled_by)
        VALUES (?, ?, ?, ?, ?, NOW(), ?, ?)");
    
    $stmt->bind_param("sssissi", $title, $description, $incident_date, $anonymous, $reported_by, $status, $handled_by);

    if ($stmt->execute()) {
        $message = "success";
    } else {
        $message = "error";
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Report | GAD VPA</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --primary-purple: #6f42c1;
            --gradient-purple: linear-gradient(135deg, #6f42c1 0%, #8e44ad 100%);
            --bg-light: #f8f7fa;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-light);
            color: #2d3436;
        }

        /* Compact Header Style */
        .compact-header {
            background: var(--gradient-purple);
            padding: 15px 0;
            color: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.25rem;
            letter-spacing: -0.5px;
            color: white !important;
            text-decoration: none;
        }

        /* Container & Card Style */
        .report-container {
            max-width: 700px;
            margin: 40px auto;
        }

        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        .card-header {
            background: white;
            border-bottom: 1px solid #f0f0f0;
            padding: 20px;
            text-align: center;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #555;
            margin-bottom: 8px;
        }

        /* Input Styles */
        .form-control, .form-select {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            font-size: 0.95rem;
            background-color: #fcfcfc;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-purple);
            box-shadow: 0 0 0 3px rgba(111, 66, 193, 0.1);
            background-color: white;
        }

        /* Button Style */
        .btn-purple {
            background: var(--gradient-purple);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 10px;
            font-weight: 700;
            transition: all 0.3s;
        }

        .btn-purple:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(111, 66, 193, 0.3);
            color: white;
        }

        .btn-cancel {
            color: #888;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .btn-cancel:hover { color: #555; }

        .form-check-input:checked {
            background-color: var(--primary-purple);
            border-color: var(--primary-purple);
        }

        footer {
            padding: 30px 0;
            color: #999;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>

<?php include 'includes/user_nav.php'; ?>

<div class="main-content">
    <div class="container report-container">
        <div class="card">
        <div class="card-header">
            <h4 class="fw-bold mb-1" style="color: var(--primary-purple);">Incident Report Form</h4>
            <p class="text-muted small mb-0">Mangyaring ibigay ang mga detalye ng insidente nang tapat.</p>
        </div>
        
        <div class="card-body p-4">
            <?php if ($message == "success"): ?>
                <div class="alert alert-success border-0 shadow-sm mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <strong>Salamat!</strong> Matagumpay na naipadala ang iyong report.
                </div>
            <?php elseif ($message == "error"): ?>
                <div class="alert alert-danger border-0 shadow-sm mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Error!</strong> May problema sa pagpapadala. Pakisubukang muli.
                </div>
            <?php endif; ?>

            <form action="report.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">URI NG INSIDENTE</label>
                    <select class="form-select" name="title" required>
                        <option value="" selected disabled>Pumili ng uri...</option>
                        <option value="Harassment">Harassment</option>
                        <option value="Physical Violence">Physical Violence</option>
                        <option value="Emotional/Psychological Abuse">Emotional/Psychological Abuse</option>
                        <option value="Cyber Violence">Cyber Violence</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">PETSA NG INSIDENTE</label>
                    <input type="date" name="incident_date" class="form-control" required>
                </div>

                <div class="mb-4">
                    <label class="form-label">DETALYADONG DESKRIPSYON</label>
                    <textarea name="description" class="form-control" rows="5" placeholder="Ikuwento ang nangyari..." required></textarea>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="anonymous" id="anonymous">
                    <label class="form-check-label text-muted small" for="anonymous">
                        I-report ito bilang <strong>Anonymous</strong> (Hindi makikita ang iyong pangalan)
                    </label>
                </div>

                <div class="p-3 rounded-3 mb-4" style="background-color: #f3effb; border-left: 4px solid var(--primary-purple);">
                    <p class="small mb-0 text-dark">
                        <i class="bi bi-info-circle-fill me-2" style="color: var(--primary-purple);"></i>
                        Ang lahat ng impormasyon ay ituturing na <strong>Confidential</strong> sa ilalim ng GAD policies.
                    </p>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <a href="index.php" class="btn-cancel"><i class="bi bi-arrow-left me-1"></i> Bumalik</a>
                    <button type="submit" class="btn btn-purple shadow-sm">
                        I-submit ang Report <i class="bi bi-send-fill ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
    </div>

    <footer class="text-center mt-4">
        <div class="container">
            <p class="mb-1">&copy; 2026 GAD VPA - CvSU Naic Campus</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="#" class="text-decoration-none text-muted">Privacy</a>
                <span>&bull;</span>
                <a href="#" class="text-decoration-none text-muted">Support</a>
            </div>
        </div>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>