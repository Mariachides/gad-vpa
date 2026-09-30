<?php
session_start();
if(!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin'){
    header("Location: login.php");
    exit();
}

// 1. Load existing data as a LIST (Array of Arrays)
$gad_data = []; 
if (file_exists('data.php')) {
    include 'data.php';
    if (!is_array($gad_data)) {
        $gad_data = [];
    }
}

// --- LOGIC PARA SA PAGBURA (DELETE) ---
if (isset($_GET['delete'])) {
    $index_to_delete = $_GET['delete'];
    if (isset($gad_data[$index_to_delete])) {
        // Burahin ang video file kung mayroon
        if (!empty($gad_data[$index_to_delete]['video_file']) && file_exists($gad_data[$index_to_delete]['video_file'])) {
            unlink($gad_data[$index_to_delete]['video_file']);
        }

        // Alisin sa array
        unset($gad_data[$index_to_delete]);
        // I-reindex ang array para walang butas na index
        $gad_data = array_values($gad_data);

        // I-save muli ang data.php
        $file_content = "<?php\n";
        $file_content .= "// Last Updated: " . date('Y-m-d H:i:s') . "\n";
        $file_content .= "\$gad_data = " . var_export($gad_data, true) . ";\n";
        $file_content .= "?>";
        file_put_contents('data.php', $file_content);

        header("Location: admin_presentation.php?status=deleted");
        exit();
    }
}

if (isset($_POST['save'])) {
    $new_title = $_POST['title'] ?? 'New Resource';
    $new_id = $_POST['video_id'] ?? '';
    $new_desc = $_POST['description'] ?? '';
    
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }

    $video_path = ""; 

    if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] == 0) {
        $v_file_name = time() . '_vid_' . preg_replace("/[^a-zA-Z0-9.]/", "_", basename($_FILES["video_file"]["name"]));
        $v_target_file = $target_dir . $v_file_name;
        if (move_uploaded_file($_FILES["video_file"]["tmp_name"], $v_target_file)) {
            $video_path = $v_target_file;
        }
    }

    $new_entry = [
        'title' => $new_title,
        'video_id' => $new_id,
        'video_file' => $video_path,
        'description' => $new_desc
    ];

    $gad_data[] = $new_entry;

    $file_content = "<?php\n";
    $file_content .= "// Last Updated: " . date('Y-m-d H:i:s') . "\n";
    $file_content .= "\$gad_data = " . var_export($gad_data, true) . ";\n";
    $file_content .= "?>";
    
    if(file_put_contents('data.php', $file_content)){
        header("Location: admin_presentation.php?status=success");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Resources | Admin Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1e3a8a;
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
            margin: 0;
        }

        .sidebar { 
            width: 280px; 
            background-color: var(--sidebar-color); 
            border-right: 1px solid #e5e7eb;
            flex-shrink: 0; 
            position: fixed;
            height: 100vh;
            z-index: 1000;
        }

        .sidebar-header { padding: 2rem 1.5rem; border-bottom: 1px solid #f3f4f6; }

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

        .nav-link i { font-size: 1.2rem; margin-right: 12px; }
        .nav-link:hover { background-color: #f3f4f6; color: var(--primary-color); }
        .nav-link.active { background-color: #eff6ff; color: var(--primary-color); }

        .main-content { 
            flex-grow: 1; 
            padding: 2.5rem; 
            margin-left: 280px; 
        }

        .form-container, .list-container {
            background: #fff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .logout-btn { margin-top: auto; border-top: 1px solid #f3f4f6; padding: 1.5rem; }
    </style>
</head>
<body>

<?php include 'includes/admin_nav.php'; ?>

<div class="main-content">
    <div class="container-fluid">
        <div class="mb-4">
            <h2 class="fw-bold text-primary">Multimedia Management</h2>
            <p class="text-muted">Upload new videos or manage existing ones below.</p>
        </div>

        <?php if(isset($_GET['status'])): ?>
            <?php if($_GET['status'] == 'success'): ?>
                <div class='alert alert-success alert-dismissible fade show' role='alert'>
                    <i class="bi bi-check-circle-fill me-2"></i> Video added successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif($_GET['status'] == 'deleted'): ?>
                <div class='alert alert-warning alert-dismissible fade show' role='alert'>
                    <i class="bi bi-trash-fill me-2"></i> Video has been deleted.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="form-container mb-5">
            <h4 class="fw-bold mb-4">Upload New Video</h4>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-4">
                    <label class="form-label fw-bold"><i class="bi bi-tag-fill me-1"></i> Video Title:</label>
                    <input type="text" name="title" class="form-control form-control-lg" placeholder="Enter video title" required>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-danger"><i class="bi bi-youtube"></i> YouTube Video ID:</label>
                        <input type="text" name="video_id" class="form-control" placeholder="Example: dQw4w9WgXcQ">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-primary"><i class="bi bi-file-earmark-play-fill"></i> Or Upload MP4 Video:</label>
                        <input type="file" name="video_file" class="form-control" accept="video/mp4">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold"><i class="bi bi-info-circle-fill me-1"></i> Description:</label>
                    <textarea name="description" class="form-control" rows="3" required></textarea>
                </div>
                
                <div class="mt-4 text-end">
                    <button type="submit" name="save" class="btn btn-primary px-5 py-2 fw-bold">
                        <i class="bi bi-cloud-arrow-up-fill me-2"></i> Save and Publish
                    </button>
                </div>
            </form>
        </div>

        <div class="list-container">
             <h4 class="fw-bold mb-4"><i class="bi bi-collection-play me-2"></i> Managed Videos</h4>
             <div class="table-responsive">
                 <table class="table table-hover align-middle">
                     <thead class="table-light">
                         <tr>
                             <th>Title</th>
                             <th>Type</th>
                             <th class="text-center">Action</th>
                         </tr>
                     </thead>
                     <tbody>
                         <?php if(empty($gad_data)): ?>
                            <tr><td colspan="3" class="text-center text-muted">No videos found.</td></tr>
                         <?php else: ?>
                            <?php foreach($gad_data as $index => $item): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($item['title']); ?></strong></td>
                                <td>
                                    <?php if(!empty($item['video_id'])): ?> <span class="badge bg-danger">YouTube</span> <?php endif; ?>
                                    <?php if(!empty($item['video_file'])): ?> <span class="badge bg-primary">MP4</span> <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="?delete=<?php echo $index; ?>" 
                                       class="btn btn-outline-danger btn-sm" 
                                       onclick="return confirm('Sigurado ka bang gusto mong burahin ito?')">
                                         <i class="bi bi-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                         <?php endif; ?>
                     </tbody>
                 </table>
             </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>