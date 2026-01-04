<?php
// Handle create / update product

require_once __DIR__ . '/../../db.php';

$id             = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$product_name   = trim($_POST['product_name'] ?? '');
$category_id    = (int)($_POST['category_id'] ?? 0);
$price          = (float)($_POST['price'] ?? 0);
$stock_quantity = (int)($_POST['stock_quantity'] ?? 0);

// Validation
if (empty($product_name)) {
    header('Location: index.php?module=products&action=list&error=' . urlencode('Product name is required'));
    exit;
}

if (strlen($product_name) > 255) {
    header('Location: index.php?module=products&action=list&error=' . urlencode('Product name must be 255 characters or less'));
    exit;
}

if ($category_id <= 0) {
    header('Location: index.php?module=products&action=list&error=' . urlencode('Please select a category'));
    exit;
}

// Verify category exists
$checkStmt = mysqli_prepare($conn, "SELECT id FROM categories WHERE id = ?");
mysqli_stmt_bind_param($checkStmt, 'i', $category_id);
mysqli_stmt_execute($checkStmt);
$result = mysqli_stmt_get_result($checkStmt);
if (!mysqli_fetch_assoc($result)) {
    mysqli_stmt_close($checkStmt);
    header('Location: index.php?module=products&action=list&error=' . urlencode('Invalid category selected'));
    exit;
}
mysqli_stmt_close($checkStmt);

if ($price < 0) {
    header('Location: index.php?module=products&action=list&error=' . urlencode('Price cannot be negative'));
    exit;
}

if ($stock_quantity < 0) {
    header('Location: index.php?module=products&action=list&error=' . urlencode('Stock quantity cannot be negative'));
    exit;
}

$success = false;
if ($id > 0) {
    // Verify product exists
    $checkStmt = mysqli_prepare($conn, "SELECT id FROM products WHERE id = ?");
    mysqli_stmt_bind_param($checkStmt, 'i', $id);
    mysqli_stmt_execute($checkStmt);
    $result = mysqli_stmt_get_result($checkStmt);
    if (!mysqli_fetch_assoc($result)) {
        mysqli_stmt_close($checkStmt);
        header('Location: index.php?module=products&action=list&error=' . urlencode('Product not found'));
        exit;
    }
    mysqli_stmt_close($checkStmt);

    $stmt = mysqli_prepare(
        $conn,
        "UPDATE products SET product_name = ?, category_id = ?, price = ?, stock_quantity = ? WHERE id = ?"
    );
    mysqli_stmt_bind_param($stmt, 'siddi', $product_name, $category_id, $price, $stock_quantity, $id);
    if (mysqli_stmt_execute($stmt)) {
        $success = true;
    }
    mysqli_stmt_close($stmt);
} else {
    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO products (product_name, category_id, price, stock_quantity) VALUES (?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, 'sidd', $product_name, $category_id, $price, $stock_quantity);
    if (mysqli_stmt_execute($stmt)) {
        $success = true;
    }
    mysqli_stmt_close($stmt);
}

if ($success) {
    $message = $id > 0 ? 'Product updated successfully!' : 'Product created successfully!';
    header('Location: index.php?module=products&action=list&success=' . urlencode($message));
} else {
    header('Location: index.php?module=products&action=list&error=' . urlencode('Failed to save product. Please try again.'));
}
exit;
