<?php
require 'admin/config/db.php';
$conn->query('ALTER TABLE leads ADD COLUMN is_cleared TINYINT(1) DEFAULT 0');
$conn->query('ALTER TABLE estimate_requests ADD COLUMN is_cleared TINYINT(1) DEFAULT 0');
echo 'Done';
