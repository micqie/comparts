<?php
// Delete product

require_once __DIR__ . '/../../db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: index.php?module=products&action=list&error=' . urlencode('Invalid product ID'));
    exit;
}

// Check if product exists and get name
$checkStmt = mysqli_prepare($conn, "SELECT product_name FROM products WHERE id = ?");
mysqli_stmt_bind_param($checkStmt, 'i', $id);
mysqli_stmt_execute($checkStmt);
$result = mysqli_stmt_get_result($checkStmt);
$product = mysqli_fetch_assoc($result);
mysqli_stmt_close($checkStmt);

if (!$product) {
    header('Location: index.php?module=products&action=list&error=' . urlencode('Product not found'));
    exit;
}

// Check if product is in any orders
$checkStmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM order_details WHERE product_id = ?");
mysqli_stmt_bind_param($checkStmt, 'i', $id);
mysqli_stmt_execute($checkStmt);
$result = mysqli_stmt_get_result($checkStmt);
$row = mysqli_fetch_assoc($result);
$orderCount = $row['count'];
mysqli_stmt_close($checkStmt);

// Check if product has inventory transactions
$checkStmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM inventory_transactions WHERE product_id = ?");
mysqli_stmt_bind_param($checkStmt, 'i', $id);
mysqli_stmt_execute($checkStmt);
$result = mysqli_stmt_get_result($checkStmt);
$row = mysqli_fetch_assoc($result);
$inventoryCount = $row['count'];
mysqli_stmt_close($checkStmt);

if ($orderCount > 0 || $inventoryCount > 0) {
    $errorMessage = 'Cannot delete product: ';
    $reasons = [];

    if ($orderCount > 0) {
        $reasons[] = "associated with {$orderCount} order item(s)";
    }
    if ($inventoryCount > 0) {
        $reasons[] = "has {$inventoryCount} inventory transaction(s)";
    }

    $errorMessage .= implode(' and ', $reasons);
    header('Location: index.php?module=products&action=list&error=' . urlencode($errorMessage));
    exit;
}

// Safe to delete
$stmt = mysqli_prepare($conn, "DELETE FROM products WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
if (mysqli_stmt_execute($stmt)) {
    $productName = htmlspecialchars($product['product_name']);
    header('Location: index.php?module=products&action=list&success=' . urlencode("Product '{$productName}' deleted successfully!"));
} else {
    // Log the actual error for debugging
    error_log("Delete product failed: " . mysqli_error($conn));
    header('Location: index.php?module=products&action=list&error=' . urlencode('Failed to delete product. Please try again.'));
}
mysqli_stmt_close($stmt);
exit;
