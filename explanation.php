<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$activePage = 'explanation.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brief Explanation - GAD VPA</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --primary-purple: #6f42c1;
            --gradient-purple: linear-gradient(135deg, #6f42c1 0%, #8e44ad 100%);
            --bg-light: #f8f7fa;
        }

        body { 
            background-color: var(--bg-light); 
            font-family: 'Inter', sans-serif;
            color: #2d3436;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            margin: 0;
        }

        .compact-header {
            background: var(--gradient-purple);
            padding: 15px 0;
            color: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.25rem;
            letter-spacing: -0.5px;
            color: white !important;
            text-decoration: none;
        }

        .main-content {
            flex: 1;
            padding-bottom: 50px;
        }

        .explanation-container { 
            background: white; 
            padding: 40px; 
            border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
            margin-top: 40px; 
            border: none;
        }

        .text-purple { color: var(--primary-purple); }
        
        .avatar-box {
            background: #000; 
            border-radius: 18px;
            overflow: hidden;
            aspect-ratio: 9/16; 
            display: flex;
            align-items: center;
            justify-content: center;
            border: 4px solid var(--primary-purple);
            box-shadow: 0 8px 20px rgba(111, 66, 193, 0.2);
        }
        
        video {
            width: 100%;
            height: 100%;
            object-fit: contain; 
            background-color: #000;
        }

        .violence-card {
            border-left: 5px solid var(--primary-purple);
            margin-bottom: 20px;
            padding: 15px 20px;
            background-color: #fcfaff;
            border-radius: 0 12px 12px 0;
            transition: 0.3s;
            cursor: pointer;
        }

        .violence-card:hover {
            background-color: #f3effb;
            transform: translateX(5px);
        }

        .violence-card.active {
            background-color: #f0eaf9;
            border-left: 5px solid #5a2da0;
            transform: translateX(5px);
            box-shadow: 0 4px 15px rgba(111, 66, 193, 0.15);
        }

        .violence-card h5 {
            color: #2d3436;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .violence-card.card-placeholder {
            opacity: 0.5;
            border-left: 5px dashed var(--primary-purple);
        }

        /* Highlight for the Submit Incident Report sidebar link */
        .sidebar .nav-link.report-callout {
            position: relative;
        }

        /* Yellow glow from behind the link */
        .sidebar .nav-link.report-callout::after {
            content: '';
            position: absolute;
            inset: -6px;
            background: radial-gradient(circle, rgba(241, 196, 15, 0.65) 0%, rgba(241, 196, 15, 0.18) 55%, rgba(241, 196, 15, 0) 75%);
            filter: blur(5px);
            z-index: -1;
            border-radius: 12px;
            animation: report-glow 1.4s ease-in-out infinite;
        }

        @keyframes report-glow {
            0%, 100% { opacity: 0.6; }
            50% { opacity: 1; }
        }

        footer {
            background-color: #6f42c1;
            color: white;
            padding: 20px 0;
            width: 100%;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<?php include 'includes/user_nav.php'; ?>

<main class="main-content container">
    <div class="explanation-container">
        <div class="row align-items-center">
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="avatar-box">
                    <video id="explanation-video" controls playsinline>
                        <source id="video-source" src="expla/gbvclip.mp4" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
            </div>

            <div class="col-md-8">
                <!-- EDIT: Change the title & description here or via data-title/data-desc on cards -->
                <h2 class="text-purple fw-bold mb-3" id="explanation-title" style="letter-spacing: -1px;">Violence Prevention</h2>
                <p class="text-muted mb-4" id="explanation-desc">Public health approach to stop abuse, injury, and community harm before it starts.</p>

                <!-- EDIT: For each card, update data-title, data-desc, the <h5>, and the <p> -->
                <!-- Also change data-video to the correct file in the expla/ folder -->

                <div class="violence-card active" data-video="expla/gbvclip.mp4" data-title="What is Gender-Based Violence?" data-desc="Harmful acts committed against a person because of their gender or gender-related expectations.">
                    <h5>Gender-Based Violence</h5>
                    <p class="text-muted small mb-0">GBV violates a person’s rights, safety, dignity, and well-being.</p>
                </div>

                <div class="violence-card" data-video="expla/types.mp4" data-title="Types of Gender-Based Violence" data-desc="There are different types of violence you should know:">
                    <h5>Types</h5>
                    <p class="text-muted small mb-0">Physical Violence, Sexual Violence, Emotional or Psychological Abuse, Economic Abuse, Online or Cyber Violence</p>
                </div>

                <div class="violence-card" data-video="expla/preventclip.mp4" data-title="How to Prevent Gender-Based Violence" data-desc="You can help prevent gender-based violence.">
                    <h5>Prevention</h5>
                    <p class="text-muted small mb-0">Helping create inclusive and safe spaces in your school and community.</p>
                </div>

                <div class="violence-card" data-video="expla/howtoclip.mp4" data-title="How to Report Violence Safely and Anonymously" data-desc="You can report it through official and appropriate channels, such as the GAD Office or our reporting system.">
                    <h5>Report Violence</h5>
                    <p class="text-muted small mb-0">Reports will be handled confidentially and in accordance with applicable policies and procedures.</p>
                </div>

                <!-- Card 5: Placeholder — replace data-video, data-title, data-desc, <h5>, and <p> when ready -->
                <div class="violence-card" data-video="expla/support.mp4" data-title="Support Systems and Safe Spaces" data-desc="Support is available through counselors, GAD Office staff, appropriate hotlines, and trusted peer or support groups.">
                    <h5>Support</h5>
                    <p class="text-muted small mb-0">These safe spaces can provide guidance, emotional support, protection, and assistance to help individuals access the support they need and stay safe.</p>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center mt-4">
        <div class="container">
            <span>&copy; 2025 GAD VPA | Cavite State University - Naic Campus</span>
        </div>
    </footer>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var cards = document.querySelectorAll('.violence-card');
    var video = document.getElementById('explanation-video');
    var videoSource = document.getElementById('video-source');
    var titleEl = document.getElementById('explanation-title');
    var descEl = document.getElementById('explanation-desc');

    cards.forEach(function (card) {
        card.addEventListener('click', function () {
            var videoPath = this.getAttribute('data-video');
            var newTitle = this.getAttribute('data-title');
            var newDesc = this.getAttribute('data-desc');

            // If card has no video, just update text
            if (!videoPath) {
                titleEl.textContent = newTitle;
                descEl.innerHTML = newDesc;
                return;
            }

            // Update active state
            cards.forEach(function (c) { c.classList.remove('active'); });
            this.classList.add('active');

            // Swap video source and reload
            videoSource.src = videoPath;
            video.load();
            video.play();

            // Update h2 and paragraph
            titleEl.textContent = newTitle;
            descEl.innerHTML = newDesc;

            // Clear report highlight whenever a new video starts
            var reportLink = document.querySelector('.sidebar a.nav-link[href="report.php"]');
            if (reportLink) { reportLink.classList.remove('report-callout'); }
        });
    });

    // Highlight the "Submit Incident Report" tab when the 4th video
    // (Report Violence -> expla/howtoclip.mp4) reaches ~10 seconds
    var reportLink = document.querySelector('.sidebar a.nav-link[href="report.php"]');
    var glowTimeout = null;

    function removeCallout() {
        if (reportLink) { reportLink.classList.remove('report-callout'); }
        if (glowTimeout) { clearTimeout(glowTimeout); glowTimeout = null; }
    }

    video.addEventListener('timeupdate', function () {
        if (videoSource.src.indexOf('howtoclip.mp4') !== -1 && video.currentTime >= 9.0 && video.currentTime < 10.2) {
            if (reportLink) { reportLink.classList.add('report-callout'); }
            if (glowTimeout) { clearTimeout(glowTimeout); }
            glowTimeout = setTimeout(removeCallout, 3000); // stop after 3 seconds
        }
    });

    // Stop the glow when the video ends
    video.addEventListener('ended', removeCallout);
});
</script>

</body>
</html>
