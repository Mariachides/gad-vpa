<?php
session_start();
if(!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin'){
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal | Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    :root {
        --primary-color: #1e3a8a; /* Deep Navy Blue */
        --sidebar-color: #ffffff;
        --bg-color: #f3f4f6;
        --text-main: #1f2937;

        /* USER / RECEIVER CHAT COLOR */
        --user-bubble: #dcfce7;
        --user-border: #86efac;

        /* ADMIN / SENDER CHAT COLOR */
        --admin-bubble: #16a34a;
    }

    body { 
        display: flex; 
        min-height: 100vh; 
        background-color: var(--bg-color);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: var(--text-main);
    }

    /* Sidebar Styles */
    .sidebar { 
        width: 280px; 
        background-color: var(--sidebar-color); 
        border-right: 1px solid #e5e7eb;
        flex-shrink: 0; 
        transition: all 0.3s;
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

    /* Content Area */
    .main-content { 
        flex-grow: 1; 
        padding: 2.5rem; 
    }

    .card-feature {
        border: none;
        border-radius: 12px;
        transition: transform 0.2s, box-shadow 0.2s;
        background: #fff;
    }

    .card-feature:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        cursor: pointer;
    }

    .icon-box {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
        background-color: #eff6ff;
        color: var(--primary-color);
        font-size: 1.5rem;
    }

    /* CHAT SYSTEM CUSTOM COLORS */
    .msg-user {
        align-self: flex-start;
        background-color: var(--user-bubble);
        border: 1px solid var(--user-border);
        color: #166534;
        padding: 8px 12px;
        border-radius: 15px 15px 15px 2px;
        max-width: 85%;
        font-size: 0.9rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .msg-admin {
        align-self: flex-end;
        background-color: var(--admin-bubble);
        color: white;
        padding: 8px 12px;
        border-radius: 15px 15px 2px 15px;
        max-width: 85%;
        font-size: 0.9rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .user-item { 
        cursor: pointer; 
        transition: 0.2s; 
        font-size: 0.85rem; 
    }

    .user-item:hover { 
        background-color: #eff6ff; 
    }

    .user-item.active { 
        background-color: #dbeafe; 
        color: #1e3a8a; 
        font-weight: 600; 
    }

    .logout-btn {
        margin-top: auto;
        border-top: 1px solid #f3f4f6;
        padding: 1.5rem;
    }
</style>
</head>
<body>

<?php include 'includes/admin_nav.php'; ?>

<div class="main-content">
    <div class="container-fluid">
        <div class="welcome-section d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold">Welcome back, Prof. <?php echo $_SESSION['username']; ?>!</h2>
                <p class="text-muted">Overview of your school administration management.</p>
            </div>
            <div class="text-end">
                <span class="badge bg-white text-dark border p-2 shadow-sm">
                    <i class="bi bi-clock me-1"></i> <?php echo date('F d, Y'); ?>
                </span>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3">
                    <small class="text-muted fw-bold">TOTAL REPORTS</small>
                    <h3 class="fw-bold mb-0">24</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3">
                    <small class="text-muted fw-bold">PENDING TASKS</small>
                    <h3 class="fw-bold mb-0 text-warning">8</h3>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <?php
            $features = [
                ['name' => 'Incident Reports', 'link' => 'admin_reports.php', 'icon' => 'bi-journal-text', 'desc' => 'Review and manage school documentations.'],
                ['name' => 'Event Calendar', 'link' => 'admin_calendar.php', 'icon' => 'bi-calendar3', 'desc' => 'Update faculty schedules and activities.'],
                ['name' => 'Accomplishments', 'link' => 'admin_accomplishments.php', 'icon' => 'bi-trophy', 'desc' => 'Track teacher and student achievements.'],
                ['name' => 'Feedback System', 'link' => 'admin_feedback.php', 'icon' => 'bi-chat-quote', 'desc' => 'Analyze comments and suggestions.'],
                ['name' => 'Video Gallery', 'link' => 'admin_presentation.php', 'icon' => 'bi-camera-reels', 'desc' => 'Manage educational video presentations.'],
                ['name' => 'E-Brochures', 'link' => 'admin_awareness_brochure.php', 'icon' => 'bi-file-earmark-pdf', 'desc' => 'Publish awareness and info materials.'],
                ['name' => 'Articles', 'link' => 'admin_articles.php', 'icon' => 'bi-newspaper', 'desc' => 'Manage and edit educational articles.']
            ];

            foreach($features as $f){
                echo '<div class="col-md-4">
                        <div class="card card-feature shadow-sm h-100" onclick="location.href=\''.$f['link'].'\'">
                            <div class="card-body p-4">
                                <div class="icon-box"><i class="bi '.$f['icon'].'"></i></div>
                                <h5 class="card-title fw-bold">'.$f['name'].'</h5>
                                <p class="card-text text-muted small">'.$f['desc'].'</p>
                                <div class="text-primary mt-3 small fw-bold">Manage Section <i class="bi bi-arrow-right ms-1"></i></div>
                            </div>
                        </div>
                      </div>';
            }
            ?>
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
                
                <div id="admin-messages" class="p-3 flex-grow-1" style="overflow-y: auto; background: #f8fafc; display: flex; flex-direction: column; gap: 10px;">
                    </div>

                <form id="admin-send-form" class="p-2 border-top bg-white d-flex gap-2">
                    <input type="text" id="admin-msg-input" class="form-control form-control-sm" placeholder="Type a reply..." disabled>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-send"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
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
        fetch('admin_fetch_users.php')
            .then(res => res.json())
            .then(data => {
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
        fetch(`admin_fetch_convo.php?user=${selectedUser}`)
            .then(res => res.text())
            .then(data => {
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

        fetch('admin_send_reply.php', { method: 'POST', body: formData })
            .then(() => {
                adminInput.value = '';
                loadAdminMessages();
            });
    };

    setInterval(fetchUsers, 5000);
    setInterval(loadAdminMessages, 3000);
    fetchUsers(); // Initial call
});
</script>

</body>
</html>