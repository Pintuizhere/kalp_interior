<?php
require 'admin/config/db.php';

$sql = "CREATE TABLE IF NOT EXISTS gallery_categories (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE,
    icon VARCHAR(255),
    order_index INT(11) DEFAULT 0
)";

if ($conn->query($sql) === TRUE) {
    echo "Table gallery_categories created successfully.\n";
    
    // Check if empty before inserting defaults
    $res = $conn->query("SELECT count(*) as count FROM gallery_categories");
    $row = $res->fetch_assoc();
    if ($row['count'] == 0) {
        $defaults = [
            ['Living Room', 'living-room', 'fa-solid fa-couch', 0],
            ['Bedroom', 'bedroom', 'fa-solid fa-bed', 1],
            ['Dining', 'dining', 'fa-solid fa-utensils', 2],
            ['Bathroom', 'bathroom', 'fa-solid fa-bath', 3],
            ['Other Spaces', 'other-spaces', 'fa-solid fa-door-open', 4]
        ];
        
        $stmt = $conn->prepare("INSERT INTO gallery_categories (name, slug, icon, order_index) VALUES (?, ?, ?, ?)");
        foreach ($defaults as $cat) {
            $stmt->bind_param("sssi", $cat[0], $cat[1], $cat[2], $cat[3]);
            $stmt->execute();
        }
        echo "Default gallery categories inserted.\n";
    } else {
        echo "Table already has data, skipping default insert.\n";
    }
} else {
    echo "Error creating table: " . $conn->error . "\n";
}
?>
