<?php
require_once 'admin/config/db.php';

$sql = "CREATE TABLE IF NOT EXISTS team_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    role VARCHAR(255) NOT NULL,
    image VARCHAR(255) NOT NULL,
    email VARCHAR(255) DEFAULT NULL,
    whatsapp VARCHAR(255) DEFAULT NULL,
    display_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'team_members' created successfully or already exists.\\n";
} else {
    echo "Error creating table: " . $conn->error . "\\n";
}

$conn->close();
?>
