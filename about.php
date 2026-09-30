<?php 
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$activePage = 'about.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About GAD VPA</title>

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
            color: #333;
            margin: 0;
        }

        /* TOP NAVBAR (MATCHING INDEX) */
        .navbar-custom {
            background: var(--gradient-purple);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 12px 0;
        }
        
        .navbar-brand {
            font-weight: 700 !important;
            letter-spacing: -0.5px;
            color: white !important;
        }

        .section-padding {
            padding: 80px 0;
        }

        .text-purple {
            color: var(--primary-purple);
        }

        .card-about {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: 0.3s;
            background: white;
            padding: 40px;
        }

        .icon-box {
            width: 80px;
            height: 80px;
            background: #f0ebf7;
            color: var(--primary-purple);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 20px;
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        /* FOOTER (MATCHING SCREENSHOT) */
        footer { 
            background-color: var(--primary-purple); 
            color: white; 
            padding: 20px 0; 
            font-size: 0.9rem;
        }

        h1, h2 {
            font-weight: 700;
            letter-spacing: -1px;
        }
    </style>
</head>

<body>

<?php include 'includes/user_nav.php'; ?>

<div class="main-content">
<section class="section-padding" style="background: white; border-bottom: 1px solid #eee;">
    <div class="container text-center">
        <h1 class="display-5 text-purple mb-3">Violence Prevention & Awareness</h1>
        <p class="lead text-muted mx-auto" style="max-width: 700px;">
            Learn more about our mission to foster a safe, supportive environment
            for gender equality and violence prevention at Cavite State University - Naic.
        </p>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card-about h-100">
                    <div class="icon-box">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h2 class="text-purple mb-3">Our Mission</h2>
                    <p class="text-secondary lh-lg">
                        To advance gender equality and development within GAD VPA, while preventing gender-based 
                        violence and delivering confidential support and resources to all stakeholders. We are dedicated to 
                        cultivating a secure environment where individuals can access assistance fearlessly, 
                        while building awareness and strengthening survivors via education and advocacy.
                    </p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card-about h-100">
                    <div class="icon-box">
                        <i class="bi bi-eye"></i>
                    </div>
                    <h2 class="text-purple mb-3">Our Vision</h2>
                    <p class="text-secondary lh-lg">
                        A thriving GAD VPA community where gender equality prevails, gender-based violence is proactively eliminated,
                        and every person feels secure, valued, and empowered to voice concerns,
                        obtain help, and utilize support services without fear or stigma.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="text-center mt-4">
    <div class="container">
        <span>© 2025 GAD VPA | Cavite State University - Naic Campus</span>
    </div>
</footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>