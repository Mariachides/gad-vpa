<?php
// Shared admin sidebar navigation
// $activePage is optional; defaults to the current page's filename.
if (!isset($activePage)) {
    $activePage = basename($_SERVER['PHP_SELF']);
}
// Helper to toggle the active class on the current page's link
if (!function_exists('adminNavActive')) {
    function adminNavActive($page, $activePage) {
        return ($activePage === $page) ? ' active' : '';
    }
}
?>
<style>
    :root {
        --primary-color: #1e3a8a;
        --primary-hover: #16306f;
        --sidebar-color: #ffffff;
        --bg-color: #f3f4f6;
        --text-main: #1f2937;
    }

    body {
        display: flex;
        min-height: 100vh;
        margin: 0;
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
        display: flex;
        flex-direction: column;
        position: sticky;
        top: 0;
        height: 100vh;
        overflow-y: auto;
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
        text-decoration: none;
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
        font-weight: 700;
    }

    .logout-btn {
        margin-top: auto;
        border-top: 1px solid #f3f4f6;
        padding: 1.5rem;
    }

    /* Content Area */
    .main-content {
        flex-grow: 1;
        padding: 2.5rem;
        min-width: 0;
        margin-left: 0;
    }

    @media (max-width: 768px) {
        .sidebar {
            width: 220px;
        }
    }
</style>

<div class="sidebar">
    <div class="sidebar-header">
        <h5 class="mb-0 text-primary fw-bold"><i class="bi bi-shield-check"></i> ADMIN PORTAL</h5>
        <small class="text-muted">Faculty Management</small>
    </div>

    <ul class="nav nav-pills flex-column mt-3">
        <li class="nav-item"><a href="admin_landing.php" class="nav-link<?php echo adminNavActive('admin_landing.php', $activePage); ?>"><i class="bi bi-grid-1x2"></i> Dashboard</a></li>
        <li class="nav-item"><a href="admin_reports.php" class="nav-link<?php echo adminNavActive('admin_reports.php', $activePage); ?>"><i class="bi bi-journal-text"></i> Incident Reports</a></li>
        <li class="nav-item"><a href="admin_calendar.php" class="nav-link<?php echo adminNavActive('admin_calendar.php', $activePage); ?>"><i class="bi bi-calendar3"></i> School Calendar</a></li>
        <li class="nav-item"><a href="admin_accomplishments.php" class="nav-link<?php echo adminNavActive('admin_accomplishments.php', $activePage); ?>"><i class="bi bi-trophy"></i> Accomplishments</a></li>
        <li class="nav-item"><a href="admin_feedback.php" class="nav-link<?php echo adminNavActive('admin_feedback.php', $activePage); ?>"><i class="bi bi-chat-quote"></i> Student Feedback</a></li>
        <li class="nav-item"><a href="admin_presentation.php" class="nav-link<?php echo adminNavActive('admin_presentation.php', $activePage); ?>"><i class="bi bi-camera-reels"></i> Multimedia Resources</a></li>
        <li class="nav-item"><a href="admin_awareness_brochure.php" class="nav-link<?php echo adminNavActive('admin_awareness_brochure.php', $activePage); ?>"><i class="bi bi-file-earmark-pdf"></i> E-Brochures</a></li>
        <li class="nav-item"><a href="admin_articles.php" class="nav-link<?php echo adminNavActive('admin_articles.php', $activePage); ?>"><i class="bi bi-newspaper"></i> Articles</a></li>
    </ul>

    <div class="logout-btn">
        <a href="logout.php" class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center">
            <i class="bi bi-box-arrow-right me-2"></i> Sign Out
        </a>
    </div>
</div>