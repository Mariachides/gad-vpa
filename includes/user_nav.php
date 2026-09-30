<?php
// ============================================================
// SHARED USER LAYOUT: Top Navbar + Sidebar
// Set $activePage = 'filename.php' BEFORE including this file
// to highlight the current page in the sidebar.
// ============================================================
if(!isset($activePage)){
    $activePage = '';
}

$navUserPages = [
    'presentation_videos.php'     => ['fa-solid fa-circle-play', 'Multimedia'],
    'report.php'                  => ['fa-solid fa-file-shield', 'Submit Incident Report'],
    'calendar.php'                => ['fa-solid fa-calendar-check', 'Calendar of Activities'],
    'brochure.php'                => ['fa-solid fa-book-open', 'Awareness Brochure'],
    'user_gad_events.php'         => ['fa-solid fa-star', 'Accomplishment Events and Reports'],
    'articles.php'                => ['fa-solid fa-newspaper', 'Articles'],
    'explanation.php'             => ['fa-solid fa-lightbulb', 'Brief Explanation'],
    'avatar.php'                  => ['fa-solid fa-robot', 'Talk to Virtual Assistant'],
];
?>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Montserrat:wght@700;800&family=Playfair+Display:ital,wght@0,700;1,700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<style>
:root {
    --primary-purple: #6f42c1;
    --gradient-purple: linear-gradient(135deg, #6f42c1 0%, #8e44ad 100%);
    --bg-light: #f8f9fa;
}

.navbar-custom {
    background: var(--gradient-purple);
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
.navbar-custom .navbar-brand,
.navbar-custom .nav-link { color: #fff; }
.navbar-custom .nav-link:hover { color: #ffd700; }

.brand-container { display: flex; flex-direction: column; align-items: flex-start; }
.brand-main { font-family: 'Montserrat', sans-serif; font-size: 1.7rem; letter-spacing: -0.8px; line-height: 1; }
.brand-gad { font-weight: 800; color: #ffffff; }
.brand-vpa { font-family: 'Montserrat', sans-serif; font-weight: 400; color: #ffffff; margin-left: 8px; letter-spacing: 3px; }
.brand-sub { font-family: 'Montserrat', sans-serif; font-size: 0.62rem !important; font-weight: 700; letter-spacing: 3.5px; color: rgba(255,255,255,0.85); text-transform: uppercase; display: block; margin-top: 4px; }

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
.sidebar .user-link { text-decoration: none; display: block; margin-bottom: 25px; }
.sidebar .user-box {
    color: #fff;
    text-align: center;
    padding: 20px 10px;
    border-radius: 12px;
    transition: all 0.3s ease;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}
.sidebar .user-box i.fa-circle-user { font-size: 3.5rem; margin-bottom: 10px; color: #ffd700; }
.sidebar .nav-link {
    color: rgba(255,255,255,0.8);
    padding: 12px 15px;
    margin-bottom: 5px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    transition: all 0.3s ease;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 600;
}
.sidebar .nav-link i { width: 30px; font-size: 1.1rem; margin-right: 10px; text-align: center; }
.sidebar .nav-link:hover,
.sidebar .nav-link.active { background-color: #5a359a; color: #fff; transform: translateX(8px); }

.main-content { margin-left: 280px; padding: 40px; margin-top: 56px; min-height: 100vh; }

@media (max-width: 991px) {
    .navbar-custom { position: relative; }
    .sidebar { width: 100%; position: relative; height: auto; top: 0; padding-top: 80px; }
    .main-content { margin-left: 0; margin-top: 0; }
}
</style>

<nav class="navbar navbar-expand-lg navbar-custom fixed-top">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="index.php">
            <div class="brand-container">
                <div class="brand-main">
                    <span class="brand-gad">GAD</span><span class="brand-vpa">VPA</span>
                </div>
                <small class="brand-sub">YOUR SAFE HAVEN</small>
            </div>
        </a>
        <div class="collapse navbar-collapse justify-content-end" id="topNavbar">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="index.php"><i class="fa-solid fa-house me-1"></i> Home</a></li>
                <li class="nav-item"><a class="nav-link" href="about.php"><i class="fa-solid fa-circle-info me-1"></i> About</a></li>
                <li class="nav-item"><a class="nav-link" href="feedback.php"><i class="fa-solid fa-comment-dots me-1"></i> Feedback</a></li>
                <li class="nav-item"><a class="nav-link text-warning" href="logout.php" onclick="return confirm('Logout?');"><i class="fa-solid fa-right-from-bracket me-1"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="sidebar">
    <a href="profile.php" class="user-link">
        <div class="user-box">
            <i class="fa-solid fa-circle-user"></i>
            <h5 class="mb-0 mt-2"><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></h5>
            <small style="font-size: 0.7rem; opacity: 0.8; text-transform: uppercase;">View Profile <i class="fa-solid fa-chevron-right ms-1" style="font-size: 0.6rem;"></i></small>
        </div>
    </a>
    <ul class="nav flex-column">
        <?php foreach($navUserPages as $navFile => $navItem): ?>
        <li class="nav-item">
            <a class="nav-link<?php echo ($activePage === $navFile) ? ' active' : ''; ?>" href="<?php echo $navFile; ?>">
                <i class="<?php echo $navItem[0]; ?>"></i> <?php echo $navItem[1]; ?>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>
</div>