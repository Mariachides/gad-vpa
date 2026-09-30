<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
include 'config.php';

// --- LOGIC: SAVE OR UPDATE EVENT ---
if (isset($_POST['save_event'])) {
    $id = $_POST['event_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']); 
    $date = $_POST['event_date'];
    $desc = mysqli_real_escape_string($conn, $_POST['description']);

    if (!empty($id)) {
        $query = "UPDATE calendar SET event_name='$title', event_date='$date', description='$desc' WHERE id='$id'";
    } else {
        $query = "INSERT INTO calendar (event_name, event_date, description) VALUES ('$title', '$date', '$desc')";
    }

    if (mysqli_query($conn, $query)) {
        header("Location: admin_calendar.php?msg=success");
        exit();
    }
}

// --- LOGIC: DELETE EVENT ---
if (isset($_POST['delete_event'])) {
    $id = $_POST['event_id'];
    $query = "DELETE FROM calendar WHERE id='$id'";
    if (mysqli_query($conn, $query)) {
        header("Location: admin_calendar.php?msg=deleted");
        exit();
    }
}

// Fetch calendar events
$result = mysqli_query($conn, "SELECT id, event_name AS title, event_date, description FROM calendar ORDER BY event_date ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Calendar | Admin Portal</title>
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

        .nav-link i { font-size: 1.2rem; margin-right: 12px; }
        .nav-link:hover { background-color: #f3f4f6; color: var(--primary-color); }
        .nav-link.active { background-color: #eff6ff; color: var(--primary-color); }

        .main-content { flex-grow: 1; padding: 2.5rem; }
        .card-custom { border: none; border-radius: 15px; background: white; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .table thead th { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; }
        .btn-add { background-color: var(--primary-color); color: white; border-radius: 8px; padding: 10px 20px; }
        .event-title-cell { display: flex; align-items: center; gap: 10px; }
        .event-title-cell .event-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: #eff6ff; color: var(--primary-color); flex-shrink: 0; }
        .desc-cell { max-width: 280px; }
        .desc-cell span { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .calendar-table tbody tr { transition: all 0.15s ease; }
        .calendar-table tbody tr:hover { background-color: #f8fafc; }
        .empty-state-icon { font-size: 2.5rem; color: #cbd5e1; }
    </style>
</head>
<body>

<?php include 'includes/admin_nav.php'; ?>

<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Calendar of Activities</h2>
                <p class="text-muted">Schedule and manage upcoming school events.</p>
            </div>
            <button class="btn btn-add shadow-sm" onclick="openEventModal()">
                <i class="bi bi-plus-circle me-2"></i> Add New Event
            </button>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-<?php echo $_GET['msg'] == 'deleted' ? 'danger' : 'success'; ?> alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="bi <?php echo $_GET['msg'] == 'deleted' ? 'bi-trash' : 'bi-check-circle-fill'; ?> me-2"></i> 
                <?php echo $_GET['msg'] == 'deleted' ? 'Event deleted successfully!' : 'Calendar updated successfully!'; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card card-custom overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle calendar-table">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3">Event Details</th>
                                <th class="py-3">Date</th>
                                <th class="py-3">Description</th>
                                <th class="py-3 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="calendarTableBody">
                            <?php if(mysqli_num_rows($result) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td class="px-4">
                                        <div class="event-title-cell">
                                            <div class="event-icon"><i class="bi bi-calendar-event"></i></div>
                                            <span class="fw-bold text-dark"><?php echo htmlspecialchars($row['title']); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border fw-normal">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            <?php echo date('M d, Y', strtotime($row['event_date'])); ?>
                                        </span>
                                    </td>
                                    <td class="desc-cell">
                                        <span class="text-muted small"><?php echo htmlspecialchars($row['description']); ?></span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-primary"
                                                    onclick='openEventModal(<?php echo json_encode($row); ?>)'>
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <form method="POST" class="d-inline" onsubmit="return confirm('Sigurado ka ba na gusto mong burahin ang event na ito?')">
                                                <input type="hidden" name="event_id" value="<?php echo $row['id']; ?>">
                                                <button type="submit" name="delete_event" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center py-5 text-muted">
                                    <i class="bi bi-calendar-x empty-state-icon"></i><br>
                                    No scheduled activities found.
                                </td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border-0 shadow" method="POST">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="modalTitle">Event Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="event_id" id="event_id">
                <div class="mb-3">
                    <label class="form-label fw-bold small">Event Title</label>
                    <input type="text" name="title" id="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Event Date</label>
                    <input type="date" name="event_date" id="event_date" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="4" required></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 d-flex justify-content-between">
                <div>
                    <button type="submit" name="delete_event" id="deleteBtn" class="btn btn-outline-danger" onclick="return confirm('Sigurado ka ba na gusto mong burahin ang event na ito?')">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                </div>
                <div>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="save_event" class="btn btn-primary">Save Event</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function openEventModal(data = null) {
    const modalElement = document.getElementById('eventModal');
    const modal = new bootstrap.Modal(modalElement);
    const deleteBtn = document.getElementById('deleteBtn');

    if(data) {
        document.getElementById('modalTitle').innerText = "Edit Activity Details";
        document.getElementById('event_id').value = data.id;
        document.getElementById('title').value = data.title;
        document.getElementById('event_date').value = data.event_date;
        document.getElementById('description').value = data.description;
        deleteBtn.style.display = "block"; // Pakita ang delete button sa Edit
    } else {
        document.getElementById('modalTitle').innerText = "Add New School Activity";
        document.getElementById('event_id').value = "";
        document.getElementById('title').value = "";
        document.getElementById('event_date').value = "";
        document.getElementById('description').value = "";
        deleteBtn.style.display = "none"; // Itago ang delete button sa Add
    }
    modal.show();
}
</script>
</body>
</html>