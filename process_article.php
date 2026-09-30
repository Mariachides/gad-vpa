<?php
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $category = $_POST['category'];
    $short_desc = mysqli_real_escape_string($conn, $_POST['short_desc']);
    $full_content = mysqli_real_escape_string($conn, $_POST['full_content']);
    
    // Handle Image Upload
    $target_dir = "images/";
    $image_name = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $image_name;
    move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);

    $sql = "INSERT INTO articles (title, category, image_path, short_desc, full_content) 
            VALUES ('$title', '$category', '$target_file', '$short_desc', '$full_content')";

    if (mysqli_query($conn, $sql)) {
        header("Location: admin_articles.php?success=1");
    }
}
?>