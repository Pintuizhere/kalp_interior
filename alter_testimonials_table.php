<?php
$conn = new mysqli('localhost', 'root', '', 'kalp_interior_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "ALTER TABLE testimonials ADD COLUMN company_logo VARCHAR(255) AFTER company_name";
if ($conn->query($sql) === TRUE) {
    echo "Column added successfully";
} else {
    echo "Error: " . $conn->error;
}
$conn->close();
?>
