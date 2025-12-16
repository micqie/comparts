<?php
// Add product to cart
require_once __DIR__ . '/../../config/auth.php';

if (!isLoggedIn()) {
    header('Location: index.php?module=auth&action=login');
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

if ($product_id <= 0 || $quantity <= 0) {
    header('Location: index.php?module=customer&action=products');
    exit;
}

// Get product info
require_once __DIR__ . '/../../db.php';
$stmt = mysqli_prepare($conn, "SELECT id, product_name, price, stock_quantity FROM products WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$product || (int)$product['stock_quantity'] < $quantity) {
    header('Location: index.php?module=customer&action=products&error=' . urlencode('Insufficient stock'));
    exit;
}

// Check if product already in cart
$found = false;
foreach ($_SESSION['cart'] as &$item) {
    if ($item['product_id'] == $product_id) {
        $newQty = $item['quantity'] + $quantity;
        if ($newQty <= (int)$product['stock_quantity']) {
            $item['quantity'] = $newQty;
            $found = true;
        }
        break;
    }
}

// Add new item if not found
if (!$found) {
    $_SESSION['cart'][] = [
        'product_id' => $product_id,
        'product_name' => $product['product_name'],
        'price' => (float)$product['price'],
        'quantity' => $quantity
    ];
}

header('Location: index.php?module=customer&action=cart');
exit;
