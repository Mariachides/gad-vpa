<?php
session_start();
include('db_connection.php'); // Siguraduhing tama ang details ng database mo dito

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$activePage = 'articles.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Articles | GAD VPA Shield & Speak</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        :root {
            --primary-purple: #6f42c1;
            --gradient-purple: linear-gradient(135deg, #6f42c1 0%, #8e44ad 100%);
            --bg-light: #f8f9fa;
        }

        body {
            margin: 0;
            background-color: var(--bg-light);
            font-family: 'Inter', sans-serif;
        }

        .navbar-custom {
            background: var(--gradient-purple);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .navbar-custom .navbar-brand,
        .navbar-custom .nav-link { color: #fff; }

        .sidebar {
            position: fixed;
            top: 56px;
            left: 0;
            width: 280px;
            height: calc(100vh - 56px);
            background-color: var(--primary-purple);
            padding: 20px 15px;
            z-index: 1000;
        }

        .sidebar .user-box {
            color: #fff;
            text-align: center;
            padding: 20px 10px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 25px;
        }
        .sidebar .user-box i { font-size: 3.5rem; color: #ffd700; }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 15px;
            margin-bottom: 5px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            transition: 0.3s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: #5a359a;
            color: #fff;
            transform: translateX(8px);
        }
        .sidebar .nav-link i { width: 30px; font-size: 1.1rem; margin-right: 10px; text-align: center; }

        .main-content { margin-left: 280px; padding: 40px; margin-top: 56px; min-height: 100vh; }
        
        .article-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: 0.3s;
            background: #fff;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        .article-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(111, 66, 193, 0.1);
        }
        .article-img {
            height: 200px;
            width: 100%;
            object-fit: cover;
        }
        .badge-gad {
            background-color: #f0ebf7;
            color: var(--primary-purple);
            font-weight: 600;
            font-size: 0.75rem;
        }
        .text-purple { color: var(--primary-purple); }

        /* Compressed and Justified Text for Preview */
        .article-description {
            text-align: justify;
            display: -webkit-box;
            -webkit-line-clamp: 3; /* Limit to 3 lines */
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 15px;
        }

        .btn-outline-purple {
            color: var(--primary-purple);
            border: 2px solid var(--primary-purple);
            background: transparent;
            font-weight: 600;
            transition: 0.3s;
            border-radius: 50px;
        }
        .btn-outline-purple:hover {
            background: var(--primary-purple);
            color: #fff;
        }

        .modal-content { border-radius: 20px; border: none; }
        .modal-header { background: var(--gradient-purple); color: white; border-radius: 20px 20px 0 0; }
        .modal-body img { border-radius: 10px; margin-bottom: 20px; max-height: 350px; object-fit: cover; width: 100%; }
        
        /* Justified Full Text in Modal */
        .modal-body-content {
            text-align: justify;
            line-height: 1.6;
        }

        @media (max-width: 991px) {
            .sidebar { width: 100%; position: relative; height: auto; top: 0; padding-top: 80px; }
            .main-content { margin-left: 0; margin-top: 0; }
        }
    </style>
</head>
<body>

<?php include 'includes/user_nav.php'; ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-purple fw-bold"><i class="fa-solid fa-newspaper me-2"></i> GAD Articles</h2>
    </div>

    <div class="row g-4">
        <?php
        // Kinukuha ang lahat ng articles mula sa database
        $query = "SELECT * FROM articles ORDER BY id DESC";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
        ?>
            <div class="col-md-6 col-lg-4">
                <div class="card article-card h-100">
                    <img src="<?php echo htmlspecialchars($row['image_path']); ?>" class="card-img-top article-img" onerror="this.src='https://via.placeholder.com/400x200?text=Article'">
                    <div class="card-body d-flex flex-column">
                        <span class="badge badge-gad mb-2 align-self-start"><?php echo htmlspecialchars($row['category']); ?></span>
                        <h5 class="fw-bold"><?php echo htmlspecialchars($row['title']); ?></h5>
                        <p class="text-muted small article-description">
                            <?php echo htmlspecialchars($row['short_desc']); ?>
                        </p>
                        <div class="mt-auto">
                            <button class="btn btn-outline-purple btn-sm" data-bs-toggle="modal" data-bs-target="#modalArticle<?php echo $row['id']; ?>">Read More</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modalArticle<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <img src="<?php echo htmlspecialchars($row['image_path']); ?>" class="img-fluid" onerror="this.src='https://via.placeholder.com/800x400?text=GAD+Article'">
                            <div class="modal-body-content p-3">
                                <?php echo nl2br(htmlspecialchars($row['full_content'])); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php 
            }
        } else {
            echo '<div class="col-12 text-center mt-5"><p class="text-muted">No articles published yet.</p></div>';
        }
        ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>