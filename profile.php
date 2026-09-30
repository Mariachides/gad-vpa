<?php
session_start();

// Siguraduhin na naka-login ang user
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Dito mo pwedeng i-include ang iyong DB connection para kuhanin ang ibang details
// include 'db_connection.php'; 

$activePage = 'profile.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | GAD VPA</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .profile-container {
            margin-top: 50px;
            margin-bottom: 50px;
        }

        .card-profile {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .profile-header {
            background: linear-gradient(135deg, #6f42c1 0%, #5a359a 100%);
            padding: 60px 20px;
            text-align: center;
            color: white;
        }

        .profile-img-container {
            position: relative;
            display: inline-block;
        }

        .profile-img {
            width: 130px;
            height: 130px;
            object-fit: cover;
            border: 5px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            background-color: white;
        }

        .edit-badge {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background: #ffd700;
            color: #333;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid white;
            cursor: pointer;
            transition: 0.3s;
        }

        .edit-badge:hover {
            transform: scale(1.1);
            background: #ffc107;
        }

        .profile-body {
            padding: 40px;
            background: white;
        }

        .info-label {
            color: #6c757d;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        .info-value {
            color: #333;
            font-size: 1.1rem;
            font-weight: 500;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        .btn-purple {
            background-color: #6f42c1;
            color: white;
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-purple:hover {
            background-color: #5a359a;
            color: white;
            box-shadow: 0 5px 15px rgba(111, 66, 193, 0.3);
        }

        .status-pill {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            background: #e8f5e9;
            color: #2e7d32;
        }
    </style>
</head>
<body>

<?php include 'includes/user_nav.php'; ?>

<div class="main-content">
<div class="container profile-container">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            
            <a href="index.php" class="btn text-muted mb-3 p-0">
                <i class="fa-solid fa-arrow-left me-2"></i> Back to Dashboard
            </a>

            <div class="card card-profile">
                <div class="profile-header">
                    <div class="profile-img-container mb-3">
                        <i class="fa-solid fa-user-circle fa-7x"></i> <div class="edit-badge" title="Change Photo">
                            <i class="fa-solid fa-camera"></i>
                        </div>
                    </div>
                    <h3 class="mb-0 fw-bold"><?php echo htmlspecialchars($_SESSION['username']); ?></h3>
                    <p class="opacity-75 mb-0">Student / User</p>
                </div>

                <div class="profile-body">
                    <div class="row">
                        <div class="col-12">
                            <span class="info-label">Account Status</span>
                            <div class="info-value">
                                <span class="status-pill"><i class="fa-solid fa-check-circle me-1"></i> Active</span>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <span class="info-label">Username</span>
                            <div class="info-value"><?php echo htmlspecialchars($_SESSION['username']); ?></div>
                        </div>

                        <div class="col-12">
                            <span class="info-label">Full Name</span>
                            <div class="info-value text-muted">Not Set</div>
                        </div>

                        <div class="col-12">
                            <span class="info-label">School / Department</span>
                            <div class="info-value text-muted">Gender and Development VPA</div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button class="btn btn-purple w-100 mb-2">
                            <i class="fa-solid fa-user-pen me-2"></i> Edit My Details
                        </button>
                        <a href="logout.php" class="btn btn-outline-danger btn-sm border-0">
                            <i class="fa-solid fa-right-from-bracket me-1"></i> Logout from this account
                        </a>
                    </div>
                </div>
            </div>

            <p class="text-center mt-4 text-muted small">
                © 2025 GAD VPA Platform • Secure Access
            </p>

        </div>
    </div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>