<?php
require 'admin/config/db.php';
$conn->query("ALTER TABLE projects ADD slug VARCHAR(255) NULL AFTER title");
$conn->query("ALTER TABLE projects ADD UNIQUE (slug)");
echo $conn->error;
echo "Done";
?>
