<?php
$conn = new mysqli('localhost', 'root', '', 'kalp_interior_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "CREATE TABLE IF NOT EXISTS testimonials (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(255) NOT NULL,
    client_role VARCHAR(255),
    company_name VARCHAR(255),
    company_icon VARCHAR(100),
    client_image VARCHAR(255),
    content TEXT NOT NULL,
    status ENUM('Published', 'Draft') DEFAULT 'Published',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table testimonials created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}
$conn->close();
?>
