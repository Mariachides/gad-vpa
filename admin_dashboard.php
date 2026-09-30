<?php
session_start();
if(!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin'){
    header("Location: login.php");
    exit();
}
include 'config.php';

$admin = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard - GAD VPA</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<style>
body { background-color: #f5f5f5; }
.navbar-brand { font-weight: bold; }
.card-stats { transition: transform 0.3s; cursor: pointer; position: relative; }
.card-stats:hover { transform: translateY(-5px); box-shadow: 0 6px 15px rgba(0,0,0,0.15); }
.card-icon { font-size: 2.5rem; }
.badge-notify {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 0.8rem;
}
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">GAD VPA Admin</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="admin_reports.php">Reports</a></li>
        <li class="nav-item"><a class="nav-link" href="admin_calendar.php">Calendar</a></li>
        <li class="nav-item"><a class="nav-link" href="admin_gad_event.php">Accomplishments</a></li>
        <li class="nav-item"><a class="nav-link" href="admin_feedback.php">Feedback</a></li>
        <li class="nav-item"><a class="nav-link" href="admin_chatbox.php">Chatbox</a></li>
      </ul>
      <span class="navbar-text text-light">
        Welcome, <?php echo $_SESSION['username']; ?> | <a href="../logout.php" class="text-light">Logout</a>
      </span>
    </div>
  </div>
</nav>

<div class="container mt-4">
    <h2>Admin Dashboard</h2>
    <p>Quick overview of system activities:</p>

    <div class="row g-4 mt-3">
        <!-- Reports -->
        <div class="col-md-4">
            <a href="admin_reports.php" class="text-decoration-none text-dark">
                <div class="card card-stats p-3 position-relative">
                    <span class="badge bg-danger badge-notify" id="reports-badge" style="display:none;"></span>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5>Reports</h5>
                            <h3 id="reports-count">0</h3>
                        </div>
                        <div class="card-icon text-primary"><i class="bi bi-file-earmark-text"></i></div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Calendar -->
        <div class="col-md-4">
            <a href="admin_calendar.php" class="text-decoration-none text-dark">
                <div class="card card-stats p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5>Calendar Events</h5>
                            <h3 id="calendar-count">0</h3>
                        </div>
                        <div class="card-icon text-success"><i class="bi bi-calendar-event"></i></div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Accomplishments -->
        <div class="col-md-4">
            <a href="admin_gad_event.php" class="text-decoration-none text-dark">
                <div class="card card-stats p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5>Accomplishments</h5>
                            <h3 id="accomplishments-count">0</h3>
                        </div>
                        <div class="card-icon text-warning"><i class="bi bi-check2-circle"></i></div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Feedback -->
        <div class="col-md-4">
            <a href="admin_feedback.php" class="text-decoration-none text-dark">
                <div class="card card-stats p-3 position-relative">
                    <span class="badge bg-danger badge-notify" id="feedback-badge" style="display:none;"></span>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5>Feedback</h5>
                            <h3 id="feedback-count">0</h3>
                        </div>
                        <div class="card-icon text-danger"><i class="bi bi-chat-left-text"></i></div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Chat Messages -->
        <div class="col-md-4">
            <a href="admin_chatbox.php" class="text-decoration-none text-dark">
                <div class="card card-stats p-3 position-relative">
                    <span class="badge bg-danger badge-notify" id="chat-badge" style="display:none;"></span>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5>Chat Messages</h5>
                            <h3 id="chat-count">0</h3>
                        </div>
                        <div class="card-icon text-info"><i class="bi bi-chat-dots"></i></div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<script>
function updateDashboard() {
    fetch('admin_dashboard_data.php')
    .then(res => res.json())
    .then(data => {
        if(data.error) return;

        // Update counts
        document.getElementById('reports-count').innerText = data.reports_count;
        document.getElementById('calendar-count').innerText = data.calendar_count;
        document.getElementById('accomplishments-count').innerText = data.accomplishments_count;
        document.getElementById('feedback-count').innerText = data.feedback_count;
        document.getElementById('chat-count').innerText = data.chat_count;

        // Update badges
        const reportsBadge = document.getElementById('reports-badge');
        const feedbackBadge = document.getElementById('feedback-badge');
        const chatBadge = document.getElementById('chat-badge');

        reportsBadge.innerText = data.new_reports > 0 ? `${data.new_reports} new` : '';
        reportsBadge.style.display = data.new_reports > 0 ? 'inline-block' : 'none';

        feedbackBadge.innerText = data.new_feedback > 0 ? `${data.new_feedback} new` : '';
        feedbackBadge.style.display = data.new_feedback > 0 ? 'inline-block' : 'none';

        chatBadge.innerText = data.new_chat > 0 ? `${data.new_chat} new` : '';
        chatBadge.style.display = data.new_chat > 0 ? 'inline-block' : 'none';
    });
}

// First run
updateDashboard();

// Refresh every 30 seconds
setInterval(updateDashboard, 30000);
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>