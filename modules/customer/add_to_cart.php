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

// Validation
if ($product_id <= 0) {
    header('Location: index.php?module=customer&action=products&error=' . urlencode('Invalid product selected'));
    exit;
}

if ($quantity <= 0) {
    header('Location: index.php?module=customer&action=products&error=' . urlencode('Quantity must be greater than 0'));
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

if (!$product) {
    header('Location: index.php?module=customer&action=products&error=' . urlencode('Product not found'));
    exit;
}

$availableStock = (int)$product['stock_quantity'];
if ($availableStock <= 0) {
    header('Location: index.php?module=customer&action=products&error=' . urlencode('Product is out of stock'));
    exit;
}

// Check if product already in cart
$found = false;
$currentCartQty = 0;
foreach ($_SESSION['cart'] as &$item) {
    if ($item['product_id'] == $product_id) {
        $currentCartQty = $item['quantity'];
        $newQty = $item['quantity'] + $quantity;
        if ($newQty <= $availableStock) {
            $item['quantity'] = $newQty;
            $found = true;
        } else {
            header('Location: index.php?module=customer&action=products&error=' . urlencode('Insufficient stock. Available: ' . $availableStock . ', Requested: ' . $newQty));
            exit;
        }
        break;
    }
}

// Add new item if not found
if (!$found) {
    if ($quantity > $availableStock) {
        header('Location: index.php?module=customer&action=products&error=' . urlencode('Insufficient stock. Available: ' . $availableStock . ', Requested: ' . $quantity));
        exit;
    }
    $_SESSION['cart'][] = [
        'product_id' => $product_id,
        'product_name' => $product['product_name'],
        'price' => (float)$product['price'],
        'quantity' => $quantity
    ];
}

$productName = htmlspecialchars($product['product_name']);
$message = $found ? "Updated quantity for '{$productName}' in cart!" : "'{$productName}' added to cart successfully!";
header('Location: index.php?module=customer&action=cart&success=' . urlencode($message));
exit;
