<?php
session_start();
// 1. Security Check
if(!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin'){
    header("Location: login.php");
    exit();
}

// 2. Database Connection
include 'config.php';

// 3. Create folder automatically if it doesn't exist
$target_dir = "brochures/";
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// Handle File Upload
if (isset($_POST['upload'])) {
    $display_title = mysqli_real_escape_string($conn, $_POST['title']);
    $original_name = basename($_FILES["brochure_file"]["name"]);
    $file_extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
    
    if($file_extension != "pdf") {
        $error = "Sorry, only PDF files are allowed.";
    } else {
        $new_filename = time() . "_" . $original_name; 
        $target_file = $target_dir . $new_filename;

        if (move_uploaded_file($_FILES["brochure_file"]["tmp_name"], $target_file)) {
            $db_file_name = mysqli_real_escape_string($conn, $display_title);
            $query = "INSERT INTO awareness_brochure (file_name, file_path) VALUES ('$db_file_name', '$target_file')";
            mysqli_query($conn, $query);
            $msg = "Brochure '$display_title' uploaded successfully!";
        } else {
            $error = "Failed to upload file. Check folder permissions.";
        }
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $res = mysqli_query($conn, "SELECT file_path FROM awareness_brochure WHERE id=$id");
    if($row = mysqli_fetch_assoc($res)) {
        if (file_exists($row['file_path'])) {
            unlink($row['file_path']); 
        }
        mysqli_query($conn, "DELETE FROM awareness_brochure WHERE id=$id");
        header("Location: admin_awareness_brochure.php?deleted=1");
        exit();
    }
}

$brochures = mysqli_query($conn, "SELECT * FROM awareness_brochure ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Awareness Brochures | Admin Portal</title>
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

        /* --- SIDEBAR (UNIFORM STYLE) --- */
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
        
        .upload-section { 
            background: white; 
            border-radius: 15px; 
            padding: 25px; 
            margin-bottom: 2rem; 
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .pdf-icon-bg {
            width: 40px;
            height: 40px;
            background-color: #fef2f2;
            color: #dc2626;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
        }
    </style>
</head>
<body>

<?php include 'includes/admin_nav.php'; ?>

<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">E-Brochures & Materials</h2>
                <p class="text-muted">Upload and manage educational PDF materials for the faculty.</p>
            </div>
        </div>

        <?php if(isset($msg)): ?>
            <div class="alert alert-success border-0 shadow-sm d-flex align-items-center mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?php echo $msg; ?>
            </div>
        <?php endif; ?>
        <?php if(isset($error)): ?>
            <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        <?php if(isset($_GET['deleted'])): ?>
            <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4" role="alert">
                <i class="bi bi-trash me-2"></i> Brochure has been deleted successfully.
            </div>
        <?php endif; ?>

        <div class="upload-section">
            <h5 class="fw-bold mb-3 text-primary"><i class="bi bi-cloud-arrow-up me-2"></i> Upload New Brochure</h5>
            <form action="" method="POST" enctype="multipart/form-data" class="row g-3">
                <div class="col-md-5">
                    <label class="form-label small fw-bold text-muted text-uppercase">Display Title</label>
                    <input type="text" name="title" class="form-control bg-light border-0" placeholder="e.g., Understanding Your Rights" required style="height: 45px;">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted text-uppercase">Choose PDF File</label>
                    <input type="file" name="brochure_file" class="form-control bg-light border-0" accept=".pdf" required style="height: 45px; line-height: 32px;">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" name="upload" class="btn btn-primary w-100 fw-bold" style="height: 45px; background-color: var(--primary-color);">
                        <i class="bi bi-plus-lg me-1"></i> Add Brochure
                    </button>
                </div>
            </form>
        </div>

        <div class="card card-custom overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th class="px-4 py-3">Brochure Title</th>
                                <th class="py-3 text-center">Date Uploaded</th>
                                <th class="py-3 text-center">File Access</th>
                                <th class="py-3 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($brochures) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($brochures)): ?>
                                <tr>
                                    <td class="px-4">
                                        <div class="d-flex align-items-center">
                                            <div class="pdf-icon-bg me-3">
                                                <i class="bi bi-file-earmark-pdf-fill fs-5"></i>
                                            </div>
                                            <span class="fw-bold text-dark"><?php echo htmlspecialchars($row['file_name']); ?></span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border p-2 fw-normal">
                                            <i class="bi bi-calendar-event me-1 text-primary"></i>
                                            <?php echo date('M d, Y', strtotime($row['date_uploaded'])); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?php echo $row['file_path']; ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            <i class="bi bi-eye me-1"></i> View PDF
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger border-0 rounded-circle" onclick="return confirm('Delete this brochure permanently?')">
                                            <i class="bi bi-trash3 fs-6"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-book fs-1 d-block mb-3"></i>
                                            No brochures uploaded yet.
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
</body>
</html>