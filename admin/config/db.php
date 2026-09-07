<?php
$host = "localhost";
$username = "root";
$password = ""; // Default XAMPP password is empty
$database = "kalp_interior_db"; // Keep original database name

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Set UTF-8 charset
$conn->set_charset("utf8");
?>
