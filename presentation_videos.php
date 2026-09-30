<?php
// Load ang data file
$gad_data = [];
if (file_exists('data.php')) {
    include 'data.php';
}

if (!is_array($gad_data)) {
    $gad_data = [];
}

// BALIGTARIN ANG ORDER
$gad_data = array_reverse($gad_data);

$activePage = 'presentation_videos.php';

function extractYoutubeId($input) {
    $input = trim($input);
    if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $input, $m)) {
        return $m[1];
    }
    if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $input)) {
        return $input;
    }
    return '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Gallery | GAD VPA</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        :root {
            --primary-purple: #6f42c1;
            --gradient-purple: linear-gradient(135deg, #6f42c1 0%, #8e44ad 100%);
            --bg-light: #f8f7fa;
            --sidebar-width: 280px;
        }

        body { 
            margin: 0; 
            background-color: var(--bg-light); 
            font-family: 'Inter', sans-serif; 
            color: #2d3436;
        }

        /* --- COMPACT HEADER --- */
        .compact-header {
            background: var(--gradient-purple);
            padding: 15px 0;
            color: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1001;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.25rem;
            letter-spacing: -0.5px;
            color: white !important;
            text-decoration: none;
        }

        /* --- SIDEBAR --- */
        .sidebar { 
            position: fixed; 
            top: 60px; 
            left: 0; 
            width: var(--sidebar-width); 
            height: calc(100vh - 60px); 
            background: white; 
            padding: 30px 15px; 
            z-index: 1000;
            border-right: 1px solid #eee;
        }

        .sidebar .nav-link { 
            color: #555; 
            padding: 12px 15px; 
            margin-bottom: 8px; 
            border-radius: 12px; 
            font-weight: 600; 
            font-size: 0.9rem;
            display: flex; 
            align-items: center; 
            transition: all 0.3s; 
            text-decoration: none; 
        }

        .sidebar .nav-link i {
            margin-right: 12px;
            font-size: 1.1rem;
            color: var(--primary-purple);
        }

        .sidebar .nav-link:hover { 
            background-color: #f3effb; 
            color: var(--primary-purple); 
        }

        .sidebar .nav-link.active { 
            background: var(--gradient-purple);
            color: white; 
        }

        .sidebar .nav-link.active i {
            color: white;
        }

        /* --- MAIN CONTENT --- */
        .main-content { 
            margin-left: var(--sidebar-width); 
            padding: 40px; 
            margin-top: 60px; 
        }

        .text-purple { 
            color: var(--primary-purple); 
            font-weight: 700; 
        }

        /* --- VIDEO CARDS --- */
        .gallery-card { 
            background: #fff; 
            border-radius: 16px; 
            border: none; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
            transition: all 0.3s; 
            height: 100%; 
            overflow: hidden; 
        }

        .gallery-card:hover { 
            transform: translateY(-8px); 
            box-shadow: 0 15px 35px rgba(111, 66, 193, 0.12); 
        }

        .ratio video, .ratio iframe { 
            background: #000; 
        }

        .card-body h6 {
            color: #2d3436;
            font-size: 1rem;
        }

        /* --- SEARCH BAR --- */
        .search-wrapper {
            background: white;
            border-radius: 30px;
            padding: 5px 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            max-width: 500px;
            border: 1px solid #eee;
        }

        .form-control-custom {
            border: none;
            padding: 10px;
            font-size: 0.95rem;
            width: 100%;
            outline: none;
        }

        @media (max-width: 991px) {
            .sidebar { width: 100%; position: relative; height: auto; top: 0; border-right: none; padding: 15px; }
            .main-content { margin-left: 0; padding: 20px; margin-top: 0; }
            .compact-header { position: relative; }
        }
    </style>
</head>
<body>

<?php include 'includes/user_nav.php'; ?>

<div class="main-content">
    <div class="mb-5">
        <h2 class="text-purple mb-2"><i class="fa-solid fa-video me-2"></i> GAD Video Gallery</h2>
        <p class="text-muted">Explore our multimedia resources for awareness and education.</p>
    </div>

    <div class="mb-5">
        <div class="search-wrapper d-flex align-items-center">
            <i class="fa-solid fa-magnifying-glass text-muted ms-2"></i>
            <input type="text" id="resourceSearch" class="form-control-custom" placeholder="Search videos or topics...">
        </div>
    </div>

    <div id="no-results-msg" class="text-center py-5 d-none">
        <i class="fa-solid fa-magnifying-glass fa-3x text-muted mb-3 opacity-50"></i>
        <h5 class="text-muted">No videos found.</h5>
    </div>

    <div class="video-container">
        <div class="row g-4">
            <?php if (!empty($gad_data)): ?>
                <?php foreach ($gad_data as $item): ?>
                    <?php if (!empty($item['video_id']) || !empty($item['video_file'])): ?>
                    <div class="col-md-6 col-xl-4 search-item">
                        <div class="card gallery-card">
                            <div class="ratio ratio-16x9">
                                <?php if (!empty($item['video_id'])): ?>
                                    <?php $ytId = extractYoutubeId($item['video_id']); ?>
                                    <?php if ($ytId): ?>
                                    <iframe src="https://www.youtube.com/embed/<?php echo htmlspecialchars($ytId); ?>" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                    <?php else: ?>
                                    <div class="d-flex align-items-center justify-content-center h-100 bg-light text-muted">Invalid video link</div>
                                    <?php endif; ?>
                                <?php elseif (!empty($item['video_file'])): ?>
                                    <video controls><source src="<?php echo htmlspecialchars($item['video_file']); ?>" type="video/mp4"></video>
                                <?php endif; ?>
                            </div>
                            <div class="card-body p-4">
                                <h6 class="fw-bold mb-2 item-title"><?php echo htmlspecialchars($item['title']); ?></h6>
                                <p class="text-muted small mb-0 item-desc"><?php echo htmlspecialchars($item['description']); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-5 w-100 text-muted">
                    <div class="p-5 border rounded-4 bg-white shadow-sm">
                        No videos available at the moment.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('resourceSearch').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let items = document.querySelectorAll('.search-item');
        let visibleCount = 0;

        items.forEach(function(item) {
            let title = item.querySelector('.item-title').innerText.toLowerCase();
            let desc = item.querySelector('.item-desc') ? item.querySelector('.item-desc').innerText.toLowerCase() : "";
            if (title.includes(filter) || desc.includes(filter)) {
                item.style.display = "";
                visibleCount++;
            } else {
                item.style.display = "none";
            }
        });
        document.getElementById('no-results-msg').classList.toggle('d-none', visibleCount > 0);
    });
</script>

</body>
</html> 