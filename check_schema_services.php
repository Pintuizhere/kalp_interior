<?php
require 'admin/config/db.php';
$res = $conn->query("DESCRIBE services");
while($row = $res->fetch_assoc()) {
    print_r($row);
}
?>
