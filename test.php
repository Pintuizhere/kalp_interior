<?php
require 'admin/config/db.php';
$res = $conn->query("SELECT setting_value FROM calculator_settings WHERE setting_key = 'pdf_template_html'");
if($res && $res->num_rows > 0){
    $row = $res->fetch_assoc();
    file_put_contents('test_output.txt', $row['setting_value']);
}
