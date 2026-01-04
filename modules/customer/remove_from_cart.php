<?php
// Remove item from cart
require_once __DIR__ . '/../../config/auth.php';

if (!isLoggedIn()) {
    header('Location: index.php?module=auth&action=login');
    exit;
}

if (!isset($_SESSION['cart'])) {
    header('Location: index.php?module=customer&action=cart&error=' . urlencode('Cart is empty'));
    exit;
}

$index = isset($_POST['index']) ? (int)$_POST['index'] : -1;

if ($index < 0 || $index >= count($_SESSION['cart'])) {
    header('Location: index.php?module=customer&action=cart&error=' . urlencode('Invalid item selected'));
    exit;
}

$removedItem = $_SESSION['cart'][$index];
array_splice($_SESSION['cart'], $index, 1);

$productName = htmlspecialchars($removedItem['product_name']);
header('Location: index.php?module=customer&action=cart&success=' . urlencode("'{$productName}' removed from cart!"));
exit;
