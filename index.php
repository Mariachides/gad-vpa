<?php
session_start();
include('db_connection.php'); 

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GAD VPA | Shield & Speak</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Montserrat:wght@700;800&family=Playfair+Display:ital,wght@0,700;1,700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        :root {
            --primary-purple: #6f42c1;
            --gradient-purple: linear-gradient(135deg, #6f42c1 0%, #8e44ad 100%);
            --bg-light: #f8f9fa;
        }

        body {
            margin: 0;
            background-color: var(--bg-light);
            font-family: 'Inter', sans-serif;
        }

        /* --- ENHANCED TYPOGRAPHY SECTION --- */
        .brand-container {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .brand-main {
            font-family: 'Montserrat', sans-serif; /* Modern student-friendly structure */
            font-size: 1.7rem;
            letter-spacing: -0.8px;
            line-height: 1;
        }
        .brand-gad {
            font-weight: 800;
            color: #ffffff;
        }
        .brand-vpa {
            font-family: 'Montserrat', sans-serif; /* Clean simple touch */
            font-weight: 400;
            color: #ffffff;
            margin-left: 8px;
            letter-spacing: 3px;
        }
        .brand-sub {
            font-family: 'Montserrat', sans-serif;
            font-size: 0.62rem !important;
            font-weight: 700;
            letter-spacing: 3.5px; /* Premium letter spacing */
            color: rgba(255, 255, 255, 0.85);
            text-transform: uppercase;
            display: block;
            margin-top: 4px;
        }
        /* ------------------------------------ */

        .announcement-ticker {
            background: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
            padding: 10px 0;
            overflow: hidden;
            white-space: nowrap;
            border-radius: 10px;
            margin-bottom: 20px;
            position: relative;
            display: flex;
            align-items: center;
        }
        .ticker-label {
            background: #ffc107;
            padding: 0 15px;
            font-weight: 800;
            z-index: 2;
            height: 100%;
            display: flex;
            align-items: center;
            position: absolute;
            left: 0;
        }
        .ticker-content {
            display: inline-block;
            padding-left: 100%;
            animation: marquee 20s linear infinite;
            font-weight: 600;
        }
        @keyframes marquee {
            0% { transform: translate(0, 0); }
            100% { transform: translate(-100%, 0); }
        }

        .navbar-custom {
            background: var(--gradient-purple);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .navbar-custom .navbar-brand,
        .navbar-custom .nav-link { color: #fff; }
        .navbar-custom .nav-link:hover { color: #ffd700; }

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
        .sidebar .nav-link:hover { background-color: #5a359a; color: #fff; transform: translateX(8px); }

        .main-content { margin-left: 280px; padding: 40px; margin-top: 56px; min-height: 100vh; }
        .text-purple { color: var(--primary-purple); font-weight: 700; }

        .carousel { border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .carousel img { height: 480px; object-fit: cover; }
        
        .card { border: none; transition: transform 0.3s ease, box-shadow 0.3s ease; border-radius: 15px; }
        .card:hover { transform: translateY(-10px); box-shadow: 0 15px 35px rgba(111, 66, 193, 0.15) !important; }
        .card-report-icon { font-size: 2.5rem; color: var(--primary-purple); margin-bottom: 15px; }
        
        .btn-purple { 
            background: var(--gradient-purple); 
            color: white; 
            border: none;
            border-radius: 8px; 
            padding: 10px 20px; 
            font-weight: 600; 
            text-decoration: none; 
            display: inline-block; 
        }
        .btn-purple:hover { opacity: 0.9; color: #fff; }

        .article-card {
            overflow: hidden;
            background: #fff;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        .article-img {
            height: 200px;
            width: 100%;
            object-fit: cover;
        }
        .badge-gad {
            background-color: #f0ebf7;
            color: var(--primary-purple);
            font-weight: 600;
            font-size: 0.75rem;
        }
        
        .article-description {
            text-align: justify;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 15px;
        }

        .btn-outline-purple {
            color: var(--primary-purple);
            border: 2px solid var(--primary-purple);
            background: transparent;
            font-weight: 600;
            transition: 0.3s;
            border-radius: 50px;
        }
        .btn-outline-purple:hover {
            background: var(--primary-purple);
            color: #fff;
        }

        .modal-body-content {
            text-align: justify;
            line-height: 1.6;
        }

        .footer-square-box {
            background: #ffffff;
            border: 1px solid #e0e0e0;
            padding: 15px;
            height: 100%;
            transition: 0.3s;
            color: #333;
            text-align: center;
        }
        .footer-square-box:hover { border-color: var(--primary-purple); box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .footer-icon-circle {
            width: 40px;
            height: 40px;
            background: #f0ebf7;
            color: var(--primary-purple);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin-bottom: 10px;
            border-radius: 8px; 
        }

        footer { 
            background-color: var(--primary-purple); 
            color: white; 
            padding: 20px 0; 
            margin-top: 60px;
            font-size: 0.9rem;
        }

        @media (max-width: 991px) {
            .sidebar { width: 100%; position: relative; height: auto; top: 0; padding-top: 80px; }
            .main-content { margin-left: 0; margin-top: 0; }
        }
    </style>
</head>
<body>

<div id="chat-container" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999; font-family: 'Inter', sans-serif;">
    <button id="chat-toggle" class="btn-purple" style="width: 60px; height: 60px; border-radius: 50%; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.2); cursor: pointer;">
        <i class="fa-solid fa-comments" style="font-size: 1.5rem;"></i>
    </button>
    <div id="chat-window" style="display: none; width: 320px; background: white; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.2); position: absolute; bottom: 70px; right: 0; overflow: hidden;">
        <div style="background: var(--gradient-purple); color: white; padding: 15px; font-weight: bold; display: flex; justify-content: space-between; align-items: center;">
            <span><i class="fa-solid fa-headset me-2"></i> GAD Help Desk</span>
            <button id="close-chat" style="background: transparent; border: none; color: white; cursor: pointer;"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div id="chat-messages" style="height: 300px; padding: 15px; overflow-y: auto; background: #f9f9f9; display: flex; flex-direction: column; gap: 10px;">
            <small class="text-center text-muted">Welcome! How can we help you today?</small>
        </div>
        <form id="chat-form" style="padding: 10px; border-top: 1px solid #eee; display: flex; gap: 5px;">
            <input type="text" id="chat-input" placeholder="Type a message..." required style="flex: 1; border: 1px solid #ddd; border-radius: 20px; padding: 8px 15px; outline: none; font-size: 0.9rem;">
            <button type="submit" style="background: var(--primary-purple); color: white; border: none; width: 35px; height: 35px; border-radius: 50%;"><i class="fa-solid fa-paper-plane"></i></button>
        </form>
    </div>
</div>

<div class="modal fade" id="autoAnnouncementModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px; border: none; overflow: hidden;">
            <div class="modal-header bg-warning text-dark border-0">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-circle-exclamation me-2"></i> IMPORTANT NOTICE</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fa-solid fa-bullhorn mb-3 text-warning" style="font-size: 4rem;"></i>
                <h4 class="fw-bold">Welcome to GAD VPA!</h4>
                <p class="text-muted">Check out our Monthly Events for March 2026: The E-VAW Webinar Series. Stay informed and empowered!</p>
                <button type="button" class="btn btn-purple px-4" data-bs-dismiss="modal">Got it!</button>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/user_nav.php'; ?>

<div class="main-content">
    <div class="announcement-ticker shadow-sm">
        <div class="ticker-label"><i class="fa-solid fa-bullhorn me-2"></i> NEWS:</div>
        <div class="ticker-content">
            REMINDER: E-VAW Webinar Series will start this March 15, 2026. Register now! | GAD Office is open from 8:00 AM to 5:00 PM. | Welcome to the GAD VPA Safe Haven!
        </div>
    </div>

    <h2 class="text-center text-purple mb-4 mt-2"><i class="fa-solid fa-bullhorn me-2"></i> GAD Monthly Events</h2>
    <div id="gadCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="images/january.jpg" class="d-block w-100">
                <div class="carousel-caption d-none d-md-block" style="background: rgba(0,0,0,0.5); border-radius: 10px;">
                    <h3>January – GAD GAVE ME YOU</h3>
                </div>
            </div>
            <div class="carousel-item">
                <img src="images/february.jpg" class="d-block w-100">
                <div class="carousel-caption d-none d-md-block" style="background: rgba(0,0,0,0.5); border-radius: 10px;">
                    <h3>February – Anti-Violence Awareness Campaign</h3>
                </div>
            </div>
            <div class="carousel-item">
                <img src="images/march.jpg" class="d-block w-100">
                <div class="carousel-caption d-none d-md-block" style="background: rgba(0,0,0,0.5); border-radius: 10px;">
                    <h3>March – E-VAW Webinar Series</h3>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#gadCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
        <button class="carousel-control-next" type="button" data-bs-target="#gadCarousel" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
    </div>

    <h2 class="text-center text-purple my-5"><i class="fa-solid fa-newspaper me-2"></i> Featured Articles</h2>
    <div class="row g-4">
        <?php
        $query = "SELECT * FROM articles ORDER BY id DESC LIMIT 3";
        $result = mysqli_query($conn, $query);
        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
        ?>
            <div class="col-md-6 col-lg-4">
                <div class="card article-card h-100 shadow-sm d-flex flex-column">
                    <img src="<?php echo htmlspecialchars($row['image_path']); ?>" class="card-img-top article-img" onerror="this.src='https://via.placeholder.com/400x200?text=Article'">
                    <div class="card-body d-flex flex-column">
                        <span class="badge badge-gad mb-2 align-self-start"><?php echo htmlspecialchars($row['category']); ?></span>
                        <h5 class="fw-bold"><?php echo htmlspecialchars($row['title']); ?></h5>
                        <p class="text-muted small article-description"><?php echo htmlspecialchars($row['short_desc']); ?></p>
                        <div class="mt-auto">
                            <button class="btn btn-outline-purple btn-sm" data-bs-toggle="modal" data-bs-target="#modalArticle<?php echo $row['id']; ?>">Read More</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="modalArticle<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content" style="border-radius: 20px; overflow: hidden; border: none;">
                        <div class="modal-header" style="background: var(--gradient-purple); color: white;">
                            <h5 class="modal-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <img src="<?php echo htmlspecialchars($row['image_path']); ?>" class="img-fluid rounded mb-3" onerror="this.src='https://via.placeholder.com/800x400?text=Article'">
                            <div class="modal-body-content text-start p-3">
                                <?php echo nl2br(htmlspecialchars($row['full_content'])); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php 
            }
        } else {
            echo "<div class='col-12 text-center'><p class='text-muted'>No featured articles available at the moment.</p></div>";
        }
        ?>
    </div>

    <h2 class="text-center text-purple my-5"><i class="fa-solid fa-clipboard-list me-2"></i> GAD Accomplishment Reports</h2>
    <div class="row row-cols-1 row-cols-md-3 g-4 text-center">
        <div class="col">
            <div class="card h-100 shadow-sm p-4">
                <div class="card-body">
                    <i class="fa-solid fa-file-pdf card-report-icon"></i>
                    <h5 class="fw-bold">2025 Q1 Report</h5>
                    <button class="btn btn-purple w-100" data-bs-toggle="modal" data-bs-target="#modalQ1">View Report</button>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 shadow-sm p-4">
                <div class="card-body">
                    <i class="fa-solid fa-file-pdf card-report-icon"></i>
                    <h5 class="fw-bold">2025 Q2 Report</h5>
                    <button class="btn btn-purple w-100" data-bs-toggle="modal" data-bs-target="#modalQ2">View Report</button>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 shadow-sm p-4">
                <div class="card-body">
                    <i class="fa-solid fa-file-pdf card-report-icon"></i>
                    <h5 class="fw-bold">2025 Q3 Report</h5>
                    <button class="btn btn-purple w-100" data-bs-toggle="modal" data-bs-target="#modalQ3">View Report</button>
                </div>
            </div>
        </div>
    </div>

    <h2 class="text-center text-purple my-5"><i class="fa-solid fa-users-gear me-2"></i> GAD Officials</h2>
    <div class="row justify-content-center text-center g-4">
        <div class="col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm p-4">
                <img src="images/head_gad.jpg" class="card-img-top rounded-circle mx-auto" style="width:130px;height:130px;object-fit:cover; border: 4px solid #6f42c1;">
                <h6 class="fw-bold mt-3">Mr. Allan Evardo Jr.</h6>
                <p class="small text-muted mb-0">Head of GAD</p>
            </div>
        </div>
        <div class="col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm p-4">
                <img src="images/assistant.jpg" class="card-img-top rounded-circle mx-auto" style="width:130px;height:130px;object-fit:cover; border: 4px solid #6f42c1;">
                <h6 class="fw-bold mt-3">Mrs. Elizabeth E. Bravo</h6>
                <p class="small text-muted mb-0">Assistant Coordinator</p>
            </div>
        </div>
    </div>

    <div class="container mt-5 pt-4">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="row g-0 shadow-sm">
                    <div class="col-md-4">
                        <div class="footer-square-box" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#officePhotoModal">
                            <div class="footer-icon-circle"><i class="fa-solid fa-location-dot"></i></div>
                            <h6 class="fw-bold text-uppercase">Address</h6>
                            <p class="text-muted">Cavite State University - Naic Campus<br></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="footer-square-box border-start-0">
                            <div class="footer-icon-circle"><i class="fa-solid fa-phone"></i></div>
                            <h6 class="fw-bold text-uppercase">Call Us</h6>
                            <p class="text-muted">(046) 443 2546<br>+63 912 345 6789</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="footer-square-box border-start-0">
                            <div class="footer-icon-circle"><i class="fa-solid fa-envelope"></i></div>
                            <h6 class="fw-bold text-uppercase">Email Us</h6>
                            <p><a href="mailto:gad.naic@cvsu.edu.ph" class="text-decoration-none text-purple">gad.naic@cvsu.edu.ph</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <div class="container text-center">
            <span>© 2025 GAD VPA | Cavite State University - Naic Campus</span>
        </div>
    </footer>
</div>

<div class="modal fade" id="modalQ1" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Accomplishment Report: 2025 Quarter 1</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <h6 class="fw-bold text-purple">Activities Conducted:</h6>
                <ul>
                    <li><strong>January:</strong> GAD Awareness Seminar for Freshmen (250 participants).</li>
                    <li><strong>February:</strong> Distribution of Anti-VAWC Flyers in local barangays.</li>
                    <li><strong>March:</strong> Women's Month Kick-off Parade and Forum.</li>
                </ul>
                <hr>
                <h6 class="fw-bold text-purple">Key Achievements:</h6>
                <p>Successfully established the <em>Shield & Speak</em> digital portal prototype and integrated the incident reporting system with the Campus Security Office.</p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalQ2" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Accomplishment Report: 2025 Quarter 2</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <h6 class="fw-bold text-success">Activities Conducted:</h6>
                <ul>
                    <li><strong>April:</strong> Training of Faculty on Gender-Sensitive Teaching Strategies.</li>
                    <li><strong>May:</strong> Mental Health and Gender Diversity Workshop.</li>
                    <li><strong>June:</strong> Pride Month Celebration and SOGIE 101 Webinar.</li>
                </ul>
                <hr>
                <h6 class="fw-bold text-success">Key Achievements:</h6>
                <p>95% of faculty members attended the sensitivity training. Launched the anonymous feedback feature for students.</p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalQ3" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Accomplishment Report: 2025 Quarter 3</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <h6 class="fw-bold text-info">Activities Conducted:</h6>
                <ul>
                    <li><strong>July:</strong> GAD Curriculum Integration Review.</li>
                    <li><strong>August:</strong> Anti-Sexual Harassment (Safe Spaces Act) Orientation for Student Leaders.</li>
                    <li><strong>September:</strong> Budget Planning for 2026 GAD Projects.</li>
                </ul>
                <hr>
                <h6 class="fw-bold text-info">Key Achievements:</h6>
                <p>Updated the campus handbook to include specific protocols for online harassment cases.</p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="officePhotoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 15px; overflow: hidden; border: none;">
            <div class="modal-header" style="background-color: #6f42c1; color: white;">
                <h5 class="modal-title"><i class="fa-solid fa-building me-2"></i> GAD Office Location</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 text-center">
                <img src="images/office_location.jpg" class="img-fluid w-100">
                <div class="p-3 bg-light">
                    <p class="mb-0 text-muted small">The GAD Office is conveniently located across from the Library Building, adjacent to the Cooperative, and directly facing the Campus Gym.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    window.onload = () => {
        const myModal = new bootstrap.Modal('#autoAnnouncementModal');
        myModal.show();
    };

    document.addEventListener('DOMContentLoaded', function() {
        const chatToggle = document.getElementById('chat-toggle');
        const chatWindow = document.getElementById('chat-window');
        const closeChat = document.getElementById('close-chat');
        const chatForm = document.getElementById('chat-form');
        const chatInput = document.getElementById('chat-input');
        const chatMessages = document.getElementById('chat-messages');

        chatToggle.onclick = () => chatWindow.style.display = chatWindow.style.display === 'none' ? 'block' : 'none';
        closeChat.onclick = () => chatWindow.style.display = 'none';

        function loadMessages() {
            fetch('fetch_messages.php')
                .then(response => response.text())
                .then(data => {
                    chatMessages.innerHTML = data;
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                });
        }

        setInterval(loadMessages, 3000);

        chatForm.onsubmit = (e) => {
            e.preventDefault();
            const msg = chatInput.value;
            if(!msg.trim()) return;
            const formData = new FormData();
            formData.append('message', msg);
            fetch('send_message.php', {
                method: 'POST',
                body: formData
            }).then(() => {
                chatInput.value = '';
                loadMessages();
            });
        };
    });
</script>

</body>
</html>