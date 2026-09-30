<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "gad_vpa";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database connection failed");
}
?>
