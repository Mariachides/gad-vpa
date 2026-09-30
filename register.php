<?php
include 'config.php';
session_start();

// 🔹 Enable error reporting for debugging (alisin ito kapag live na ang system)
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize inputs
    $first_name   = trim($_POST['first_name']);
    $last_name    = trim($_POST['last_name']);
    $username     = trim($_POST['username']);
    $email        = trim($_POST['email']);
    $phone        = trim($_POST['phone']);
    $password     = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];
    $role         = $_POST['role']; // 'user' or 'admin'

    // Password match check
    if ($password !== $confirm_pass) {
        echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
        exit();
    }

    // Set course_year and student_number: NULL for admin, or user input
    if ($role === 'admin') {
        $course_year    = NULL;
        $student_number = NULL;
    } else {
        $course_year    = trim($_POST['course_year']);
        $student_number = trim($_POST['student_number']);

        // Server-side check (huwag umasa sa HTML "required" lang)
        if ($student_number === '') {
            echo "<script>alert('Student number is required!'); window.history.back();</script>";
            exit();
        }
    }

    // Check if username exists
    $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username=?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    if (mysqli_stmt_num_rows($stmt) > 0) {
        echo "<script>alert('Username already exists!'); window.history.back();</script>";
        exit();
    }
    mysqli_stmt_close($stmt);

    // Check if student number already exists (para sa students lang)
    if ($student_number !== NULL) {
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE student_number=?");
        mysqli_stmt_bind_param($stmt, "s", $student_number);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            echo "<script>alert('Student number already registered!'); window.history.back();</script>";
            exit();
        }
        mysqli_stmt_close($stmt);
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into DB
    $insertQuery = "INSERT INTO users 
        (first_name, last_name, username, email, phone, course_year, student_number, password, role)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $insertQuery);
    mysqli_stmt_bind_param($stmt, "sssssssss", 
        $first_name, 
        $last_name, 
        $username, 
        $email, 
        $phone, 
        $course_year, 
        $student_number, 
        $hashedPassword, 
        $role
    );

    if (mysqli_stmt_execute($stmt)) {
        header("Location: login.php?registered=1");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>