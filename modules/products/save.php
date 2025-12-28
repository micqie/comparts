<?php
// Handle create / update product

require_once __DIR__ . '/../../db.php';

$id             = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$product_name   = trim($_POST['product_name'] ?? '');
$category_id    = (int)($_POST['category_id'] ?? 0);
$price          = (float)($_POST['price'] ?? 0);
$stock_quantity = (int)($_POST['stock_quantity'] ?? 0);

if ($product_name === '' || $category_id <= 0) {
    header('Location: index.php?module=products&action=list');
    exit;
}

if ($id > 0) {
    $stmt = mysqli_prepare(
        $conn,
        "UPDATE products SET product_name = ?, category_id = ?, price = ?, stock_quantity = ? WHERE id = ?"
    );
    mysqli_stmt_bind_param($stmt, 'siddi', $product_name, $category_id, $price, $stock_quantity, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
} else {
    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO products (product_name, category_id, price, stock_quantity) VALUES (?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, 'sidd', $product_name, $category_id, $price, $stock_quantity);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

header('Location: index.php?module=products&action=list');
exit;



