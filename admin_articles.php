<?php
session_start();
// Check kung admin
if(!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin'){
    header("Location: login.php");
    exit();
}

include 'db_connection.php';

// Logic para sa Pag-delete
if(isset($_GET['delete_id'])){
    $id = (int)$_GET['delete_id'];
    $stmt = mysqli_prepare($conn, "DELETE FROM articles WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: admin_articles.php?status=deleted");
    exit();
}

// Kunin lahat ng articles mula sa database
$result = mysqli_query($conn, "SELECT * FROM articles ORDER BY date_published DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Articles | Admin Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1e3a8a;
            --primary-hover: #172554; /* Mas madilim na blue para sa hover */
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

        .sidebar { 
            width: 280px; 
            background-color: var(--sidebar-color); 
            border-right: 1px solid #e5e7eb;
            flex-shrink: 0; 
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
            text-decoration: none;
        }

        .nav-link.active { background-color: #eff6ff; color: var(--primary-color); }
        .nav-link:hover { background-color: #f3f4f6; color: var(--primary-color); }
        .nav-link i { font-size: 1.2rem; margin-right: 12px; }

        .main-content { flex-grow: 1; padding: 2.5rem; }

        .article-table-card {
            background: #fff;
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .btn-add {
            background-color: var(--primary-color);
            color: white;
            border-radius: 8px;
            padding: 10px 20px;
            transition: all 0.3s ease; /* Para smooth ang transition */
            border: none;
        }

        /* Inayos na Hover effect */
        .btn-add:hover { 
            background-color: var(--primary-hover); 
            color: white; 
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
            transform: translateY(-1px);
        }

        .status-badge { font-size: 0.75rem; padding: 5px 10px; border-radius: 20px; }
        .article-modal-body img { max-height: 350px; object-fit: cover; }
    </style>
</head>
<body>

<?php include 'includes/admin_nav.php'; ?>

<div class="main-content">
    <div class="container-fluid">
        <?php if(isset($_GET['status']) && $_GET['status'] == 'deleted'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i> Article deleted successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif(isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i> Article published successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark">Article Management</h2>
                <p class="text-muted">Create and manage content for the user's article page.</p>
            </div>
            <button class="btn btn-add shadow-sm" data-bs-toggle="modal" data-bs-target="#addArticleModal">
                <i class="bi bi-plus-lg me-2"></i> Add New Article
            </button>
        </div>

        <div class="mb-4">
            <div class="input-group shadow-sm" style="max-width: 500px;">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" id="articleSearch" class="form-control border-start-0" placeholder="Search by title, category, or date...">
            </div>
        </div>

        <div class="card article-table-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3">Title</th>
                                <th class="py-3">Category</th>
                                <th class="py-3">Date Published</th>
                                <th class="py-3">Status</th>
                                <th class="py-3 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="articleTableBody">
                            <?php if(mysqli_num_rows($result) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($result)): ?>
                                <?php $statusClass = ($row['status'] === 'Published') ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning'; ?>
                                <tr class="article-row" data-search="<?php echo htmlspecialchars(strtolower($row['title'] . ' ' . $row['category'] . ' ' . date('M d, Y', strtotime($row['date_published']))), ENT_QUOTES); ?>">
                                    <td class="px-4 fw-bold"><?php echo htmlspecialchars($row['title']); ?></td>
                                    <td><span class="badge bg-info-subtle text-info"><?php echo htmlspecialchars($row['category']); ?></span></td>
                                    <td><?php echo date('M d, Y', strtotime($row['date_published'])); ?></td>
                                    <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($row['status']); ?></span></td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#viewArticleModal<?php echo $row['id']; ?>" title="View article">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <a href="?delete_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" title="Delete article" onclick="return confirm('Sigurado ka bang buburahin mo ang artikulong ito?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="bi bi-newspaper fs-2"></i><br>
                                        No articles found.
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <tr id="noSearchResults" style="display: none;">
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-search fs-2"></i><br>
                                    No articles match your search.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if(mysqli_num_rows($result) > 0): ?>
    <?php mysqli_data_seek($result, 0); ?>
    <?php while($row = mysqli_fetch_assoc($result)): ?>
    <div class="modal fade" id="viewArticleModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-newspaper me-2"></i> Article Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 article-modal-body">
                    <?php if(!empty($row['image_path'])): ?>
                        <img src="<?php echo htmlspecialchars($row['image_path']); ?>" class="img-fluid rounded w-100 mb-3" onerror="this.style.display='none'">
                    <?php endif; ?>
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                        <span class="badge bg-info-subtle text-info"><?php echo htmlspecialchars($row['category']); ?></span>
                        <span class="status-badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($row['status']); ?></span>
                        <small class="text-muted"><i class="bi bi-calendar-event me-1"></i><?php echo date('M d, Y, h:i A', strtotime($row['date_published'])); ?></small>
                    </div>
                    <h4 class="fw-bold"><?php echo htmlspecialchars($row['title']); ?></h4>
                    <hr>
                    <h6 class="fw-bold text-uppercase text-muted">Short Description</h6>
                    <p><?php echo $row['short_desc'] ? nl2br(htmlspecialchars($row['short_desc'])) : '<em class="text-muted">No short description.</em>'; ?></p>
                    <h6 class="fw-bold text-uppercase text-muted mt-3">Full Content</h6>
                    <div style="white-space: pre-wrap; text-align: justify; line-height: 1.7;">
                        <?php echo $row['full_content'] ? htmlspecialchars($row['full_content']) : '<em class="text-muted">No content.</em>'; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
<?php endif; ?>

<div class="modal fade" id="addArticleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Create New Article</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="process_article.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Article Title</label>
                            <input type="text" class="form-control" name="title" placeholder="e.g. Benefits of Gender Sensitivity" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Category</label>
                            <select class="form-select" name="category">
                                <option>Education</option>
                                <option>Legal</option>
                                <option>Wellness</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Thumbnail Image</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Short Description</label>
                            <textarea class="form-control" name="short_desc" rows="2" placeholder="Brief summary for the card..."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Full Content</label>
                            <textarea class="form-control" name="full_content" rows="6" placeholder="Write the whole article here..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Publish Article</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('articleSearch').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#articleTableBody tr.article-row');
    let visible = 0;
    rows.forEach(row => {
        let show = row.dataset.search.includes(filter);
        row.style.display = show ? "" : "none";
        if (show) visible++;
    });
    document.getElementById('noSearchResults').style.display = (visible === 0) ? "" : "none";
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>