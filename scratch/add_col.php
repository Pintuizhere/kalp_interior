<?php
require_once 'admin/config/db.php';
$conn->query("ALTER TABLE calc_addons ADD COLUMN value_type ENUM('percent', 'fixed') DEFAULT 'percent' AFTER name");
echo "Column added successfully or already exists.";
?>
