<?php
require 'admin/config/db.php';
$conn->query("ALTER TABLE services ADD slug VARCHAR(255) NULL AFTER title");
$conn->query("ALTER TABLE services ADD UNIQUE (slug)");
echo $conn->error;
echo "Done";
?>
