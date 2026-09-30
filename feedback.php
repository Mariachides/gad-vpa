<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

// --- Handle AJAX submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_submit'])) {
    header('Content-Type: application/json');

    $username = $_SESSION['username'];
    $rating   = trim($_POST['rating'] ?? '');
    $type     = trim($_POST['type'] ?? '');
    $message  = trim($_POST['message'] ?? '');

    if ($rating === '' || $message === '') {
        echo json_encode(['success' => false, 'error' => 'Please complete the required fields.']);
        exit();
    }

    // Store the rating/type alongside the message so nothing is lost,
    // without needing extra columns in the `feedback` table.
    $fullMessage = "[$type] ($rating) " . $message;

    $stmt = mysqli_prepare($conn, "INSERT INTO feedback (user_name, message, date_sent) VALUES (?, ?, NOW())");
    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
        exit();
    }

    mysqli_stmt_bind_param($stmt, "ss", $username, $fullMessage);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    }

    mysqli_stmt_close($stmt);
    exit();
}

$activePage = 'feedback.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback | GAD VPA</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        :root {
            --primary-purple: #6f42c1;
            --gradient-purple: linear-gradient(135deg, #6f42c1 0%, #8e44ad 100%);
            --bg-light: #f8f7fa;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Inter', sans-serif;
            color: #333;
        }

        /* TOP NAVBAR */
        .navbar-custom {
            background: var(--gradient-purple);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 12px 0;
        }

        .navbar-brand {
            font-weight: 700 !important;
            color: white !important;
        }

        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            padding: 20px;
        }

        .text-purple {
            color: var(--primary-purple);
            font-weight: 700;
        }

        .btn-purple {
            background: var(--gradient-purple);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-purple:hover {
            opacity: 0.9;
            color: white;
            transform: translateY(-2px);
        }

        .btn-purple:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.9rem;
            color: #555;
        }

        .form-control, .form-select {
            border-radius: 10px;
            padding: 10px 15px;
            border: 1px solid #e0e0e0;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-purple);
            box-shadow: 0 0 0 0.25rem rgba(111, 66, 193, 0.1);
        }

        /* FOOTER */
        footer {
            background-color: var(--primary-purple);
            color: #fff;
            padding: 20px 0;
            margin-top: 60px;
            font-size: 0.9rem;
        }

        .alert-custom {
            background-color: #f0ebf7;
            border: none;
            color: var(--primary-purple);
            border-radius: 12px;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>

<?php include 'includes/user_nav.php'; ?>

<div class="main-content">
<section class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card">
                <div class="text-center mb-4">
                    <h2 class="text-purple">Feedback Form</h2>
                    <p class="text-muted small">
                        Your feedback helps us improve the GAD system.
                        This form is <strong>anonymous</strong>.
                    </p>
                </div>

                <div id="errorMessage" class="alert alert-danger d-none rounded-3"></div>

                <form id="feedbackForm">
                    <div class="mb-3">
                        <label class="form-label">How satisfied are you with the system?</label>
                        <select class="form-select" name="rating" id="rating" required>
                            <option value="">Select rating</option>
                            <option>Very Satisfied</option>
                            <option>Satisfied</option>
                            <option>Neutral</option>
                            <option>Dissatisfied</option>
                            <option>Very Dissatisfied</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Type of Feedback</label>
                        <select class="form-select" name="type" id="type">
                            <option>General Feedback</option>
                            <option>Bug / Issue</option>
                            <option>Suggestion</option>
                            <option>Content Improvement</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Your Feedback</label>
                        <textarea class="form-control" name="message" id="message" rows="4" placeholder="Write your feedback here..." required></textarea>
                    </div>

                    <div class="alert alert-custom mb-4">
                        <i class="fa-solid fa-lock me-2"></i> This feedback is anonymous. No personal information is collected.
                    </div>

                    <div class="d-grid">
                        <button type="submit" id="submitBtn" class="btn btn-purple">
                            <i class="fa-solid fa-paper-plane me-2"></i> Submit Feedback
                        </button>
                    </div>
                </form>

                <div id="successMessage" class="alert alert-success mt-4 d-none text-center rounded-3 border-0">
                    <i class="fa-solid fa-circle-check me-2"></i> Thank you! Your response has been recorded.
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="text-center mt-4">
    <div class="container">
        <span>© 2025 GAD VPA | Cavite State University - Naic Campus</span>
    </div>
</footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.getElementById("feedbackForm").addEventListener("submit", function (e) {
        e.preventDefault();

        const submitBtn = document.getElementById("submitBtn");
        const errorBox = document.getElementById("errorMessage");
        errorBox.classList.add("d-none");
        submitBtn.disabled = true;

        const formData = new FormData();
        formData.append("ajax_submit", "1");
        formData.append("rating", document.getElementById("rating").value);
        formData.append("type", document.getElementById("type").value);
        formData.append("message", document.getElementById("message").value);

        fetch("feedback.php", {
            method: "POST",
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById("feedbackForm").classList.add("d-none");
                    document.getElementById("successMessage").classList.remove("d-none");
                } else {
                    errorBox.textContent = data.error || "Something went wrong. Please try again.";
                    errorBox.classList.remove("d-none");
                    submitBtn.disabled = false;
                }
            })
            .catch(() => {
                errorBox.textContent = "Could not reach the server. Please try again.";
                errorBox.classList.remove("d-none");
                submitBtn.disabled = false;
            });
    });
</script>

</body>
</html>