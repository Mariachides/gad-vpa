<?php
session_start();
if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}
include 'accomplishment_data.php';
if (!isset($reports)) { $reports = []; }
usort($reports, function($a, $b) { return strtotime($b['date']) - strtotime($a['date']); });
$activePage = 'user_gad_events.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events & Accomplishments - GAD VPA</title>
    
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
            background-color: var(--bg-light); 
            font-family: 'Inter', sans-serif;
            margin: 0; 
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .compact-header {
            background: var(--gradient-purple);
            padding: 12px 0;
            color: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .navbar-brand { 
            font-weight: 700;
            font-size: 1.15rem;
            color: white !important;
            text-decoration: none;
        }

        main { flex: 1; }

        /* --- MALIIT NA CARDS (COMPACT DESIGN) --- */
        .card { 
            border: none; 
            border-radius: 12px; 
            transition: all 0.3s ease; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.04);
            max-width: 320px; /* Nilimitahan ang lapad ng box */
            margin: 0 auto;
        }
        
        .card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 10px 20px rgba(111, 66, 193, 0.12);
        }

        .card-body {
            padding: 1.25rem !important; /* Binawasan ang padding sa loob */
        }

        .card-title {
            font-size: 1rem; /* Mas maliit na title */
            font-weight: 700;
            margin-bottom: 5px;
        }

        .card-text {
            font-size: 0.85rem; /* Mas maliit na details text */
            line-height: 1.4;
        }

        .btn-purple { 
            background: var(--gradient-purple);
            border: none;
            color: white; 
            font-size: 0.8rem; /* Mas maliit na button text */
            padding: 8px;
        }

        /* --- FOOTER (SCREENSHOT STYLE) --- */
        footer { 
            background-color: #6f42c1; 
            color: #fff; 
            padding: 20px 0; 
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<?php include 'includes/user_nav.php'; ?>

<main class="main-content">
    <div class="container py-4">
    <div class="text-center mb-4">
        <h3 class="fw-bold text-dark" style="color: #6f42c1 !important;">Accomplishment Events and Reports</h3>
        <p class="text-muted small">Latest activities and milestones of the GAD Office.</p>
    </div>

    <div class="row g-3 justify-content-center">
        <?php if(!empty($reports)): ?>
            <?php foreach($reports as $r): ?>
                <div class="col-sm-6 col-md-4 col-lg-3"> <div class="card h-100">
                        <div class="card-body d-flex flex-column text-center">
                            <h6 class="card-title text-dark"><?php echo htmlspecialchars($r['title']); ?></h6>
                            <small class="text-muted mb-2 d-block" style="font-size: 0.75rem;">
                                <i class="bi bi-calendar-event text-purple me-1"></i>
                                <?php echo date("M d, Y", strtotime($r['date'])); ?>
                            </small>
                            <p class="card-text text-secondary mb-3">
                                <?php echo htmlspecialchars(substr($r['details'], 0, 80)) . '...'; ?>
                            </p>
                            <button class="btn btn-purple btn-sm mt-auto rounded-pill fw-bold" 
                                    onclick='viewEvent(<?php echo json_encode($r); ?>)'>
                                View Details
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <p class="text-muted small">No records found.</p>
            </div>
        <?php endif; ?>
    </div>
    </div>

    <footer class="text-center mt-4">
        <div class="container">
            <span>© 2025 GAD VPA | Cavite State University - Naic Campus</span>
        </div>
    </footer>
</main>

<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-body p-4">
                <h5 class="fw-bold" id="viewTitle" style="color: #6f42c1;"></h5>
                <small class="text-muted" id="viewDate"></small>
                <hr>
                <p id="viewDetails" class="text-secondary small" style="white-space: pre-wrap;"></p>
                <div class="text-end">
                    <button type="button" class="btn btn-light btn-sm border rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function viewEvent(data){
    document.getElementById('viewTitle').innerText = data.title;
    document.getElementById('viewDate').innerText = data.date;
    document.getElementById('viewDetails').innerText = data.details;
    new bootstrap.Modal(document.getElementById('viewModal')).show();
}
</script>
</body>
</html>