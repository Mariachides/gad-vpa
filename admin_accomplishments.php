<?php
session_start();
// Gumamit ng config.php base sa iyong pangalawang code
include('config.php'); 

if(!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin'){
    header("Location: login.php");
    exit();
}

$message = "";

// --- LOGIC: DELETE ACHIEVEMENT EVENT ---
if(isset($_GET['delete_event'])){
    $id = mysqli_real_escape_string($conn, $_GET['delete_event']);
    if(mysqli_query($conn, "DELETE FROM accomplishments WHERE id='$id'")){
        $message = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                        Event deleted successfully!
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>";
    }
}

// --- LOGIC: DELETE PDF REPORT ---
if(isset($_GET['delete_report'])){
    $id = mysqli_real_escape_string($conn, $_GET['delete_report']);
    
    // Kunin muna ang file path para mabura ang file sa folder
    $file_res = mysqli_query($conn, "SELECT file_path FROM accomplishment_reports WHERE id='$id'");
    if($file_data = mysqli_fetch_assoc($file_res)){
        if(file_exists($file_data['file_path'])){
            unlink($file_data['file_path']); // Binubura ang file sa 'reports/' folder
        }
    }

    if(mysqli_query($conn, "DELETE FROM accomplishment_reports WHERE id='$id'")){
        $message = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                        Report deleted successfully!
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>";
    }
}

