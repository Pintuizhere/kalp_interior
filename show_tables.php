<?php
require 'admin/config/db.php';
$res = $conn->query("DESCRIBE projects");
while($row = $res->fetch_assoc()) {
    echo $row['Field'] . ' - ' . $row['Type'] . "\n";
}
