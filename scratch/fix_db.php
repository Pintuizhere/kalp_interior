<?php
require_once '../admin/config/db.php';
$conn->query("ALTER TABLE calc_addons MODIFY percent_value DECIMAL(12,2) NOT NULL DEFAULT 0.00");
$conn->query("ALTER TABLE calc_styles MODIFY percent_value DECIMAL(12,2) NOT NULL DEFAULT 0.00");
$conn->query("ALTER TABLE calc_breakdowns MODIFY percent_value DECIMAL(12,2) NOT NULL DEFAULT 0.00");
echo "Column modified successfully.";
?>
