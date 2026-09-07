<?php
require 'admin/config/db.php';
$res = $conn->query("SELECT content FROM news_offers ORDER BY id DESC LIMIT 1");
if ($res && $res->num_rows > 0) {
    print_r($res->fetch_assoc());
}
$res = $conn->query("SELECT content FROM blogs ORDER BY id DESC LIMIT 1");
if ($res && $res->num_rows > 0) {
    print_r($res->fetch_assoc());
}
?>