// --- FUNCTION: Handle File Upload ---
if(isset($_POST['upload_report'])){
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $quarter = $_POST['quarter'];
    $year = $_POST['year'];
    
    $target_dir = "reports/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_name = time() . "_" . basename($_FILES["report_file"]["name"]);
    $target_file = $target_dir . $file_name;

    if(move_uploaded_file($_FILES["report_file"]["tmp_name"], $target_file)){
        $query = "INSERT INTO accomplishment_reports (title, quarter, year, file_path) VALUES ('$title', '$quarter', '$year', '$target_file')";
        if(mysqli_query($conn, $query)){
            $message = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                            Report uploaded successfully!
                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                        </div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>Failed to upload file.</div>";
    }
}

// Fetch Accomplishments Events
$result_events = mysqli_query($conn, "SELECT * FROM accomplishments ORDER BY date_created DESC");

// Fetch Accomplishment Reports
$result_reports = mysqli_query($conn, "SELECT * FROM accomplishment_reports ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accomplishments & Reports | Admin Portal</title>
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
        }

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
        .nav-link:hover { background-color: #f3f4f6; color: var(--primary-color); }
        .nav-link.active { background-color: #eff6ff; color: var(--primary-color); font-weight: bold; }
        .logout-btn { margin-top: auto; border-top: 1px solid #f3f4f6; padding: 1.5rem; }

        .main-content { flex-grow: 1; padding: 2.5rem; }
        .card-custom { border: none; border-radius: 15px; background: white; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); margin-bottom: 2rem; }
        .achievement-badge { background-color: #fff7ed; color: #9a3412; border: 1px solid #ffedd5; padding: 4px 10px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; }

        .user-item { cursor: pointer; transition: 0.2s; font-size: 0.85rem; padding: 10px; }
        .user-item:hover { background-color: #eff6ff; }
        .user-item.active { background-color: #dbeafe; color: #1e3a8a; font-weight: 600; }
    </style>
</head>
<body>

<?php include 'includes/admin_nav.php'; ?>

<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Accomplishment Reports and Events</h2>
                <p class="text-muted">Tracking school milestones and GAD project reports for CvSU Naic.</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#uploadReportModal">
                    <i class="bi bi-file-earmark-pdf me-2"></i> Upload PDF Report
                </button>
                <button class="btn btn-primary shadow-sm" style="background-color: var(--primary-color);" data-bs-toggle="modal" data-bs-target="#addEventModal">
                    <i class="bi bi-plus-circle me-2"></i> Add Achievement Event
                </button>
            </div>
        </div>

        <?php echo $message; ?>

        <div class="card card-custom">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">Achievement Events</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Title</th>
                                <th>Description</th>
                                <th class="text-center">Date Created</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($result_events)): ?>
                            <tr>
                                <td class="ps-4 fw-bold"><?php echo htmlspecialchars($row['title']); ?></td>
                                <td class="text-muted small"><?php echo htmlspecialchars($row['description']); ?></td>
                                <td class="text-center">
                                    <span class="achievement-badge"><?php echo date('M d, Y', strtotime($row['date_created'])); ?></span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary rounded-circle"><i class="bi bi-pencil"></i></button>
                                    <a href="?delete_event=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger rounded-circle ms-1" onclick="return confirm('Delete this event?')"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card card-custom">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">Recently Uploaded PDF Reports</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Report Title</th>
                                <th>Quarter</th>
                                <th>Year</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($result_reports) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($result_reports)): ?>
                                <tr>
                                    <td class="ps-4"><i class="bi bi-file-pdf text-danger me-2"></i><?php echo $row['title']; ?></td>
                                    <td><?php echo $row['quarter']; ?></td>
                                    <td><?php echo $row['year']; ?></td>
                                    <td class="text-center">
                                        <a href="<?php echo $row['file_path']; ?>" class="btn btn-sm btn-outline-primary" target="_blank">View</a>
                                        <a href="?delete_report=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger ms-1" onclick="return confirm('Delete this PDF report?')"><i class="bi bi-trash"></i></a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center py-4 text-muted">No PDF reports found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="admin-chat-container" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999;">
    <button id="admin-chat-toggle" class="btn btn-primary shadow-lg" style="width: 60px; height: 60px; border-radius: 50%;">
        <i class="bi bi-chat-dots-fill" style="font-size: 1.5rem;"></i>
    </button>
    <div id="admin-chat-window" style="display: none; width: 600px; height: 450px; background: white; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); position: absolute; bottom: 75px; right: 0; overflow: hidden; border: 1px solid #e5e7eb;">
        <div class="row g-0 h-100">
            <div class="col-4 border-end bg-light" style="height: 100%;">
                <div class="p-3 border-bottom bg-white fw-bold">Active Chats</div>
                <div id="user-list" class="list-group list-group-flush" style="overflow-y: auto; height: calc(100% - 55px);"></div>
            </div>
            <div class="col-8 d-flex flex-column h-100">
                <div class="p-3 border-bottom bg-white d-flex justify-content-between align-items-center">
                    <span id="active-chat-user" class="fw-bold">Select a user</span>
                    <button id="close-admin-chat" class="btn-close"></button>
                </div>
                <div id="admin-messages" class="p-3 flex-grow-1" style="overflow-y: auto; background: #f8fafc; display: flex; flex-direction: column; gap: 10px;"></div>
                <form id="admin-send-form" class="p-2 border-top bg-white d-flex gap-2">
                    <input type="text" id="admin-msg-input" class="form-control form-control-sm" placeholder="Type a reply..." disabled>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-send"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addEventModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border-0 shadow" action="save_accomplishment.php" method="POST">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">New Achievement Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold small">Title</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Best Faculty Award" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Description</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Briefly describe the achievement..." required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Date</label>
                    <input type="date" name="date_created" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="submit" class="btn btn-primary px-4">Save Achievement</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="uploadReportModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Upload PDF Accomplishment Report</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Report Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. 2026 Q1 GAD Summary" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Quarter</label>
                            <select name="quarter" class="form-control">
                                <option>1st Quarter</option><option>2nd Quarter</option>
                                <option>3rd Quarter</option><option>4th Quarter</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Year</label>
                            <input type="number" name="year" class="form-control" value="2026" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select PDF File</label>
                        <input type="file" name="report_file" class="form-control" accept=".pdf" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="upload_report" class="btn btn-primary px-4">Upload Now</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Chat script is the same as original
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('admin-chat-toggle');
    const chatWin = document.getElementById('admin-chat-window');
    const userList = document.getElementById('user-list');
    const adminMessages = document.getElementById('admin-messages');
    const adminForm = document.getElementById('admin-send-form');
    const adminInput = document.getElementById('admin-msg-input');
    const activeUserLabel = document.getElementById('active-chat-user');

    let selectedUser = null;

    toggleBtn.onclick = () => chatWin.style.display = chatWin.style.display === 'none' ? 'block' : 'none';
    document.getElementById('close-admin-chat').onclick = () => chatWin.style.display = 'none';

    function fetchUsers() {
        fetch('admin_fetch_users.php').then(res => res.json()).then(data => {
            userList.innerHTML = data.map(user => `
                <div class="list-group-item user-item ${selectedUser === user ? 'active' : ''}" onclick="selectUser('${user}')">
                    <i class="bi bi-person-circle me-2"></i> ${user}
                </div>
            `).join('');
        });
    }

    window.selectUser = (username) => {
        selectedUser = username;
        activeUserLabel.innerText = "Chatting with: " + username;
        adminInput.disabled = false;
        loadAdminMessages();
    };

    function loadAdminMessages() {
        if(!selectedUser) return;
        fetch(`admin_fetch_convo.php?user=${selectedUser}`).then(res => res.text()).then(data => {
            adminMessages.innerHTML = data;
            adminMessages.scrollTop = adminMessages.scrollHeight;
        });
    }

    adminForm.onsubmit = (e) => {
        e.preventDefault();
        if(!selectedUser || !adminInput.value.trim()) return;
        const formData = new FormData();
        formData.append('receiver', selectedUser);
        formData.append('message', adminInput.value);
        fetch('admin_send_reply.php', { method: 'POST', body: formData }).then(() => {
            adminInput.value = '';
            loadAdminMessages();
        });
    };

    setInterval(fetchUsers, 5000);
    setInterval(loadAdminMessages, 3000);
});
</script>
</body>
</html>