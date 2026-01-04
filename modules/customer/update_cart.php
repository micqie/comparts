<?php
// Update cart item quantity
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
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

// Validation
if ($index < 0 || $index >= count($_SESSION['cart'])) {
    header('Location: index.php?module=customer&action=cart&error=' . urlencode('Invalid item selected'));
    exit;
}

if ($quantity <= 0) {
    header('Location: index.php?module=customer&action=cart&error=' . urlencode('Quantity must be greater than 0'));
    exit;
}

// Check stock availability
require_once __DIR__ . '/../../db.php';
$product_id = $_SESSION['cart'][$index]['product_id'];
$stmt = mysqli_prepare($conn, "SELECT product_name, stock_quantity FROM products WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$product) {
    header('Location: index.php?module=customer&action=cart&error=' . urlencode('Product not found'));
    exit;
}

$availableStock = (int)$product['stock_quantity'];
if ($availableStock < $quantity) {
    $productName = htmlspecialchars($product['product_name']);
    header('Location: index.php?module=customer&action=cart&error=' . urlencode("Insufficient stock for '{$productName}'. Available: {$availableStock}, Requested: {$quantity}"));
    exit;
}

$_SESSION['cart'][$index]['quantity'] = $quantity;
$productName = htmlspecialchars($product['product_name']);
header('Location: index.php?module=customer&action=cart&success=' . urlencode("Quantity updated for '{$productName}'!"));
exit;
