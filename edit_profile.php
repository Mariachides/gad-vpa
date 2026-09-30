<?php
session_start();
// include 'db_connection.php'; // Siguraduhin na may connection ka sa DB

if (!isset($_SESSION['username'])) { header("Location: login.php"); exit(); }

// Dito natin kukunin ang current data sa DB para ipakita sa input fields
// $user = $_SESSION['username'];
// $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$user'");
// $row = mysqli_fetch_array($query);

$activePage = 'profile.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile | GAD VPA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .edit-card { max-width: 500px; margin: 50px auto; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .btn-purple { background-color: #6f42c1; color: white; }
    </style>
</head>
<body>

<?php include 'includes/user_nav.php'; ?>

<div class="main-content">
<div class="container">
    <div class="card edit-card p-4">
        <h4 class="text-center mb-4">Edit Your Profile</h4>
        
        <form action="update_logic.php" method="POST" enctype="multipart/form-data">
            
            <div class="mb-3 text-center">
                <label class="form-label d-block">Profile Picture</label>
                <input type="file" name="profile_image" class="form-control form-control-sm">
                <small class="text-muted">Choose a square image for best results.</small>
            </div>

            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="fullname" class="form-control" placeholder="Enter your full name">
            </div>

            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="example@email.com">
            </div>

            <div class="d-grid gap-2">
                <button type="submit" name="update_profile" class="btn btn-purple">Save Changes</button>
                <a href="profile.php" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
</div>
</div>

</body>
</html>