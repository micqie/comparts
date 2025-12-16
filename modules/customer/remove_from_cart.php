<?php
// Remove item from cart
require_once __DIR__ . '/../../config/auth.php';

if (!isLoggedIn()) {
    header('Location: index.php?module=auth&action=login');
    exit;
}

if (!isset($_SESSION['cart'])) {
    header('Location: index.php?module=customer&action=cart');
    exit;
}

$index = isset($_POST['index']) ? (int)$_POST['index'] : -1;

if ($index >= 0 && $index < count($_SESSION['cart'])) {
    array_splice($_SESSION['cart'], $index, 1);
}

header('Location: index.php?module=customer&action=cart');
exit;
