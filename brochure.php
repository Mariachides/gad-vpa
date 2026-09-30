<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$activePage = 'brochure.php';

// Connect to DB and fetch uploaded brochures
include 'config.php';
$brochures = mysqli_query($conn, "SELECT * FROM awareness_brochure ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Awareness Brochures - GAD VPA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

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
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            margin: 0;
        }

        .content-wrapper {
            flex: 1; /* Para itulak ang footer sa pinaka-ibaba */
            padding-top: 50px;
            padding-bottom: 50px;
        }

        /* PROFESSIONAL COMPACT CARDS */
        .brochure-card {
            border: none;
            border-radius: 16px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: #fff;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .brochure-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(111, 66, 193, 0.15) !important;
        }

        .icon-box {
            background-color: #f3effb;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            margin: 0 auto 15px;
            transition: 0.3s;
        }

        .brochure-card:hover .icon-box {
            background: var(--gradient-purple);
        }

        .brochure-card:hover .icon-box i {
            color: white !important;
        }

        .brochure-title {
            font-size: 0.95rem;
            font-weight: 700;
            margin-bottom: 15px;
            height: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2d3436;
        }

        .btn-preview {
            background: var(--gradient-purple);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            padding: 10px;
            transition: 0.3s;
        }

        .btn-preview:hover {
            opacity: 0.9;
            color: white;
        }

        /* --- FOOTER (FOLLOWING SCREENSHOT) --- */
        footer {
            background-color: #6f42c1; /* Solid purple base */
            color: white;
            padding: 20px 0;
            width: 100%;
            font-size: 0.9rem;
        }

        /* MODAL STYLE */
        .modal-content {
            border-radius: 20px;
            border: none;
        }
    </style>
</head>

<body>

<?php include 'includes/user_nav.php'; ?>

<div class="main-content">
    <div class="container content-wrapper">
        <div class="text-center mb-5">
            <h2 class="fw-bold" style="color: #6f42c1; letter-spacing: -1px;">Awareness Brochures</h2>
            <p class="text-muted">Access and download our educational materials for a safer campus.</p>
        </div>

        <div class="row g-4">
            <?php if(mysqli_num_rows($brochures) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($brochures)): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card brochure-card text-center p-4 h-100">
                        <div class="card-body p-0">
                            <div class="icon-box">
                                <i class="bi bi-file-earmark-pdf" style="font-size:1.5rem; color: #6f42c1;"></i>
                            </div>
                            <h6 class="brochure-title"><?php echo htmlspecialchars($row['file_name']); ?></h6>
                            <button class="btn btn-preview w-100 preview-btn"
                                    data-title="<?php echo htmlspecialchars($row['file_name']); ?>"
                                    data-file="<?php echo htmlspecialchars($row['file_path']); ?>">
                                Preview
                            </button>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center text-muted py-5">
                    <i class="bi bi-book fs-1 d-block mb-3"></i>
                    No brochures available yet.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <div class="container text-center">
            <span>© 2025 GAD VPA | Cavite State University - Naic Campus</span>
        </div>
    </footer>
</div>

<div class="modal fade" id="brochureModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 shadow-sm">
                <h5 class="modal-title fw-bold" id="modalTitle" style="color: #6f42c1;"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <iframe id="modalIframe" width="100%" height="600px" style="border:none;"></iframe>
            </div>
            <div class="modal-footer border-0">
                <a id="modalDownload" class="btn btn-preview px-4 w-auto" download>
                    <i class="bi bi-download me-1"></i> Download PDF
                </a>
                <button type="button" class="btn btn-light rounded-pill px-4 border" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const previewButtons = document.querySelectorAll('.preview-btn');
    const modal = new bootstrap.Modal(document.getElementById('brochureModal'));
    const modalTitle = document.getElementById('modalTitle');
    const modalIframe = document.getElementById('modalIframe');
    const modalDownload = document.getElementById('modalDownload');

    previewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const title = this.getAttribute('data-title');
            const file = this.getAttribute('data-file');

            modalTitle.textContent = title;
            modalIframe.src = file;
            modalDownload.href = file;

            modal.show();
        });
    });
</script>

</body>
</html>