<?php
require 'admin/config/db.php';
$res = $conn->query("DESCRIBE page_content");
while($r = $res->fetch_assoc()) {
    print_r($r);
}
?>
