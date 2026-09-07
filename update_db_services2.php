<?php
require 'admin/config/db.php';
$conn->query("ALTER TABLE services ADD slug VARCHAR(255) NULL AFTER name");
$conn->query("ALTER TABLE services ADD UNIQUE (slug)");
$conn->query("ALTER TABLE services ADD meta_title VARCHAR(255) NULL AFTER display_order");
$conn->query("ALTER TABLE services ADD meta_keywords VARCHAR(255) NULL AFTER meta_title");
$conn->query("ALTER TABLE services ADD meta_description TEXT NULL AFTER meta_keywords");
echo $conn->error;
echo "Done";
?>
