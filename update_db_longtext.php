<?php
require 'admin/config/db.php';
$conn->query("ALTER TABLE page_content MODIFY content_value LONGTEXT");
echo $conn->error;
echo "Done";
?>
