<?php
// Update cart item quantity
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
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

if ($index >= 0 && $index < count($_SESSION['cart']) && $quantity > 0) {
    // Check stock availability
    require_once __DIR__ . '/../../db.php';
    $product_id = $_SESSION['cart'][$index]['product_id'];
    $stmt = mysqli_prepare($conn, "SELECT stock_quantity FROM products WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($product && (int)$product['stock_quantity'] >= $quantity) {
        $_SESSION['cart'][$index]['quantity'] = $quantity;
    }
}

header('Location: index.php?module=customer&action=cart');
exit;

