<?php
// Get product data as JSON for modal
header('Content-Type: application/json');

if (!isset($conn)) {
    require_once __DIR__ . '/../../db.php';
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $stmt = mysqli_prepare($conn, "SELECT id, product_name, category_id, price, stock_quantity FROM products WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($product) {
        echo json_encode($product);
        exit;
    }
}

echo json_encode(['error' => 'Product not found']);
