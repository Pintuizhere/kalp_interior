<?php
session_start();
require_once 'admin/config/db.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$qty = isset($_GET['qty']) ? (int)$_GET['qty'] : 1;

if (!isset($_SESSION['shop_cart'])) {
    $_SESSION['shop_cart'] = [];
}

if ($action == 'add' && $id > 0) {
    // Add to cart
    if (isset($_SESSION['shop_cart'][$id])) {
        $_SESSION['shop_cart'][$id] += $qty;
    } else {
        $_SESSION['shop_cart'][$id] = $qty;
    }
    // Stay on the shop page or redirect back to checkout
    if (isset($_GET['redirect']) && $_GET['redirect'] == 'checkout') {
        header("Location: checkout.php");
    } else {
        header("Location: shop-furniture.php#products");
    }
    exit;
} elseif ($action == 'remove' && $id > 0) {
    if (isset($_SESSION['shop_cart'][$id])) {
        unset($_SESSION['shop_cart'][$id]);
    }
    header("Location: checkout.php");
    exit;
} elseif ($action == 'clear') {
    $_SESSION['shop_cart'] = [];
    header("Location: shop-furniture.php");
    exit;
} else {
    header("Location: shop-furniture.php");
    exit;
}
?>
