<?php
require 'admin/config/db.php';
$res = $conn->query("SELECT setting_value FROM calculator_settings WHERE setting_key = 'pdf_template_html'");
if($row = $res->fetch_assoc()) {
    echo $row['setting_value'];
}
