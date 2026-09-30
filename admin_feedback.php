<?php
session_start();

// 1. Security Check
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// 2. Database Connection
include 'config.php';

// 3. Fetch Feedback
$query = "SELECT * FROM `feedback` ORDER BY id DESC"; 
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query Failed: " . mysqli_error($conn));
}

$totalFeedback = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Feedback | Admin Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1e3a8a; /* Deep Navy Blue */
            --sidebar-color: #ffffff;
            --bg-color: #f3f4f6;
            --text-main: #1f2937;
        }

        body { 
            display: flex; 
            min-height: 100vh; 
            background-color: var(--bg-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-main);
        }

        /* --- SIDEBAR (UNIFORM) --- */
        .sidebar { 
            width: 280px; 
            background-color: var(--sidebar-color); 
            border-right: 1px solid #e5e7eb;
            flex-shrink: 0; 
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
        }

        .sidebar-header {
            padding: 2rem 1.5rem;
            border-bottom: 1px solid #f3f4f6;
        }

        .nav-link {
            padding: 0.8rem 1.5rem;
            color: #4b5563;
            font-weight: 500;
            display: flex;
            align-items: center;
            border-radius: 8px;
            margin: 0.2rem 1rem;
            transition: all 0.2s;
            text-decoration: none;
        }

        .nav-link i {
            font-size: 1.2rem;
            margin-right: 12px;
        }

        .nav-link:hover {
            background-color: #f3f4f6;
            color: var(--primary-color);
        }

        .nav-link.active {
            background-color: #eff6ff;
            color: var(--primary-color);
        }

        .logout-btn {
            margin-top: auto;
            border-top: 1px solid #f3f4f6;
            padding: 1.5rem;
        }

        /* --- MAIN CONTENT --- */
        .main-content { 
            flex-grow: 1; 
            padding: 2.5rem; 
        }

        .card-custom { 
            border: none; 
            border-radius: 15px; 
            background: white; 
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); 
        }

        .table thead { 
            background-color: #f8fafc; 
            color: #475569; 
            font-size: 0.85rem; 
            text-transform: uppercase; 
        }

        .feedback-msg { 
            max-width: 400px; 
            word-wrap: break-word; 
            font-size: 0.9rem;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            background-color: #eff6ff;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-weight: bold;
        }

        /* --- PRINT-ONLY HEADER (hidden on screen) --- */
        .print-header {
            display: none;
        }

        /* --- PRINT STYLES --- */
        @media print {
            /* Hide everything not meant for the printed summary */
            .sidebar,
            #feedbackSearch,
            .input-group,
            .btn-outline-primary,
            th.text-center:last-child,
            td.text-center:last-child {
                display: none !important;
            }

            body {
                display: block;
                background: #fff;
            }

            .main-content {
                padding: 0;
            }

            .card-custom {
                box-shadow: none;
                border-radius: 0;
            }

            .print-header {
                display: block;
                margin-bottom: 1.5rem;
                border-bottom: 2px solid var(--primary-color);
                padding-bottom: 1rem;
            }

            .print-header h2 {
                margin: 0;
                color: var(--primary-color);
            }

            .print-header p {
                margin: 0.25rem 0 0;
                color: #555;
                font-size: 0.9rem;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            th, td {
                border-bottom: 1px solid #ddd;
                padding: 8px;
            }
        }
    </style>
</head>
<body>

<?php include 'includes/admin_nav.php'; ?>

<div class="main-content">
    <div class="container-fluid">

        <!-- Shown only when printing -->
        <div class="print-header">
            <h2>Student Feedback Report</h2>
            <p>Created on <?php echo date('F d, Y \a\t h:i A'); ?> &middot; Total records: <?php echo $totalFeedback; ?></p>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Student Feedback</h2>
                <p class="text-muted">Review messages and suggestions from the student feedback.</p>
            </div>
            <button class="btn btn-outline-primary shadow-sm rounded-pill px-4" onclick="window.print()">
                <i class="bi bi-printer me-2"></i> Print Report
            </button>
        </div>

        <div class="mb-4">
            <div class="input-group shadow-sm" style="max-width: 500px;">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" id="feedbackSearch" class="form-control border-start-0" placeholder="Search by username or content...">
            </div>
        </div>

        <div class="card card-custom overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th class="px-4 py-3">Sender</th>
                                <th class="py-3">Feedback Message</th>
                                <th class="py-3 text-center">Date Submitted</th>
                                <th class="py-3 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="feedbackTableBody">
                            <?php if(mysqli_num_rows($result) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td class="px-4">
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar me-3">
                                                <?php echo strtoupper(substr($row['user_name'], 0, 1)); ?>
                                            </div>
                                            <span class="fw-bold text-dark"><?php echo htmlspecialchars($row['user_name']); ?></span>
                                        </div>
                                    </td>
                                    <td class="text-muted feedback-msg">
                                        <?php echo nl2br(htmlspecialchars($row['message'])); ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border p-2">
                                            <i class="bi bi-clock-history me-1 text-primary"></i>
                                            <?php 
                                                echo isset($row['date_sent']) 
                                                     ? date('M d, Y', strtotime($row['date_sent'])) 
                                                     : 'No Date'; 
                                            ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-light border rounded-pill px-3">
                                            <i class="bi bi-check2-circle me-1"></i> Mark Read
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-chat-left-dots fs-1 d-block mb-3"></i>
                                            No feedback records found.
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Real-time Search Functionality
    document.getElementById('feedbackSearch').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#feedbackTableBody tr');
        rows.forEach(row => {
            let text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? "" : "none";
        });
    });
</script>
</body>
</html>