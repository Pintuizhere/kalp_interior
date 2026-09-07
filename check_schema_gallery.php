<?php
require 'admin/config/db.php';
$res = $conn->query("DESCRIBE project_categories");
while($row = $res->fetch_assoc()) {
    print_r($row);
}
?>
