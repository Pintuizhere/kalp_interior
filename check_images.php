<?php
require 'admin/config/db.php';
$res = $conn->query("SELECT title, cover_image FROM projects ORDER BY id DESC LIMIT 3");
while($row = $res->fetch_assoc()) {
    echo $row['title'] . ' => ' . $row['cover_image'] . "<br>\n";
}
?>
