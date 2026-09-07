<?php
require 'admin/config/db.php';
$res = $conn->query('SHOW COLUMNS FROM calculator_settings');
while($row = $res->fetch_assoc()) {
    print_r($row);
}
